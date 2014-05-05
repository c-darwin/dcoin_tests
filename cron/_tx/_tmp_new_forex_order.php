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

function _tmp_new_fx_order($amount, $commission, $buy_currency_id) {

	global $db, $db_admin, $stend_id, $wallet, $encrypt_private_key;

	print '$stend_id='.$stend_id."\n";
	print_r($wallet);
	print '$encrypt_private_key='.$encrypt_private_key."\n";

	$type = ParseData::findType('new_forex_order');
	$time = time();
	$user_id =  $wallet['user_id'];
	$sell_currency_id = $wallet['currency_id'];
	$sell_rate = rand(1, 3);

	$data_for_sign = "{$type},{$time},{$user_id},{$sell_currency_id},{$sell_rate},{$amount},{$buy_currency_id},{$commission}";

	debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);

	debug_print("signature={$signature}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	$data = dec_binary ($type, 1) .
		dec_binary ($time, 4) .
		ParseData::encode_length_plus_data($user_id) .
		ParseData::encode_length_plus_data($sell_currency_id) .
		ParseData::encode_length_plus_data($sell_rate) .
		ParseData::encode_length_plus_data($amount) .
		ParseData::encode_length_plus_data($buy_currency_id) .
		ParseData::encode_length_plus_data($commission) .
		ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

    wait_tx_gen($db_admin);
	insert_tx($data, $db);
}

do {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];

	upd_deamon_time($db_admin);

	// шлем DC от случайного юзера с его случайного кошелька, где есть FC
	$wallet = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT *
			FROM `".DB_PREFIX."wallets`
			WHERE  `amount` > 0
			ORDER BY RAND()
			LIMIT 1
			", 'fetch_array' );

	if ($wallet) {

		if(!isset($db_stends[$wallet['user_id']])) {
			sleep(1);
			continue;
		}

		// работаем с БД стендом
		$db = $db_stends[$wallet['user_id']]['db_link'];
		$stend_id = $db_stends[$wallet['user_id']]['stend_id'];

		$encrypt_private_key = base64_decode( get_miner_private_key($db));
		if (!$encrypt_private_key) {
			sleep(10);
			continue;
		}
		$min_commssion = 0.01;

		$total_amount = $wallet['amount'] /*+ calc_profit_($wallet['currency_id'], $wallet['amount'], $wallet['user_id'], $db, $wallet['last_update'], time(), 'wallet')*/;

		/*// пишем  админскую таблу, чтобы удобнее читать логи
		$db = $db_admin;*/

		$need_currency = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `currency_id`
				FROM `".DB_PREFIX."wallets`
				WHERE  `amount` > 0
				ORDER BY RAND()
				LIMIT 1
				", 'fetch_one' );

		debug_print("need_currency={$need_currency}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

		if ($need_currency>0 && $need_currency!=$wallet['currency_id']) {
			$sum_1 = rand(0, $total_amount/100);
			//$node_commission_1 = ParseData::calc_node_commission($sum_1,array(0.1, $min_commssion, 0), $db);
			$node_commission_1 = 0.1;
			if ($sum_1>0) _tmp_new_fx_order($sum_1, $node_commission_1, $need_currency);
		}

	}
	get_sleep();

} while(true);

?>
