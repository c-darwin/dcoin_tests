<?php
session_start();

define( 'DC', TRUE);

define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

set_time_limit(0);

//require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );
require_once( ABSPATH . 'includes/class-parsedata.php' );
require_once( ABSPATH . 'phpseclib/Math/BigInteger.php');
require_once( ABSPATH . 'phpseclib/Crypt/Random.php');
require_once( ABSPATH . 'phpseclib/Crypt/Hash.php');
require_once( ABSPATH . 'phpseclib/Crypt/RSA.php');
require_once( ABSPATH . 'phpseclib/Crypt/AES.php');

require_once( ABSPATH . 'tmp/_fns.php' );

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$hashes_start = all_hashes();

// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType('votes_complex');
// time
$transaction_array[2] = time();
// user_id
$transaction_array[3] = 1;
// json data
$transaction_array[4] = '{"currency":{"1":[0.0000000760368,0.0000000760368,1,0,0]},"referral":{"first":"29","second":"6","third":"13"}}';
// sign
$transaction_array[5] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 100000;
$block_data['time'] = time();

$parsedata = new ParseData('', $db);
$parsedata->transaction_array = $transaction_array;
$parsedata->block_data = $block_data;
$parsedata->votes_complex_init();
//$error = $parsedata->new_reduction_front();
//if ($error) print $error;
//else {
	$parsedata->votes_complex();
	$parsedata->votes_complex_rollback();
	//$error = $parsedata->new_reduction_rollback_front();
//}

$hashes_end = all_hashes();
foreach ($hashes_end as $table=>$hash) {
	if ($hash!=$hashes_start[$table]) {
		debug_print('ERROR in '.$table, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		print 'ERROR in '.$table;
	}
}

?>