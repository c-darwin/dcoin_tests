<?php
define( 'DC', true );
define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );

require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'includes/class-parsedata.php' );

unlink(ABSPATH . '/log/calc_profit.php.log');

$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

$amount = 5000;
$time_start = 1;
$time_finish = 100;
//$time_start = 100;
//$time_finish = 300;

$pct_array = 'Array

    [10] => Array
(
    [miner] => 0.01
    [user] => 0.01
)

    [43] => Array
(
    [miner] => 0.01
    [user] => 0.01
)

)';
preg_match_all('/\[([0-9]+)\].*?(0\.[0-9]+).*?(0\.[0-9]+)/s', $pct_array, $m);
$pct_array = array();
foreach($m[1] as $k=>$time) {
	$pct_array[$time]['miner'] = $m[2][$k];
	$pct_array[$time]['user'] = $m[2][$k];
}
/*
$pct_array = array(
	//0=>array('user'=>0.0000000000000, 'miner'=>0.0000000000000)
	100=>array('user'=>0.05, 'miner'=>1),
	200=>array('user'=>0.1, 'miner'=>1),
	300=>array('user'=>0.2, 'miner'=>1)
);
*/
// с первого блока = юзер
// дальше - результат подсчета баллов за голосования
$points_status_array = 'Array
(
    [20] => user
    [80] => miner
)
';
preg_match_all('/\[([0-9]+)\].*?(miner|user)/s', $points_status_array, $m);
$points_status_array = array();
foreach($m[1] as $k=>$time) {
	$points_status_array[$time] = $m[2][$k];
}

//$points_status_array = array(0=>'user');

$holidays_array = array(
		array(20, 25)
);

$max_promised_amount_array = 'Array
(
    [0] => 9000
    [50] => 10000
    [51] => 400
    [52] => 10000
    [53] => 400
    [54] => 10000
    [55] => 400
    [56] => 10000
)';
preg_match_all('/\[([0-9]+)\].*?([0-9]+)/s', $max_promised_amount_array, $m);
$max_promised_amount_array = array();
foreach($m[1] as $k=>$time) {
	$max_promised_amount_array[$time] = $m[2][$k];
}

$currency_id = 2;
$result = ParseData::calc_profit( $amount, $time_start, $time_finish, $pct_array, $points_status_array,$holidays_array, $max_promised_amount_array, $currency_id );
print file_get_contents(ABSPATH . 'log/calc_profit.php.log');
print $result;

?>
