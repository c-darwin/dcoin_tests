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

function hash_table_data($db)
{
	$columns = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT GROUP_CONCAT( column_name SEPARATOR ',' )
		FROM information_schema.columns
		WHERE table_schema = 'FC'
		AND table_name = '".DB_PREFIX."promised_amount'
		", 'fetch_one');
	return $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT MD5(GROUP_CONCAT( CONCAT_WS( '#', {$columns}) SEPARATOR '##' )) FROM `".DB_PREFIX."promised_amount`
		", 'fetch_one');
}

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType('new_reduction');
// time
$transaction_array[2] = time();
// user_id
$transaction_array[3] = 1;
// currency_id
$transaction_array[4] = 72;
// pct
$transaction_array[5] = 0;
// sign
$transaction_array[6] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 222;
$block_data['time'] = time();

$hash1 = hash_table_data($db, 'promised_amount').hash_table_data($db, 'wallets').hash_table_data($db, 'cash_requests');

$parsedata = new ParseData('', $db);
$parsedata->transaction_array = $transaction_array;
$parsedata->block_data = $block_data;
$parsedata->new_reduction_init();
//$error = $parsedata->new_reduction_front();
//if ($error) print $error;
//else {
	$parsedata->new_reduction();
	$parsedata->new_reduction_rollback();
	//$error = $parsedata->new_reduction_rollback_front();
//}

$hash2 = hash_table_data($db, 'promised_amount').hash_table_data($db, 'wallets').hash_table_data($db, 'cash_requests');
if ($hash1!=$hash2)
	print 'ERROR';

?>