<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'new_cf_project';

$time = '1426383715';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 4;
//currency_id
$transaction_array[] = 72;
//amount
$transaction_array[] = 5000;
//end_time
$transaction_array[] = time()+3600*24*30;
//latitude
$transaction_array[] = 39.94801;
//langitude
$transaction_array[] = 39.94801;
//category
$transaction_array[] = 1;
//project_currency_name
$transaction_array[] = '0VVDDDF';
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 130000;
$block_data['time'] = $time;
$block_data['user_id'] = 2;

make_test();

?>