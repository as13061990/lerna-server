<?php

class Bot extends \Basic\Basic {
	
	public static function main() {
		if ($_POST['message']['entities'][0]['type'] != 'bot_command' && $_POST['message']['from']['id'] != $_POST['message']['chat']['id']) {
			exit();
		}
		$message = $_POST['message']['text'];

		if (substr($message, 0, 6) == '/start') {
			$data = self::start();
		} else if (substr($_POST['callback_query']['data'], 0, 8) == 'referral') {
			$data = self::referral();
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
			self::checkReferral();

			$data = [
				'text' => "Привет! Хочешь пройти тестирование и узнать, какая профессия идеально подходит именно тебе?",
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

	private static function referral() {
		$from = $_POST['callback_query']['from'];

		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);
			$chat = $from['id'];

			$data = [
				'text' => "Чтобы получить промокод на дополнительную скидку, нужно направить пригласительную ссылку своим друзьям. Как только 3 ваших друга перейдут по ссылке и запустят бота, вы получите уведомление и персональный промокод со скидкой на образовательный курс! 😊\n\nВаша уникальная ссылка:\nt.me/Lerna_career_bot?start=" . $chat,
				'parse_mode' => 'html',
				'chat_id' => $chat
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
				'text' => "Пожалуйста, \nиспользуй кнопку",
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

	private static function checkReferral() {
		$db = parent::getDb();
		$ref = $_POST['message']['text'];
		$referral = substr($ref, 7);

		if (is_numeric($referral)) {
			$user = $db->select("SELECT * FROM users WHERE id = {?}", array($referral))[0];

			if ($user) {
				$db->query("UPDATE main SET referral = referral + 1");
				$user['referral'] += 1;
				$db->query("UPDATE users SET referral = {?} WHERE id = {?}", array($user['referral'], $user['id']));
				$settings = $db->select("SELECT * FROM main")[0];
				$code = $user['promo'];

				if ($user['referral'] > 2 && $user['promo'] == 0) {
					$code = $settings['promo'] + 1;
					$db->query("UPDATE users SET promo = {?}, time = {?} WHERE id = {?}", array($code, time(), $user['id']));
					$db->query("UPDATE main SET promo = {?}", array($code));
				}

				if (strlen((string) $code) === 1) {
					$code = '000' . $code;
				} else if (strlen((string) $code) === 2) {
					$code = '00' . $code;
				} else if (strlen((string) $code) === 3) {
					$code = '0' . $code;
				}
				$code = 'LernaTG' . $code;

				$text = array(
					'Один из твоих друзей перешел по ссылке – вместе учиться веселее 😉',
					"Еще один друг перешел по твоей ссылке! 🥳 \nПригласи ещё одного и получишь промокод с дополнительной ссылкой на обучение 🙂",
					"Ура, твои друзья теперь тоже с нами! Лови промокод с дополнительной скидкой: <code>" . $code . "</code>. Размер финальной скидки можешь узнать у менеджера 😊",
					"Ура, твои друзья теперь тоже с нами! Ты же не забыл про свой промокод <code>" . $code . "</code>? Успей им воспользоваться и узнай размер финальной скидки у менеджера 😊"
				);
				$text = $user['referral'] > 4 ? $text[3] : $text[$user['referral'] - 1];
				$data = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
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
				self::sendTelegram('sendMessage', $data);
			}
		}
	}

	public static function sendPhoto($id, $texure, $pro) {
		global $config;

		$path = realpath(__DIR__ . '/../../uploads') . '/' . $texure . '.png';
		$data = [
			'caption' => $pro['text'],
			'chat_id' => $id,
			'photo' => new CurlFile($path),
			'parse_mode' => 'html',
			'reply_markup' => json_encode([
				'inline_keyboard' => [
					[
						[
							'text' => 'Пригласить друзей',
							"callback_data" => "referral"
						]
					],
					[
						[
							'text' => 'Оформить курс',
							'url' => $pro['url']
						]
					]
				]
			])
		];
		$url = 'https://api.telegram.org/bot' . $config['token'] . '/sendPhoto';
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		$result = curl_exec($ch);
	}
}