<?php
App::uses('Component', 'Controller');

class PushComponent extends Component {

	public function send_push($message = '', $device_id = '', $device_type = 1) {
		if (empty($device_id)) {
			return FALSE;
		}
		if ($device_id == 'havenotoken') {
			return FALSE;
		}
		switch ($device_type) {
			case 1:	#iOS
				self::send_push_ios($message, $device_id);
				break;
			case 2:	#Android
				self::send_push_android($message, $device_id);
				break;
		}
		return TRUE;
	}

	public function send_push_ios($message = '', $device_id = '', $badge = '0'){
		$fp = '';
		$err = '';
		$errstr = '';
		$r_msg = '0';

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', Configure::read('IOS_PEM_FILE'));
		stream_context_set_option($ctx, 'ssl', 'passphrase', Configure::read('IOS_PEM_PASS'));

		// Open a connection to the APNS server
		$fp = stream_socket_client(
		Configure::read('PUSH_URL'), $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp) {
			$msg = date('Y-m-d H:i:s',time())." iOS Failed to connect: $err $errstr \r\n";
			error_log($msg, 3, '/home/thanks/www/api-thanks.me/log/gen.log');
			$r_msg = '1';
		}

		// Create the payload body
		if ($badge != '0') {
			$body['aps'] = array(
				'content-available' => 1,
				'alert' => $message,
				'sound' => 'default',
				'badge' => intval($badge)
			);
		} else {
			$body['aps'] = array(
				'content-available' => 1,
				'alert' => $message,
				'sound' => 'default'
			);
		}

		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0).pack('n', 32).pack('H*', $device_id).pack('n', strlen($payload)).$payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		if (!$result) {
			$msg = date('Y-m-d H:i:s', time())." iOS Push Failed! Device_id = $device_id \r\n";
			error_log($msg, 3, '/home/thanks/www/api-thanks.me/log/gen.log');
			$r_msg = '1';
		} else {
			$msg = date('Y-m-d H:i:s', time())." iOS Push Success! Device_id = $device_id badge = $badge\r\n";
			error_log($msg, 3, '/home/thanks/www/api-thanks.me/log/gen.log');
			$r_msg = '1';
		}
		// Close the connection to the server
		fclose($fp);

		return $r_msg;
	}


	public function send_push_android($message = '', $device_id = '') {
		// Set POST variables
		$url = 'https://android.googleapis.com/gcm/send';
		$fields = array(
			'registration_ids' => $device_id,
			'data' => $message,
		);
		$headers = array(
			'Authorization: key='.Configure::read('GOOGLE_API_KEY'),
			'Content-Type: application/json'
		);
		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Disabling SSL Certificate support temporarly
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		// Execute post
		$result = curl_exec($ch);

		if ($result === FALSE) {
			$result = 1;
			error_log(date('Y-m-d H:i:s', time())." Android Push Failed! result=$result \r\n", 3, '/home/thanks/www/api-thanks.me/log/gen.log');
			return $result;
		} else {
			error_log(date('Y-m-d H:i:s', time())." Android Push Success! result=$result\r\n", 3, '/home/thanks/www/api-thanks.me/log/gen.log');
		}

		// Close connection
		curl_close($ch);
		$result = 0;
		return $result;
	}
}
