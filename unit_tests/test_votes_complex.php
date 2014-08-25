<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'votes_complex';

$new_pct_data['referral']['first'] = 30;
$new_pct_data['referral']['second'] = 0;
$new_pct_data['referral']['third'] = 30;
$new_pct_data['currency'][1][0] = '0.0000000760368';
$new_pct_data['currency'][1][1] = '0.0000000497405';
$new_pct_data['currency'][1][2] = '1000';
$new_pct_data['currency'][1][3] = '55';
$new_pct_data['currency'][1][4] = '10';
$new_pct_data['currency'][33][0] = '0.0000000760368';
$new_pct_data['currency'][33][1] = '0.0000000497405';
$new_pct_data['currency'][33][2] = '1000';
$new_pct_data['currency'][33][3] = '55';
$new_pct_data['currency'][33][4] = '10';
$new_pct_data['currency'][2][0] = '0.0000000760368';
$new_pct_data['currency'][2][1] = '0.0000000497405';
$new_pct_data['currency'][2][2] = '1000';
$new_pct_data['currency'][2][3] = '55';
$new_pct_data['currency'][2][4] = '10';
$time = '1426283722';
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
$block_data['block_id'] = 130006;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>