<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'cf_send_dc';

$time = '1426283713';
// hash
$transaction_array[0] = '22cb812e53e22ee539af4a1d39b4596d';
// type
$transaction_array[1] =  ParseData::findType($type);
// time
$transaction_array[2] = $time;
// user_id
$transaction_array[3] = 1;
//project_id
$transaction_array[4] = 11;
//amount
$transaction_array[5] = 100;
//commission
$transaction_array[6] = 5;
//comment
$transaction_array[7] = 'ORDER #15155 авыАвыАывавы';
// sign
$transaction_array[8] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 185510;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>