<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$user_id = 91573;
$type = 'votes_complex';

$new_pct_data['referral']['first'] = 30;
$new_pct_data['referral']['second'] = 0;
$new_pct_data['referral']['third'] = 30;
$new_pct_data['currency'][72][0] = '0.0000000760368';
$new_pct_data['currency'][72][1] = '0.0000000497405';
$new_pct_data['currency'][72][2] = '1000';
$new_pct_data['currency'][72][3] = '55';
$new_pct_data['currency'][72][4] = '10';
$new_pct_data['admin'] = 0;
$json_data = json_encode($new_pct_data);

$time = '1426283722';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = $user_id;
// json data
$transaction_array[] = $json_data;
// sign
$data_for_sign = ParseData::findType($type).",{$time},{$user_id},{$json_data}";

make_front_test();

?>