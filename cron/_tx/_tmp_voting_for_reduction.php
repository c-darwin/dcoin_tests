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

$pct = 10;

//do {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	upd_deamon_time($db_admin);

	// просто проходимся по всем стендам
	foreach ($db_stends as $user_id=>$data) {

		// работаем с БД стендом
		$db = $data['db_link'];
		$stend_id = $data['stend_id'];
		$encrypt_private_key = base64_decode(get_miner_private_key($db));

		// получим все валюты, которые у нас есть по банкнотам
		$currency_list = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `currency_id`
				FROM `".DB_PREFIX."banknotes`
				WHERE `user_id` = {$user_id} AND
							 `delete` = 0
				GROUP BY `currency_id`
				", 'array');

		foreach($currency_list as $currency_id) {

			$type = ParseData::findType('votes_reduction');
			$time = time();

			$data_for_sign = "{$type},{$time},{$user_id},{$currency_id},{$pct}";
			debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);
			debug_print("signature={$signature}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			$data = dec_binary ($type, 1) .
				dec_binary ($time, 4) .
				encode_length(strlen($user_id)) . $user_id .
				encode_length(strlen($currency_id)) . $currency_id .
				encode_length(strlen($pct)) . $pct .
				encode_length(strlen($signature)) . $signature;

            wait_tx_gen($db_admin);
			insert_tx($data, $db);
		}
	}
	//sleep($argv[1]);

//} while(true);

?>
