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

	require_once( ABSPATH . 'tmp/variables.php' );
	$variables_array[sizeof($variables_array)-1] = array('sleep', '{"is_ready":[2,3,4,5,6,7,8,9,10,11,12,13,14,15],"generator":[2,3,4,5,6,7,8,9,10,11,12,13,14,15]}');

	// админом изменяем переменные
	$type = ParseData::findType('admin_variables');
	$time = time();
	$user_id =  1;
	$variables =  json_encode($variables_array);
	$stend_id = 1;

	$data_for_sign = "{$type},{$time},{$user_id},{$variables}";
	debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	$signature = encrypt_and_sign($user_id, $admin_encrypt_private_key, $data_for_sign);

	$data = dec_binary ($type, 1) .
		dec_binary ($time, 4) .
		ParseData::encode_length_plus_data($user_id) .
		ParseData::encode_length_plus_data($variables) .
		ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

	wait_tx_gen($db_admin);
	insert_tx($data, $db_admin);
	get_sleep();

} while(true);

?>
