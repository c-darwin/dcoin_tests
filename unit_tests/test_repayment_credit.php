<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'repayment_credit';

$time = '1426283713';
// hash
$transaction_array[] = '22cb812e53e22ee539af4a1d39b4596d';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
//credit_id
$transaction_array[] = 1;
//amount
$transaction_array[] = 100;
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 185510;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>