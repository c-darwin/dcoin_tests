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

		$my_miner_id = get_my_miner_id($db);
		if (!$my_miner_id)
			continue;

		$complex_data = array();
		$res = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT *
				FROM `".DB_PREFIX."promised_amount`
				WHERE `status` IN ('mining', 'repaid') AND
				           `user_id` = {$user_id}
				");
        while ( $row = $db->fetchArray( $res ) ) {

			$type = ParseData::findType('mining');
			$time = time();
            $promised_amount_id = $row['id'];

            if ($row['status']=='mining')
                $amount = calc_profit_($row['currency_id'], $row['amount']+$row['tdc_amount'], $user_id, $db, $row['tdc_amount_update'], time(), 'mining');
            else if ($row['status']=='repaid')
                $amount = calc_profit_($row['currency_id'], $row['tdc_amount'], $user_id, $db, $row['tdc_amount_update'], time(), 'repaid');

            if ($amount<0.02)
                continue;

            $data_for_sign = "{$type},{$time},{$user_id},{$promised_amount_id},{$amount}";
			debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);

			$data = dec_binary ($type, 1) .
				dec_binary ($time, 4) .
				ParseData::encode_length_plus_data($user_id) .
				ParseData::encode_length_plus_data($promised_amount_id) .
				ParseData::encode_length_plus_data($amount) .
				ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

            wait_tx_gen($db_admin);
			insert_tx($data, $db);

		}
	}
	get_sleep();

} while(true);

?>
