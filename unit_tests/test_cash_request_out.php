<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'cash_request_out';

$time = '1426283713';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 2;
//to_user_id
$transaction_array[] = 4;
//amount
$transaction_array[] = 600;
//comment
$transaction_array[] = 111111111;
//currency_id
$transaction_array[] = 21;
//hash_code
$transaction_array[] = 11111111111;
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 140000;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>