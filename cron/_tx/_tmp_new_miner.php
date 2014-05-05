<?php
define( 'DC', true );
define( 'ABSPATH', dirname(dirname(dirname(__FILE__))) . '/' );
require_once( ABSPATH . 'includes/errors.php' );
require_once( ABSPATH . 'db_config.php' );
require_once( ABSPATH . 'includes/class-mysql.php' );
require_once( ABSPATH . 'includes/fns-main.php' );
require_once( ABSPATH . 'includes/class-parsedata.php' );

require_once( ABSPATH . 'phpseclib/Math/BigInteger.php');
require_once( ABSPATH . 'phpseclib/Crypt/Random.php');
require_once( ABSPATH . 'phpseclib/Crypt/Hash.php');
require_once( ABSPATH . 'phpseclib/Crypt/RSA.php');
require_once( ABSPATH . 'phpseclib/Crypt/AES.php');

require_once( ABSPATH . 'cron/_tx/_tmp_main_fns.php');


do {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	upd_deamon_time($db_admin);

	$res = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
			SELECT *
			FROM `".DB_PREFIX."users`
			WHERE  `user_id` > 1
			");
	while ($row = $db_admin->fetchArray($res)) {

		// работаем с БД стендом
		$stend_user_id = $row['user_id'];
		if (!isset($db_stends[$stend_user_id]))
			continue;
		$db = $db_stends[$stend_user_id]['db_link'];
		$stend_id = $db_stends[$stend_user_id]['stend_id'];

		debug_print("stend_user_id={$stend_user_id}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		debug_print("stend_id={$stend_id}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

		// проверим, нет ли локально нодовского ключа
		$my_keys =  $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT *
				FROM `".DB_PREFIX."my_node_keys`
				ORDER BY `my_time` DESC
				", 'fetch_array' );
		debug_print("my_keys=".print_r_hex($my_keys), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		// если есть, то значит тр-ия просто не успела обработаться
		if (!$my_keys || ($my_keys['my_time'] < time()-NEW_MINER_TIME_SEC && $my_keys['block_id']==0)) {

			$i = $stend_id;
			$type = ParseData::findType('new_miner');
			$time = time();
			$user_id = $stend_user_id;
			$race = 2;
			$country =180;
			$host = "http://192.168.100.57:8000/{$i}/";
			$latitude = '39.'.rand(0, 9999);
			$longitude = '-75.'.rand(0, 9999);
			$face_hash = '0103ba173c61a9e964340bcde1854be4abd14740cd0b0684a2cb39edf93f4295';
			$profile_hash = '2c0817e531ba510ed27c4e9c33784575084e2f5ddac628faa40ccdf708047115';
			$face_coords = '[[102,168],[268,178],[43,230],[266,231],[193,289],[181,383],[197,453],[60,398],[321,415]]';
			$profile_coords = '[[277,185],[144,157],[165,232],[120,187],[320,244],[286,317]]';
			$video_type = 'youtube';
			$video_url_id = 'ZSt9tm3RoUU';

			$rsa = new Crypt_RSA();
			extract($rsa->createKey(1024));
			$node_public_key = clear_public_key($publickey);

			// подписываем нашим нод-ключем данные транзакции
			$data_for_sign = "{$type},{$time},{$user_id},{$race},{$country},{$latitude},{$longitude},{$host},{$face_hash},{$profile_hash},{$face_coords},{$profile_coords},{$video_type},{$video_url_id},{$node_public_key}";
			debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			$encrypt_private_key = base64_decode( get_miner_private_key($db));
			$signature = encrypt_and_sign($i, $encrypt_private_key, $data_for_sign);

			$node_public_key_bin = hextobin($node_public_key);
			$data = dec_binary ($type, 1) .
				dec_binary ($time, 4) .
				ParseData::encode_length_plus_data($user_id) .
				ParseData::encode_length_plus_data($race) .
				ParseData::encode_length_plus_data($country) .
				ParseData::encode_length_plus_data($latitude) .
				ParseData::encode_length_plus_data($longitude) .
				ParseData::encode_length_plus_data($host) .
				ParseData::encode_length_plus_data($face_coords) .
				ParseData::encode_length_plus_data($profile_coords) .
				ParseData::encode_length_plus_data($face_hash) .
				ParseData::encode_length_plus_data($profile_hash) .
				ParseData::encode_length_plus_data($video_type) .
				ParseData::encode_length_plus_data($video_url_id) .
				ParseData::encode_length_plus_data($node_public_key_bin) .
				ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

			$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
						UPDATE `".DB_PREFIX."my_table`
						SET `node_voting_send_request` = {$time}
						");

			$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				INSERT INTO `".DB_PREFIX."my_node_keys` (
					`public_key`,
					`private_key`,
					`my_time`
				)
				VALUES (
					0x{$node_public_key},
					'{$privatekey}',
					".time()."
				)");
			debug_print( $db->printsql()."\n".$db->getAffectedRows(), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

            wait_tx_gen($db_admin);
			insert_tx($data, $db);
		}
	}
	get_sleep();

} while(true);

?>
