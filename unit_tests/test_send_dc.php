<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'send_dc';

$data = array(
	array('user_id'=>1, 'to_user_id'=>2, 'amount'=>0.66, 'commission'=>0),
	array('user_id'=>1, 'to_user_id'=>1, 'amount'=>6.66, 'commission'=>10000),
	array('user_id'=>2, 'to_user_id'=>5, 'amount'=>0.01, 'commission'=>0),
	array('user_id'=>4, 'to_user_id'=>9, 'amount'=>5.66, 'commission'=>0),
	array('user_id'=>1, 'to_user_id'=>1, 'amount'=>1.66, 'commission'=>100),
	array('user_id'=>1, 'to_user_id'=>2, 'amount'=>0.66, 'commission'=>0.01)
);
$data_r = array_reverse($data, true);

$hashes_start = all_hashes();

foreach($data as $array) {

	$time = '1426283713';
	// hash
	$transaction_array[0] = '1111111111';
	// type
	$transaction_array[1] =  ParseData::findType($type);
	// time
	$transaction_array[2] = $time;
	// user_id
	$transaction_array[3] = 1;
	// to_user_id
	$transaction_array[4] = $array['to_user_id'];
	// currency_id
	$transaction_array[5] = 72;
	// amount
	$transaction_array[6] = $array['amount'];
	// commission
	$transaction_array[7] =  $array['commission'];
	// comment
	$transaction_array[8] = '1111111111111111111111111111111111';
	// sign
	$transaction_array[9] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

	$block_data['block_id'] = 130000;
	$block_data['time'] = $time;
	$block_data['user_id'] = 1;

	$parsedata = new ParseData('', $db);
	$parsedata->transaction_array = $transaction_array;
	$parsedata->block_data = $block_data;
	$parsedata->tx_hash = '11111111111111111111';
	$init = $type.'_init';
	$name = $type;
	$error = $parsedata->$init();
	if ($error) print $error;
	$parsedata->$name();

	$hashes_middle = all_hashes();
	foreach ($hashes_middle as $table=>$hash) {
		if ($hash!=$hashes_start[$table]) {
			print $table.' ';
		}
	}
	print "\n";
}


foreach($data_r as $array) {

	$time = '1426283713';
	// hash
	$transaction_array[0] = '1111111111';
	// type
	$transaction_array[1] =  ParseData::findType($type);
	// time
	$transaction_array[2] = $time;
	// user_id
	$transaction_array[3] = 1;
	// to_user_id
	$transaction_array[4] = $array['to_user_id'];
	// currency_id
	$transaction_array[5] = 72;
	// amount
	$transaction_array[6] = $array['amount'];
	// commission
	$transaction_array[7] =  $array['commission'];
	// comment
	$transaction_array[8] = '1111111111111111111111111111111111';
	// sign
	$transaction_array[9] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

	$block_data['block_id'] = 130000;
	$block_data['time'] = $time;
	$block_data['user_id'] = 1;

	$parsedata = new ParseData('', $db);
	$parsedata->transaction_array = $transaction_array;
	$parsedata->block_data = $block_data;
	$parsedata->tx_hash = '11111111111111111111';
	$init = $type.'_init';
	$name = $type;
	$error = $parsedata->$init();
	if ($error) print $error;
	$name_rollback = $type.'_rollback';
	$parsedata->$name_rollback();
}

$hashes_end = all_hashes();
foreach ($hashes_end as $table=>$hash) {
	if ($hash!=$hashes_start[$table]) {
		debug_print('ERROR in '.$table, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		print 'ERROR in '.$table;
	}
}

?>