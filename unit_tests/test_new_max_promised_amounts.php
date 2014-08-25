<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'new_max_promised_amounts';

$new_pct_data[22] = '1000';
$new_pct_data[21] = '2000';
$new_pct_data[72] = '11111';
$new_pct_data[1] = '1';

$time = '1426283721';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
// json data
$transaction_array[] = json_encode($new_pct_data);
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130005;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>