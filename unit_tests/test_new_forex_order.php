<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'new_forex_order';

$data = array(
	array('sell_currency_id'=>72, 'sell_rate'=>9999999999, 'amount'=>99999999.66, 'buy_currency_id'=>1, 'commission'=>10000),
	array('sell_currency_id'=>1, 'sell_rate'=>1.99999999, 'amount'=>555555.66, 'buy_currency_id'=>72, 'commission'=>0),
	array('sell_currency_id'=>1, 'sell_rate'=>0.00000005, 'amount'=>0.01, 'buy_currency_id'=>72, 'commission'=>0)
);

foreach($data as $array) {

	$hashes_start = all_hashes();
	$time = '1426283713';
	// hash
	$transaction_array[0] = '22cb812e53e22ee539af4a1d39b4596d';
	// type
	$transaction_array[1] =  ParseData::findType($type);
	// time
	$transaction_array[2] = $time;
	// user_id
	$transaction_array[3] = 1;

	$transaction_array[4] = $array['sell_currency_id'];
	$transaction_array[5] = $array['sell_rate'];
	$transaction_array[6] = $array['amount'];
	$transaction_array[7] = $array['buy_currency_id'];
	$transaction_array[8] = $array['commission'];
	// sign
	$transaction_array[9] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

	$block_data['block_id'] = 120000;
	$block_data['time'] = $time;
	$block_data['user_id'] = 1;

	make_test();

}

?>