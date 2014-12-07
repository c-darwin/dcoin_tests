<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$user_id = 91573;
$type = 'new_admin';
$admin = 4;

$time = '1426283722';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = $user_id;
// admin
$transaction_array[] = $admin;
// sign
$data_for_sign = ParseData::findType($type).",{$time},{$user_id},{$admin}";
make_front_test();

?>