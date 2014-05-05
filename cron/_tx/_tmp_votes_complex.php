<?php
define( 'DC', true );
define( 'ABSPATH', dirname(dirname(dirname(__FILE__))) . '/' );
require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'includes/class-parsedata.php' );

require_once( ABSPATH . 'phpseclib/Math/BigInteger.php');
require_once( ABSPATH . 'phpseclib/Crypt/Random.php');
require_once( ABSPATH . 'phpseclib/Crypt/Hash.php');
require_once( ABSPATH . 'phpseclib/Crypt/RSA.php');
require_once( ABSPATH . 'phpseclib/Crypt/AES.php');

require_once( ABSPATH . 'cron/_tx/_tmp_main_fns.php');


do {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	upd_deamon_time($db_admin);
	$admin_encrypt_private_key = base64_decode( get_miner_private_key($db_admin));

	// просто проходимся по всем стендам
	foreach ($db_stends as $user_id=>$data) {

		// работаем с БД стендом
		$db = $data['db_link'];
		$stend_id = $data['stend_id'];
		$encrypt_private_key = base64_decode(get_miner_private_key($db));

		$complex_data = array();
		$res1 = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `currency_id`
				FROM `".DB_PREFIX."promised_amount`
				WHERE `status` IN ('mining', 'repaid') AND
							 `user_id` = {$user_id} AND
							 `start_time` < ".(time()-90)."
				GROUP BY `currency_id`
				");
		while ($row = $db_admin->fetchArray($res1)) {
			$complex_data[$row['currency_id']][0] = 0.0000000760368; // user pct
			$complex_data[$row['currency_id']][1] = 0.0000000760368; // miner pct
			$complex_data[$row['currency_id']][2] = 75000; // max promise amount
			$complex_data[$row['currency_id']][3] = 3; // max other currency
			$complex_data[$row['currency_id']][4] = 0; // reduction
		}
		if (!$complex_data) {
			sleep(5);
			continue;
		}

		$json_data = json_encode($complex_data);
		$type = ParseData::findType('votes_complex');
		$time = time();
		$data_for_sign = "{$type},{$time},{$user_id},{$json_data}";
		debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

		$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);

		$data = dec_binary ($type, 1) .
			dec_binary ($time, 4) .
			ParseData::encode_length_plus_data($user_id) .
			ParseData::encode_length_plus_data($json_data) .
			ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

        wait_tx_gen($db_admin);
		insert_tx($data, $db);
	}
	get_sleep();

} while(true);

?>
