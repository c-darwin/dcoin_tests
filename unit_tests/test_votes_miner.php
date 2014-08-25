<?php

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

$type = 'votes_miner';
$hashes_start = all_hashes();
$time = '1426283726';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 50;
// vote_id
$transaction_array[] = 14;
// result
$transaction_array[] = 1;
// comment
$transaction_array[] = 222222222222222;
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130006;
$block_data['time'] = $time;
$block_data['user_id'] = 1;
$parsedata = new ParseData('', $db);
$parsedata->transaction_array = $transaction_array;
$parsedata->block_data = $block_data;
$parsedata->tx_hash = '11111111111';
$init = $type.'_init';
$name = $type;
$name_rollback = $type.'_rollback';
$error = $parsedata->$init();
if ($error) print $error;
if (!@$argv[1]) {
	$parsedata->$name();
	$hashes_middle = all_hashes();
	foreach ($hashes_middle as $table=>$hash) {
		if ($hash!=$hashes_start[$table]) {
			print $table.' ';
		}
	}
	print "\n";
	$parsedata->$name_rollback();
	$hashes_end = all_hashes();
	foreach ($hashes_end as $table=>$hash) {
		if ($hash!=$hashes_start[$table]) {
			debug_print('ERROR in '.$table, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
			print 'ERROR in '.$table;
		}
	}
}
else if ($argv[1]=='w') {
	print 'work';
	$parsedata->$name();
}
else if ($argv[1]=='r') {
	print 'rollback';
	$parsedata->$name_rollback();
}

?>