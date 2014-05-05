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


for ($i=0; $i<5; $i++) {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	upd_deamon_time($db_admin);
	$admin_encrypt_private_key = base64_decode( get_miner_private_key($db_admin));

	// просто проходимся по всем стендам
	foreach ($db_stends as $user_id=>$data) {

		// работаем с БД стендом
		$db = $data['db_link'];
		$stend_id = $data['stend_id'];
		$encrypt_private_key = base64_decode(get_miner_private_key($db));

		$type = ParseData::findType('message_to_admin');
		$time = time();
		$parent_id = 0;
		$subject = 'title 111';
		$message = 'hello admin';
		$message_type = 0;
		$message_subtype = 1;

		$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "SET NAMES UTF8");
		$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				INSERT INTO `".DB_PREFIX."my_admin_messages` (
						`parent_id`,
						`subject`,
						`message`,
						`message_type`,
						`message_subtype`,
						`decrypted`
					)
					VALUES (
						{$parent_id},
						'{$subject}',
						'{$message}',
						{$message_type},
						'{$message_subtype}',
						1
					)");
		$message_id = $db->getInsertId();

		$comment = '{"parent_id":"'.$parent_id.'","message_id":"'.$message_id.'","subject":"'.$subject.'","message":"'.$message.'","type":"'.$message_type.'","subtype":"'.$message_subtype.'"}';
		$admin_public_key = $db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `public_key_0`
				FROM `".DB_PREFIX."users`
				WHERE `user_id` = 1
				LIMIT 1
				", 'fetch_one' );
		if (!$admin_public_key) {
			sleep(5);
			continue;
		}
		$rsa = new Crypt_RSA();
		$rsa->loadKey($admin_public_key, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
		$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$enc =  $rsa->encrypt($comment);
		$encrypted_message = bin2hex($enc);

		$db->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
					UPDATE `".DB_PREFIX."my_admin_messages`
					SET  `status` = 'my_pending',
							`encrypted` = 0x{$encrypted_message}
					WHERE `id` = {$message_id}
					");
		$data_for_sign = "{$type},{$time},{$user_id},{$encrypted_message}";
		debug_print("data_for_sign={$data_for_sign}", __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__);

		$signature = encrypt_and_sign($stend_id, $encrypt_private_key, $data_for_sign);

		$encrypted_message = hextobin($encrypted_message);
		$data = dec_binary ($type, 1) .
			dec_binary ($time, 4) .
			ParseData::encode_length_plus_data($user_id) .
			ParseData::encode_length_plus_data($encrypted_message) .
			ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

        wait_tx_gen($db_admin);
		insert_tx($data, $db_admin);

	}
	get_sleep();

};

?>
