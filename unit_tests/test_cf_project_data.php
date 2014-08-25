<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'cf_project_data';

$time = '1426283713';
// hash
$transaction_array[0] = '1111111111';
// type
$transaction_array[1] =  ParseData::findType($type);
// time
$transaction_array[2] = $time;
// user_id
$transaction_array[3] = 4;
//project_id
$transaction_array[4] = 1;
//lang_id
$transaction_array[5] = 45;
//blurb_img
$transaction_array[6] = 'http://i.imgur.com/YRCoVnc.jpg';
//head_img
$transaction_array[7] = 'http://i.imgur.com/YRCoVnc.jpg';
//description_img
$transaction_array[8] = 'http://i.imgur.com/YRCoVnc.jpg';
//picture
$transaction_array[9] = 'http://i.imgur.com/YRCoVnc.jpg';
//video_type
$transaction_array[10] = 'youtube';
//video_url_id
$transaction_array[11] = 'X-_fg47G5yf-_f';
//news_img
$transaction_array[12] = 'http://i.imgur.com/YRCoVnc.jpg';
//links
$transaction_array[13] = '[["http:\/\/goo.gl\/fnfh1Dg",1,532,234,0],["http:\/\/goo.gl\/28Fh4h",1355,1344,2222,66]]';
//hide
$transaction_array[14] = '1';
// sign
$transaction_array[15] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';

$block_data['block_id'] = 140002;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>