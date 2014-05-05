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

	debug_print( '$db_stends:'.print_r_hex($db_stends), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	$asmin_encrypt_private_key = base64_decode( get_miner_private_key($db_admin));

	upd_deamon_time($db_admin);

	// админ может регать любое кол-во юзерских акков
	foreach($db_stends as $user_id=>$db_stend) {

		$db = $db_stend['db_link'];
		$stend_id = $db_stend['stend_id'];

		debug_print( '$user_id:'.$user_id, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		debug_print( '$stend_id:'.$stend_id, __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

		$ok = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `block_id`
				FROM `".DB_PREFIX."my_keys`
				WHERE `block_id`>0
				", 'fetch_one' );
		if ($ok)
			continue;

		$my_keys = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT *
				FROM `".DB_PREFIX."my_keys`
				ORDER BY `my_time` DESC
				LIMIT 1
				", 'fetch_array' );
		debug_print( '$my_keys:'.print_r_hex($my_keys), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
		// если у юзера нет  своего ключа
		if (!$my_keys || ($my_keys['my_time'] < time()-NEW_USER_TIME_SEC && $my_keys['block_id']==0)) {

			// генерим приватный и паблик ключи
			$rsa = new Crypt_RSA();
			extract($rsa->createKey(2048));
			$publickey = clear_public_key($publickey);
			$priv = $rsa->_parseKey($privatekey,CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
			$aes = new Crypt_AES( CRYPT_AES_MODE_ECB );
			$aes->setKey(md5($stend_id));
			$text = $privatekey;
			$aes_encr = $aes->encrypt($text);
			$private_key = chunk_split(base64_encode($aes_encr), 64);
			$public_key = $publickey;
			$password_hash = hash('sha256', hash('sha256', $stend_id));

			// пишем в БД стенда
			$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
					INSERT INTO `".DB_PREFIX."my_keys` (
								`public_key`,
								`private_key`,
								`password_hash`,
								`my_time`
						)
						VALUES (
								0x{$public_key},
								'{$private_key}',
								'{$password_hash}',
								".time()."
						)");
			debug_print( $db->printsql()."\n".$db->getAffectedRows(), __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

			// три-ию генерит админ
			$type = ParseData::findType('new_user');
			$time = time();
			$user_id = 1;
			$stend_id = 1;
			$data_for_sign = "{$type},{$time},{$user_id},{$public_key}";
			debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);
			$signature = encrypt_and_sign('1', $asmin_encrypt_private_key, $data_for_sign);

			// создаем тр-ию с паблик-ключем
			$public_key = hextobin($public_key);
			$data = dec_binary ($type, 1) .
				dec_binary ($time, 4) .
				ParseData::encode_length_plus_data($user_id) .
				ParseData::encode_length_plus_data($public_key) .
				ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

            wait_tx_gen($db_admin);
			// пишем в админскую БД
			insert_tx($data, $db_admin);
		}
	}
	get_sleep();

} while(true);

?>
