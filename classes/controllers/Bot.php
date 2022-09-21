<?php

class Bot extends \Basic\Basic {
	
	public static function main() {
		if ($_POST['message']['entities'][0]['type'] != 'bot_command' && $_POST['message']['from']['id'] != $_POST['message']['chat']['id']) {
			exit();
		}
		$message = $_POST['message']['text'];

		if ($message == '/start') {
			$data = self::start();
		} else {
			$data = self::badCommand();
		}
		
		if ($data) {
			self::sendTelegram('sendMessage', $data);
		}
	}

	private static function sendTelegram($method, $data, $headers = []) {
		global $config;
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'https://api.telegram.org/bot' . $config['token'] . '/' . $method,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
		]);
		$result = curl_exec($curl);
		curl_close($curl);
		return json_decode($result, true);
	}

	private static function start() {
		$from = $_POST['message']['from'];
		$chat = $_POST['message']['chat']['id'];

		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);

			$data = [
				'text' => "Приветственный копирайт",
				'chat_id' => $chat,
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Узнать профессию',
								'web_app' => ['url' => 'https://lerna-client.irsapp.ru']
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}

	private static function badCommand() {
		$from = $_POST['message']['from'];
		$chat = $_POST['message']['chat']['id'];
		
		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);

			$data = [
				'text' => "Приветственный копирайт",
				'chat_id' => $chat,
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Узнать профессию',
								'web_app' => ['url' => 'https://lerna-client.irsapp.ru']
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}
}