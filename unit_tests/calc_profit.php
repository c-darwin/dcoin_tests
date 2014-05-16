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

// 10
$test_data[10]['amount'] = 1500;
$test_data[10]['time_start'] = 100;
$test_data[10]['time_finish'] = 300;
$test_data[10]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[10]['points_status_array'] = array(0=>'miner', 101=>'user', 120=>'miner');
$test_data[10]['holidays_array'] = array(
	array(20, 30),
	array(90, 101),
	array(299, 300),
	array(330, 350)
);
$test_data[10]['max_promised_amount_array'] = array(0=>1000, 150=>1600, 210=>100, 220=>10000);
$test_data[10]['currency_id'] = 10;
$test_data[10]['result'] = '98987.623915392';


// 11
$test_data[11]['amount'] = 1500;
$test_data[11]['time_start'] = 100;
$test_data[11]['time_finish'] = 300;
$test_data[11]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	150=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[11]['points_status_array'] = array(0=>'miner', 101=>'user', 120=>'miner');
$test_data[11]['holidays_array'] = array(
	array(20, 350)
);
$test_data[11]['max_promised_amount_array'] = array(0=>1000, 150=>1600, 210=>100, 220=>10000);
$test_data[11]['currency_id'] = 10;
$test_data[11]['result'] = '0';


// 12
$test_data[12]['amount'] = 1500;
$test_data[12]['time_start'] = 0;
$test_data[12]['time_finish'] = 300;
$test_data[12]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[12]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[12]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(290, 10000000)
);
$test_data[12]['max_promised_amount_array'] = array(0=>1000, 220=>10000);
$test_data[12]['currency_id'] = 10;
$test_data[12]['result'] = '73337.843828611';

// 13
$test_data[13]['amount'] = 1500;
$test_data[13]['time_start'] = 300;
$test_data[13]['time_finish'] = 400;
$test_data[13]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[13]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[13]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(290, 295)
);
$test_data[13]['max_promised_amount_array'] = array(0=>1000, 220=>500);
$test_data[13]['currency_id'] = 10;
$test_data[13]['result'] = '3122.3230591262';


// 14
$test_data[14]['amount'] = 1500;
$test_data[14]['time_start'] = 50;
$test_data[14]['time_finish'] = 51;
$test_data[14]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[14]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[14]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(290, 295)
);
$test_data[14]['max_promised_amount_array'] = array(0=>1000, 220=>500);
$test_data[14]['currency_id'] = 10;
$test_data[14]['result'] = '50';


// 15
$test_data[15]['amount'] = 1500;
$test_data[15]['time_start'] = 50;
$test_data[15]['time_finish'] = 51;
$test_data[15]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	10=>array('user'=>0.0049, 'miner'=>0.04),
	11=>array('user'=>0.0088, 'miner'=>0.08),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[15]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[15]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(51, 250),
	array(290, 295),
	array(500, 600),
);
$test_data[15]['max_promised_amount_array'] = array(0=>1000, 220=>500);
$test_data[15]['currency_id'] = 10;
$test_data[15]['result'] = '80';


// 16
$test_data[16]['amount'] = 1500;
$test_data[16]['time_start'] = 50;
$test_data[16]['time_finish'] = 51;
$test_data[16]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	10=>array('user'=>0.0049, 'miner'=>0.04),
	11=>array('user'=>0.0088, 'miner'=>0.08),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[16]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[16]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(51, 250),
	array(290, 295),
	array(500, 600),
);
$test_data[16]['max_promised_amount_array'] = array(0=>1000, 220=>500);
$test_data[16]['currency_id'] = 1;
$test_data[16]['result'] = '80';


// 17
$test_data[17]['amount'] = 1500;
$test_data[17]['time_start'] = 1000;
$test_data[17]['time_finish'] = 1001;
$test_data[17]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	10=>array('user'=>0.0049, 'miner'=>0.04),
	11=>array('user'=>0.0088, 'miner'=>0.08),
	200=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[17]['points_status_array'] = array(0=>'miner', 101=>'user', 295=>'miner');
$test_data[17]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 40),
	array(51, 250),
	array(290, 295),
	array(500, 600),
);
$test_data[17]['max_promised_amount_array'] = array(0=>1000, 220=>500);
$test_data[17]['currency_id'] = 10;
$test_data[17]['result'] = '10';


// 18
$test_data[18]['amount'] = 1500;
$test_data[18]['time_start'] = 50;
$test_data[18]['time_finish'] = 140;
$test_data[18]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	164=>array('user'=>0.0049, 'miner'=>0.04),
	223=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[18]['points_status_array'] = array(0=>'miner', 98=>'miner', 101=>'user', 101=>'user', 295=>'miner');
$test_data[18]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 30),
	array(40, 50),
	array(66, 99),
	array(233, 1999),
);
$test_data[18]['max_promised_amount_array'] = array(0=>1000, 63=>3333, 63=>3333, 156=>899,  220=>500);
$test_data[18]['currency_id'] = 10;
$test_data[18]['result'] = '5157.6623487708';


// 19
$test_data[19]['amount'] = 1500;
$test_data[19]['time_start'] = 50;
$test_data[19]['time_finish'] = 140;
$test_data[19]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	164=>array('user'=>0.0049, 'miner'=>0.04),
	223=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[19]['points_status_array'] = array(0=>'miner', 98=>'miner', 101=>'user', 101=>'user', 295=>'miner');
$test_data[19]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 30),
	array(40, 50),
	array(66, 99),
	array(233, 1999),
);
$test_data[19]['max_promised_amount_array'] = array(0=>1000, 63=>3333, 63=>3333, 156=>899,  220=>500);
$test_data[19]['currency_id'] = 1;
$test_data[19]['result'] = '129106.50065867';


// 20
$test_data[20]['amount'] = 1500;
$test_data[20]['repaid_amount'] = 50;
$test_data[20]['time_start'] = 50;
$test_data[20]['time_finish'] = 140;
$test_data[20]['pct_array'] = array(
	0=>array('user'=>0.0059, 'miner'=>0.05),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	36=>array('user'=>0.0088, 'miner'=>0.08),
	164=>array('user'=>0.0049, 'miner'=>0.04),
	223=>array('user'=>0.0029, 'miner'=>0.02)
);
$test_data[20]['points_status_array'] = array(0=>'miner', 98=>'miner', 101=>'user', 101=>'user', 295=>'miner');
$test_data[20]['holidays_array'] = array(
	array(0, 10),
	array(10, 20),
	array(30, 30),
	array(40, 50),
	array(66, 99),
	array(233, 1999),
);
$test_data[20]['max_promised_amount_array'] = array(0=>1000, 63=>1525, 64=>1550,  139=>500);
$test_data[20]['currency_id'] = 10;
$test_data[20]['result'] = '4966.7977985526';


for ($i=0; $i<sizeof($test_data); $i++) {
	$result[$i]['result'] = ParseData::calc_profit( $test_data[$i]['amount'], $test_data[$i]['time_start'], $test_data[$i]['time_finish'], $test_data[$i]['pct_array'], $test_data[$i]['points_status_array'],$test_data[$i]['holidays_array'], $test_data[$i]['max_promised_amount_array'], $test_data[$i]['currency_id'], @$test_data[$i]['repaid_amount'] );
	print file_get_contents(ABSPATH . 'log/calc_profit.php.log');
	if ((string)$test_data[$i]['result']!=(string)$result[$i]['result'])
		die ('BUG IN '.$i.' = '.$test_data[$i]['result'].'!='.$result[$i]['result'].'');
}
//print_R($result);

?>
