<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$user_id = 91573;
$promised_amount_id = '551';
$amount = '2.45099825';
$type = 'mining';

$time = '1426283722';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = $user_id;
// promised_amount_id
$transaction_array[] = $promised_amount_id;
// amount
$transaction_array[] = $amount;
// sign
$data_for_sign = ParseData::findType($type).",{$time},{$user_id},{$promised_amount_id},{$amount}";

make_front_test();

?>