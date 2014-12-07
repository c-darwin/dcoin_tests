<?php

set_time_limit(0);

require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/autoload.php' );

function all_hashes()
{
	global $db;
	$tables_array = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SHOW TABLES
				", 'array');
	$hashes = array();
	foreach($tables_array as $table) {
		$order_by = '';
		if (preg_match('/^(log_forex_orders|log_forex_orders_main|cf_comments|cf_currency|cf_funding|cf_lang|cf_projects|cf_projects_data)$/i', $table) ) {
			$order_by = "`id`";
		}
		else if (preg_match('/log_time_(.*)/i', $table, $t_name) && $table!='log_time_money_orders'){
			$order_by = "`user_id`, `time`";
		}
		else if (preg_match('/^(log_transactions)$/i', $table) ) {
			$order_by = "`time`";
		}
		else if (preg_match('/^(log_votes)$/i', $table) ) {
			$order_by = "`user_id`, `voting_id`";
		}
		else if (preg_match('/^(log_(.*))$/i', $table && $table!='log_minute')) {
			$order_by = "`log_id`";
		}
		else if (preg_match('/^(wallets)$/i', $table)) {
			$order_by = "`last_update`";
		}

		if ($order_by) $order_by = 'ORDER BY '.$order_by.' DESC';

		$hashes[$table] = hash_table_data($db, $table, $order_by);
	}
	return $hashes;
}


function make_test()
{
	global $db, $transaction_array, $block_data, $type, $hashes_start, $argv;
	$parsedata = new ParseData('', $db);
	$parsedata->transaction_array = $transaction_array;
	$parsedata->block_data = $block_data;
	$parsedata->tx_hash = '11111111111111111111';
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
}


function make_front_test()
{
	global $transaction_array, $time, $data_for_sign, $db, $type, $user_id;
	$node_arr = array('new_admin');
	define('MY_PREFIX', $user_id.'_');
	if (in_array($type, $node_arr)) {
		$private_key = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `private_key`
				FROM `".DB_PREFIX.MY_PREFIX."my_node_keys`
				WHERE `block_id` = (SELECT max(`block_id`) FROM `".DB_PREFIX.MY_PREFIX."my_node_keys`)
				LIMIT 1
				", 'fetch_one');
	}
	else {
		$private_key = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `private_key`
				FROM `".DB_PREFIX.MY_PREFIX."my_keys`
				WHERE `block_id` = (SELECT max(`block_id`) FROM `".DB_PREFIX.MY_PREFIX."my_keys`)
				LIMIT 1
				", 'fetch_one');
	}
	print '$data_for_sign='.$data_for_sign."\n";
	print '$private_key='.$private_key."\n";
	$rsa = new Crypt_RSA();
	$rsa->loadKey($private_key);
	$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
	$signature = $rsa->sign($data_for_sign);
	print '$signature='.bin2hex($signature)."\n";
	if (!in_array($type, $node_arr))
		$bin_signatures = ParseData::encode_length_plus_data($signature);
	else
		$bin_signatures = $signature;
	//$bin_signatures = ParseData::encode_length_plus_data($sign);

	$transaction_array[] = $bin_signatures;
	$block_data['block_id'] = 160006;
	$block_data['time'] = $time;
	$block_data['user_id'] = 1;

	$parsedata = new ParseData('', $db);
	$parsedata->transaction_array = $transaction_array;
	$parsedata->block_data = $block_data;
	$parsedata->tx_hash = '11111111111111111111';
	$init = $type.'_init';
	$name = $type.'_front';
	$name_rollback = $type.'_rollback_front';
	$error = $parsedata->$init();
	if ($error) print $error;
	$error = $parsedata->$name();
	if ($error) print $error;
	$error = $parsedata->$name_rollback();
	if ($error) print $error;

}

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$hashes_start = all_hashes();


// **************** ищем баг с points ****************
/*
print "points--------------\n";
$data = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
	SHOW TABLE STATUS LIKE '".DB_PREFIX."log_points'
	", 'fetch_array');
print $data['Auto_increment']."\n";

$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT *
		FROM`".DB_PREFIX."points`
		");
while ($row = $db->fetchArray($res))
	print_R($row);

$res = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
		SELECT *
		FROM`".DB_PREFIX."log_points`
		");
while ($row = $db->fetchArray($res))
	print_R($row);

print "/points--------------\n";
*/

?>