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

$admin_pass = 1;

do {

	$db_stends = get_db();
	// по таблицам ходим в админской БД, т.к. таблы у всех одинаковые
	$db_admin = $db_stends[1]['db_link'];
	$asmin_encrypt_private_key = base64_decode( get_miner_private_key($db_admin));
	upd_deamon_time($db_admin);

	// ждем появление нового майнера, чтобы прогосовать за него
	$res = $db_admin->query( __FILE__, __LINE__,  __FUNCTION__,  __CLASS__, __METHOD__, "
				SELECT `id`
				FROM `".DB_PREFIX."votes_miners`
				WHERE `type` = 'user_voting' AND
							 `votes_end` = 0
				" );
	while ($row = $db_admin->fetchArray($res)) {

		$type = ParseData::findType('votes_miner');
		$time = time();
		$user_id =  1;
		$vote_id =  $row['id'];
		$result =  1;
		$comment = '111';

		$data_for_sign = "{$type},{$time},{$user_id},{$vote_id},{$result},{$comment}";
		$signature = encrypt_and_sign($admin_pass, $asmin_encrypt_private_key, $data_for_sign);

		$data = dec_binary ($type, 1) .
			dec_binary ($time, 4) .
			ParseData::encode_length_plus_data($user_id) .
			ParseData::encode_length_plus_data($vote_id) .
			ParseData::encode_length_plus_data($result) .
			ParseData::encode_length_plus_data($comment) .
			ParseData::encode_length_plus_data(ParseData::encode_length_plus_data($signature));

        wait_tx_gen($db_admin);
		insert_tx($data, $db_admin);

	}
	get_sleep();

} while(true);

?>
