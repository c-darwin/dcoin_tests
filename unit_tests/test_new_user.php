<?php
define( 'DC', TRUE);
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
require_once( ABSPATH . 'tmp/_fns.php' );

$type = 'new_user';

$time = '1426283721';
// hash
$transaction_array[] = '1111111111';
// type
$transaction_array[] =  ParseData::findType($type);
// time
$transaction_array[] = $time;
// user_id
$transaction_array[] = 1;
// public_key
$transaction_array[] =  hextobin('30820122300d06092a864886f70d01010105000382010f003082010a0282010100ae7797b5c16358862f083bb26cde86b233ba97c48087df44eaaf88efccfe554bf51df8dc7e99072cbe433933f1b87aa9ef62bd5d49dc40e75fe398426c727b0773ea9e4d88184d64c1aa561b1cdf78abe07ca5d23711c403f58abf30d41f4b96161649a91a95818d9d482e8fa3f91829abce3d80f6fc3708ce23f6841bb4a8bae301b23745fce5134420fec0519a081f162d16e4dd0da2e8869b5b67122a1fb7e9bcdb8b2512d1edabdb271bee190563b36a66f5498f50d2fc7202ad2f43b90f860428d5ecd67973900d9997475d4e1a1e4c56b44411cc4b5e9c660fe23fdcd5ab956a834fa05a4ecac9d815143d84993c9424d86379b6f76e3be9aeaaff48fb0203010001');
// sign
$transaction_array[] = '11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111';
$block_data['block_id'] = 130005;
$block_data['time'] = $time;
$block_data['user_id'] = 1;

make_test();

?>