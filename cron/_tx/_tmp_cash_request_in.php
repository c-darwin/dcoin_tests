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

		$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT *
				FROM `".DB_PREFIX."cash_requests`
				WHERE  `status` = 'pending' AND
				            `to_user_id` = {$user_id}
				");
        while ( $row = $db->fetchArray( $res ) ) {

			$type = ParseData::findType('cash_request_in');
			$time = time();
            $cash_request_id = $row['id'];
            $code = '1';

            $data_for_sign = "{$type},{$time},{$user_id},{$cash_request_id},{$code}";
			debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);

			$data = dec_binary ($type, 1) .
				dec_binary ($time, 4) .
				ParseData::encode_length_plus_data($user_id) .
                ParseData::encode_length_plus_data($cash_request_id) .
                ParseData::encode_length_plus_data($code) .
				ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

            wait_tx_gen($db_admin);
			insert_tx($data, $db);

		}
	}
	get_sleep();

} while(true);

?>
