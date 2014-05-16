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

$amount = 1000.02;
$time_start = 1;
$time_finish = 300;
//$time_start = 100;
//$time_finish = 300;

$pct_array = 'Array
(
    [0] => Array
(
    [miner] => 0.1
    [user] => 0.2
)

    [300] => Array
(
    [miner] => 0.5
    [user] => 0.6
)


)
';
preg_match_all('/\[([0-9]+)\].*?(0\.[0-9]+).*?(0\.[0-9]+)/s', $pct_array, $m);
$pct_array = array();
foreach($m[1] as $k=>$time) {
	$pct_array[$time]['miner'] = $m[2][$k];
	$pct_array[$time]['user'] = $m[3][$k];
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
    [0] => user
)
';
preg_match_all('/\[([0-9]+)\].*?(miner|user)/s', $points_status_array, $m);
$points_status_array = array();
foreach($m[1] as $k=>$time) {
	$points_status_array[$time] = $m[2][$k];
}

//$points_status_array = array(0=>'user');

$holidays_array = array(
	array(100, 120),
);

$max_promised_amount_array = 'Array
(
    [0] => 1000
)';
preg_match_all('/\[([0-9]+)\].*?([0-9]+)/s', $max_promised_amount_array, $m);
$max_promised_amount_array = array();
foreach($m[1] as $k=>$time) {
	$max_promised_amount_array[$time] = $m[2][$k];
}

// 0
$test_data[0]['amount'] = 10;
$test_data[0]['time_start'] = 0;
$test_data[0]['time_finish'] = 300;
$test_data[0]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05)
);
$test_data[0]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner', 203=>'miner');
$test_data[0]['holidays_array'] = array(
	array(130, 150)
);
$test_data[0]['max_promised_amount_array'] = array(0=>1000);
$test_data[0]['currency_id'] = 10;
$test_data[0]['result'] = '288977.43266019';

// 1
$test_data[1]['amount'] = 10;
$test_data[1]['time_start'] = 0;
$test_data[1]['time_finish'] = 300;
$test_data[1]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	250=>array('user'=>0.0019, 'miner'=>0.01),
	301=>array('user'=>0.0029, 'miner'=>0.03),
	300=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[1]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner', 203=>'miner');
$test_data[1]['holidays_array'] = array(
	array(130, 150)
);
$test_data[1]['max_promised_amount_array'] = array(0=>1000);
$test_data[1]['currency_id'] = 10;
$test_data[1]['result'] = '41436.006657618';

// 2
$test_data[2]['amount'] = 10;
$test_data[2]['time_start'] = 0;
$test_data[2]['time_finish'] = 300;
$test_data[2]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	250=>array('user'=>0.0019, 'miner'=>0.01),
	301=>array('user'=>0.0029, 'miner'=>0.03),
	300=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[2]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner', 203=>'miner');
$test_data[2]['holidays_array'] = array(
	array(130, 500)
);
$test_data[2]['max_promised_amount_array'] = array(0=>1000);
$test_data[2]['currency_id'] = 10;
$test_data[2]['result'] = '1627.6030645193';


// 3
$test_data[3]['amount'] = 10;
$test_data[3]['time_start'] = 0;
$test_data[3]['time_finish'] = 300;
$test_data[3]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	250=>array('user'=>0.0019, 'miner'=>0.01),
	301=>array('user'=>0.0029, 'miner'=>0.03),
	300=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[3]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner', 203=>'miner');
$test_data[3]['holidays_array'] = array(
	array(130, 140),
	array(150, 160),
	array(170, 210)
);
$test_data[3]['max_promised_amount_array'] = array(0=>1000);
$test_data[3]['currency_id'] = 10;
$test_data[3]['result'] = '21317.770423946';

// 4
$test_data[4]['amount'] = 10;
$test_data[4]['time_start'] = 0;
$test_data[4]['time_finish'] = 300;
$test_data[4]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	250=>array('user'=>0.0019, 'miner'=>0.01),
	300=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[4]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner', 203=>'miner', 210=>'user');
$test_data[4]['holidays_array'] = array(
	array(130, 140),
	array(150, 160),
	array(170, 210)
);
$test_data[4]['max_promised_amount_array'] = array(0=>1000);
$test_data[4]['currency_id'] = 10;
$test_data[4]['result'] = '2552.8073541488';


// 5
$test_data[5]['amount'] = 10;
$test_data[5]['time_start'] = 100;
$test_data[5]['time_finish'] = 300;
$test_data[5]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[5]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner');
$test_data[5]['holidays_array'] = array(
	array(20, 30),
	array(90, 100)
);
$test_data[5]['max_promised_amount_array'] = array(0=>1000);
$test_data[5]['currency_id'] = 10;
$test_data[5]['result'] = '107.29341748147';


// 6
$test_data[6]['amount'] = 1500;
$test_data[6]['time_start'] = 100;
$test_data[6]['time_finish'] = 300;
$test_data[6]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[6]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner');
$test_data[6]['holidays_array'] = array(
	array(20, 30),
	array(90, 150)
);
$test_data[6]['max_promised_amount_array'] = array(0=>1000, 150=>1600);
$test_data[6]['currency_id'] = 1;
$test_data[6]['result'] = '15153.345929561';


// 7
$test_data[7]['amount'] = 1500;
$test_data[7]['time_start'] = 100;
$test_data[7]['time_finish'] = 300;
$test_data[7]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[7]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner');
$test_data[7]['holidays_array'] = array(
	array(20, 30),
	array(90, 150)
);
$test_data[7]['max_promised_amount_array'] = array(0=>1000, 150=>1600, 210=>100);
$test_data[7]['currency_id'] = 10;
$test_data[7]['result'] = '4139.6240767059';

// 8
$test_data[8]['amount'] = 1500;
$test_data[8]['time_start'] = 100;
$test_data[8]['time_finish'] = 300;
$test_data[8]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[8]['points_status_array'] = array(0=>'miner', 101=>'user', 200=>'miner');
$test_data[8]['holidays_array'] = array(
	array(20, 30),
	array(90, 150)
);
$test_data[8]['max_promised_amount_array'] = array(0=>1000, 150=>1600, 210=>100);
$test_data[8]['currency_id'] = 1;
$test_data[8]['result'] = '7738.6462401027';


// 9
$test_data[9]['amount'] = 1500;
$test_data[9]['time_start'] = 100;
$test_data[9]['time_finish'] = 300;
$test_data[9]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[9]['points_status_array'] = array(0=>'miner', 101=>'user', 120=>'miner');
$test_data[9]['holidays_array'] = array(
	array(20, 30),
	array(90, 101)
);
$test_data[9]['max_promised_amount_array'] = array(0=>1000, 150=>1600, 210=>100, 220=>10000);
$test_data[9]['currency_id'] = 10;
$test_data[9]['result'] = '100997.3763937';


for ($i=0; $i<sizeof($test_data); $i++) {
	$result[$i]['result'] = ParseData::calc_profit( $test_data[$i]['amount'], $test_data[$i]['time_start'], $test_data[$i]['time_finish'], $test_data[$i]['pct_array'], $test_data[$i]['points_status_array'],$test_data[$i]['holidays_array'], $test_data[$i]['max_promised_amount_array'], $test_data[$i]['currency_id'] );
	print file_get_contents(ABSPATH . 'log/calc_profit.php.log');
	if ((string)$test_data[$i]['result']!=(string)$result[$i]['result'])
		die ('BUG IN '.$i.' = '.$test_data[$i]['result'].'!='.$result[$i]['result'].'');
}
//print_R($result);

?>
