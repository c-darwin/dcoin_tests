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
$transaction_array[1] =  ParseData::findType('votes_complex');
// time
$transaction_array[2] = time();
// user_id
$transaction_array[3] = 1;
// json data
$transaction_array[4] = '{"currency":{"1":[0.0000000000000,0.0000000000000,1,0,0]},"referral":{"first":"29","second":"6","third":"13"}}';
// sign
$transaction_array[5] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 77952;
$block_data['time'] = time();

$hash1 = hash_table_data($db, 'votes_referral').hash_table_data($db, 'votes_miner_pct').hash_table_data($db, 'votes_user_pct').hash_table_data($db, 'votes_max_promised_amount').hash_table_data($db, 'votes_max_other_currencies').hash_table_data($db, 'votes_reduction');

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

$hash2 = hash_table_data($db, 'votes_referral').hash_table_data($db, 'votes_miner_pct').hash_table_data($db, 'votes_user_pct').hash_table_data($db, 'votes_max_promised_amount').hash_table_data($db, 'votes_max_other_currencies').hash_table_data($db, 'votes_reduction');
if ($hash1!=$hash2)
	print 'ERROR';

?>