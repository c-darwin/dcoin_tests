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

	$type = ParseData::findType('admin_spots');
	$time = time();
	$user_id =  1;
	$stend_id = 1;
	$example_spots = '{"face":{"1":["121","273","","0","0"],"2":["240","273",["axes_direction","x_line","y_center"],"1","2"],"3":["36","273","","1","2"],"4":["313","274","","1","2"],"5":["180","363","","0","0"],"6":["180","423","","1","7"],"7":["180","479",["draw_angle_left_bottom"],"3","7"],"8":["86","430",["draw_angle_right_bottom"],"7","4"],"9":["256","437","","3","11"]},"profile":{"1":["286","224","","1","2"],"2":["148","218",["axes_direction","x_line"],"1","2"],"3":["168","303","","0","0"],"4":["126","243","","0","0"],"5":["325","266","","0","0"],"6":["302","355","","0","0"]}}';
	$segments = '{"face":{"1":["3","4"],"2":["1","5"],"3":["5","6"],"4":["6","7"],"5":["8","9"]},"profile":{"1":["5","6"],"2":["1","5"],"3":["6","3"],"4":["2","4"]}}';
	$tolerances = '{"face":{"1":"2","2":"3","3":"4","4":"5","5":"4","6":"4","7":"4","8":"4","9":"4","10":"4"},"profile":{"1":"2","2":"3","3":"5","4":"6"}}';
	$compatibility = '[1,2]';

	$data_for_sign = "{$type},{$time},{$user_id},{$example_spots},{$segments},{$tolerances},{$compatibility}";
	debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	$signature = encrypt_and_sign($user_id, $admin_encrypt_private_key, $data_for_sign);

	$data = dec_binary ($type, 1) .
		dec_binary ($time, 4) .
		ParseData::encode_length_plus_data($user_id) .
		ParseData::encode_length_plus_data($example_spots) .
		ParseData::encode_length_plus_data($segments) .
		ParseData::encode_length_plus_data($tolerances) .
		ParseData::encode_length_plus_data($compatibility) .
		ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

	wait_tx_gen($db_admin);
	insert_tx($data, $db_admin);
	get_sleep();

} while(true);

?>
