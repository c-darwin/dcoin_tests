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

// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType('mining');
// time
$transaction_array[2] = time();
// user_id
$transaction_array[3] = 22;
// promised_amount_id
$transaction_array[4] = '1';
// amount
$transaction_array[5] = '0.1';
// sign
$transaction_array[6] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 77952;
$block_data['time'] = time();

$hash1 = hash_table_data($db, 'points_status').hash_table_data($db, 'points').hash_table_data($db, 'promised_amount').hash_table_data($db, 'log_promised_amount').hash_table_data($db, 'wallets').hash_table_data($db, 'log_wallets').hash_table_data($db, 'my_dc_transactions');

$parsedata = new ParseData('', $db);
$parsedata->transaction_array = $transaction_array;
$parsedata->block_data = $block_data;
$parsedata->mining_init();
//$error = $parsedata->new_reduction_front();
//if ($error) print $error;
//else {
	$parsedata->mining();
	$parsedata->mining_rollback();
	//$error = $parsedata->new_reduction_rollback_front();
//}

$hash2 = hash_table_data($db, 'points_status').hash_table_data($db, 'points').hash_table_data($db, 'promised_amount').hash_table_data($db, 'log_promised_amount').hash_table_data($db, 'wallets').hash_table_data($db, 'log_wallets').hash_table_data($db, 'my_dc_transactions');
if ($hash1!=$hash2)
	print 'ERROR';

?>