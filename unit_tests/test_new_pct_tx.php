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

//require_once( ABSPATH . 'tmp/_fns.php' );

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);


// берем все голоса miner_pct
$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT `currency_id`,
						 `pct`,
						  count(`user_id`) as `votes`
			FROM `".DB_PREFIX."votes_miner_pct`
			GROUP BY  `currency_id`, `pct`
			");
while ( $row = $db->fetchArray( $res ) )
	$pct_votes[$row['currency_id']]['miner_pct'][$row['pct']] = $row['votes'];

// берем все голоса user_pct
$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT `currency_id`,
						 `pct`,
						  count(`user_id`) as `votes`
			FROM `".DB_PREFIX."votes_user_pct`
			GROUP BY  `currency_id`, `pct`
			");
while ( $row = $db->fetchArray( $res ) )
	$pct_votes[$row['currency_id']]['user_pct'][$row['pct']] = $row['votes'];

if (!isset($pct_votes)) {
	debug_print( '!isset($pct_votes)', __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	main_unlock();
	exit;
}

/*
for ($i=5; $i<1000; $i++) {
	$key = rand(0, 390);
	$pct = ParseData::getPctValue($key);
	$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			INSERT INTO `".DB_PREFIX."votes_miner_pct` (user_id, currency_id, pct)
			VALUES ({$i}, 21, {$pct})
			");
	$key = rand(0, 390);
	$pct = ParseData::getPctValue($key);
	$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			INSERT INTO `".DB_PREFIX."votes_user_pct` (user_id, currency_id, pct)
			VALUES ({$i}, 21, {$pct})
			");
}*/

$PctArray = ParseData::getPctArray();
$new_pct = array();
foreach ( $pct_votes as $currency_id => $data ) {

	// определяем % для майнеров
	$pct_arr = ParseData::makePctArray($data['miner_pct']);
	$key = get_max_vote($pct_arr, 0, 390, 100);
	$new_pct['currency'][$currency_id]['miner_pct'] = ParseData::getPctValue($key);
	debug_print( '$key miner_pct='.$key, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	// определяем % для юзеров
	$pct_y = array_search($new_pct['currency'][$currency_id]['miner_pct'], $PctArray);
	debug_print( 'miner_pct $pct_y='.$pct_y, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	$max_user_pct_y = round($pct_y/2, 2);
	debug_print( '$max_user_pct='.$max_user_pct_y, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	$user_max_key = find_user_pct($max_user_pct_y);
	debug_print( '$user_max_key='.$user_max_key, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	$pct_arr = ParseData::makePctArray($data['user_pct']);
	// отрезаем лишнее, т.к. поиск идет ровно до макимального возможного, т.е. до miner_pct/2
	$pct_arr = del_user_pct($pct_arr, $user_max_key);
	debug_print( '$user_$pct_arr='.print_r_hex($pct_arr), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	$key = get_max_vote($pct_arr, 0, $user_max_key, 100);
	debug_print( '$key user_pct='.$key, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	$new_pct['currency'][$currency_id]['user_pct'] = ParseData::getPctValue($key);
	debug_print( 'user_pct='.$new_pct['currency'][$currency_id]['user_pct'], __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
	debug_print( 'user pct y='.array_search($new_pct['currency'][$currency_id]['user_pct'], $PctArray), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
}

debug_print( $new_pct, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

$ref_levels = array('first', 'second', 'third');
for ($i=0; $i<sizeof($ref_levels); $i++) {
	$level = $ref_levels[$i];
	// берем все голоса
	$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `{$level}`,
							  count(`user_id`) as `votes`
				FROM `".DB_PREFIX."votes_referral`
				GROUP BY  `{$level}`
				");
	while ( $row = $db->fetchArray( $res ) )
		$votes_referral[$row[$level]] = $row['votes'];
	$new_pct['referral'][$level] = get_max_vote($votes_referral, 0, 30, 10);
}

print_r($new_pct);

exit;


// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType('new_pct');
// time
$transaction_array[2] = time();
// user_id
$transaction_array[3] = 1;
// json data
$transaction_array[4] = json_encode($new_pct);
// sign
$transaction_array[5] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 77952;
$block_data['time'] = time();

$hash1 = hash_table_data($db, 'pct').hash_table_data($db, 'referral').hash_table_data($db, 'log_referral');

$parsedata = new ParseData('', $db);
$parsedata->transaction_array = $transaction_array;
$parsedata->block_data = $block_data;
$parsedata->new_pct_init();
//$error = $parsedata->new_reduction_front();
//if ($error) print $error;
//else {
	//$parsedata->new_pct();
	//$parsedata->new_pct_rollback();
	//$error = $parsedata->new_reduction_rollback_front();
//}

$hash2 = hash_table_data($db, 'pct').hash_table_data($db, 'referral').hash_table_data($db, 'log_referral');
if ($hash1!=$hash2)
	print 'ERROR';

?>