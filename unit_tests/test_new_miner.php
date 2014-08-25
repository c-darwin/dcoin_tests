<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'new_miner';

$time = '1426283717';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
//race
$transaction_array[] = 1;
//country
$transaction_array[] = 1;
//latitude
$transaction_array[] = 55;
//longitude
$transaction_array[] = 55;
//host
$transaction_array[] = 'http://55.55.55.55/';
//face_coords
$transaction_array[] = '[[118,275],[241,274],[39,274],[316,276],[180,364],[182,430],[181,490],[93,441],[259,433]]';
//profile_coords
$transaction_array[] = '[[289,224],[148,216],[172,304],[123,239],[328,261],[305,349]]';
//face_hash
$transaction_array[] = 'face_hash';
//profile_hash
$transaction_array[] = 'profile_hash';
//video_type
$transaction_array[] = 'youtube';
//video_url_id
$transaction_array[] = 'video_url_id';
//node_public_key
$transaction_array[] = 'node_public_key';
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 140002;
$block_data['time'] = $time;
$block_data['user_id'] = 2;

make_test();

?>