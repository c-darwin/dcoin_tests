<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'del_cf_funding';

$time = '1426283715';
// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType($type);
// time
$transaction_array[2] = $time;
// user_id
$transaction_array[3] = 1;
//funding_id
$transaction_array[4] = 12;
// sign
$transaction_array[5] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130000;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>