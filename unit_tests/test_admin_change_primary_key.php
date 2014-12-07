<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'admin_change_primary_key';

$time = '1426283755';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 91573;
// for_user_id
$transaction_array[] =  91573;
// public_key
$transaction_array[] =  ParseData::encode_length_plus_data(hextobin('423423423'));
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130005;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>