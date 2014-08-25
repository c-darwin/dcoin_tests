<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'change_primary_key';

$bin_public_key_1 = hextobin('423423423');
$bin_public_key_2 = hextobin('');
$bin_public_key_3 = hextobin('');
$bin_public_key_pack =  ParseData::encode_length_plus_data($bin_public_key_1) .
	ParseData::encode_length_plus_data($bin_public_key_2) .
	ParseData::encode_length_plus_data($bin_public_key_3);

$time = '1426283721';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
// public_keys
$transaction_array[] =  $bin_public_key_pack;
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130005;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>