<?php

$main_path = '/usr/share/nginx/html/';
$config_ini = parse_ini_file("{$main_path}config_stend.ini", true);
$num_stends = $config_ini['test_stend']['count_stends'];

function get_db() {

	global $num_stends;
	// у какого юзера какой стенд
	for ($i=0; $i<$num_stends; $i++) {
		$db = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, "stend_{$i}", DB_PORT);
		$user_id = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `user_id`
				FROM `".DB_PREFIX."my_table`
				LIMIT 1
				", 'fetch_one' );
		$db_stends[$user_id]['db_link'] = $db;
		$db_stends[$user_id]['stend_id'] = $i;
	}

	// БД админа
	$db_admin = new MySQLidb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
	$db_stends[1]['db_link'] = $db_admin;
	$db_stends[1]['stend_id'] =1;

	return $db_stends;
}

function wait_tx_gen ($db)
{
    $wait = trim(file_get_contents(ABSPATH.'tmp/wait_tx_gen'));
    if ($wait>0){
        for ($i=0; $i<$wait; $i++) {
            sleep(1);
            upd_deamon_time($db);
        }
    }
}

function get_sleep()
{
	$ini_array = parse_ini_file(ABSPATH . "config_stend.ini", true);
	$name = substr(get_script_name(), 0, -4);
	sleep($ini_array['test_stend'][$name.'_sleep']);
}

?>
