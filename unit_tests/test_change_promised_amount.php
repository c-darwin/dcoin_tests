<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'change_promised_amount';

$time = '1426283721';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
// promised_amount_id
$transaction_array[] =  1;
// amount
$transaction_array[] =  10000;
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130005;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>