<?php

class Bot extends \Basic\Basic {
	
	/**
	 * Входящая точка бота
	 */
	public static function main() {
		if ($_POST['message']['entities'][0]['type'] != 'bot_command' && $_POST['message']['from']['id'] != $_POST['message']['chat']['id']) {
			exit();
		}
		$message = $_POST['message']['text'];

		if (substr($message, 0, 6) == '/start') {
			$data = self::start();
		} else if (substr($_POST['callback_query']['data'], 0, 8) == 'referral') {
			$data = self::referral();
		} else if (substr($_POST['callback_query']['data'], 0, 12) == 'sendReferral') {
			$data = self::sendReferral();
		} else {
			$data = self::badCommand();
		}
		
		if ($data) {
			self::sendTelegram('sendMessage', $data);
		}
	}

	/**
	 * Отправка ботом сообщения
	 */
	public static function sendTelegram($method, $data, $headers = []) {
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

	/**
	 * Кнопка бота - старт
	 */
	private static function start() {
		$from = $_POST['message']['from'];
		$chat = $_POST['message']['chat']['id'];

		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);
			self::checkReferral($chat);
			global $config;

			$data = [
				'text' => "Привет! Хотите пройти тестирование и узнать, какая профессия идеально подходит именно вам?",
				'chat_id' => $chat,
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Узнать профессию',
								'web_app' => ['url' => $config['web_app']]
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}

	/**
	 * Кнопка бота - пригласить друзей
	 */
	private static function referral() {
		$from = $_POST['callback_query']['from'];

		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);
			$chat = $from['id'];

			$data = [
				'text' => "Чтобы получить промокод на дополнительную скидку, нужно направить пригласительную ссылку своим друзьям. Как только 3 ваших друга перейдут по ссылке и запустят бота, вы получите уведомление и персональный промокод со скидкой на образовательный курс! 😊\n\nВаша уникальная ссылка:\nt.me/Lerna_career_bot?start=" . $chat,
				'parse_mode' => 'html',
				'disable_web_page_preview' => true,
				'chat_id' => $chat
			];
			return $data;
		}
		return false;
	}

	/**
	 * Неизвестная команда
	 */
	private static function badCommand() {
		$from = $_POST['message']['from'];
		$chat = $_POST['message']['chat']['id'];
		
		if (is_numeric($from['id'])) {
			$user = parent::checkUser($from);
			global $config;

			$data = [
				'text' => "Пожалуйста, используйте кнопку",
				'chat_id' => $chat,
				'reply_markup' => [
					'inline_keyboard' => [
						[
							[
								'text' => 'Узнать профессию',
								'web_app' => ['url' => $config['web_app']]
							]
						]
					]
				]
			];
			return $data;
		}
		return false;
	}

	/**
	 * Проверка реферала
	 */
	private static function checkReferral($id) {
		$db = parent::getDb();
		$ref = $_POST['message']['text'];
		$referral = substr($ref, 7);

		if (is_numeric($referral)) {
			$user = $db->select("SELECT * FROM users WHERE id = {?}", array($referral))[0];

			if ($user && $id != $referral) {
				$db->query("UPDATE main SET referral = referral + 1");
				$user['referral'] += 1;
				$db->query("UPDATE users SET referral = {?} WHERE id = {?}", array($user['referral'], $user['id']));
				$settings = $db->select("SELECT * FROM main")[0];
				$code = $user['promo'];

				if ($user['referral'] > 2 && $user['promo'] == 0) {
					$code = $settings['promo'] + 1;
					$db->query("UPDATE users SET promo = {?}, time_promo = {?} WHERE id = {?}", array($code, time(), $user['id']));
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
					'Один из ваших друзей перешел по ссылке. Вместе учиться веселее.',
					"Второй друг перешел по вашей ссылке. Пригласите еще одного, чтобы получить промокод с дополнительной скидкой на обучение.",
					"Ура, ваши друзья теперь тоже с нами! Забирайте промокод с дополнительной скидкой: <code>" . $code . "</code>. Размер финальной скидки можете узнать у менеджера.",
					"Ура, ваши друзья теперь тоже с нами! Вы же не забыли про свой промокод <code>" . $code . "</code>? Успейте им воспользоваться и узнать размер финальной скидки у менеджера 😊"
				);
				$text = $user['referral'] > 4 ? $text[3] : $text[$user['referral'] - 1];

				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/professions.php');
				$profession = $professions[$portal][$vector][$index];
				$ref = $user['referral'] < 3 ? [[
					'text' => 'Пригласить еще друзей',
					'callback_data' => 'sendReferral' . $pro
				]] : [];

				$data = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
					'reply_markup' => [
						'inline_keyboard' => [
							[
								[
									'text' => 'Оформить курс',
									'url' => $profession['url']
								]
							],
							$ref
						]
					]
				];
				self::sendTelegram('sendMessage', $data);
			}
		}
	}

	/**
	 * Отправка фото
	 */
	public static function sendPhoto($id, $texure, $pro, $sendler = false) {
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
							'text' => $sendler ? 'Узнать про курс' : 'Оформить курс',
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
		return $result;
	}

	/**
	 * Отправка реферальной ссылки
	 */
	private static function sendReferral() {
		$id = $_POST['callback_query']['from']['id'];
		$data = $_POST['callback_query']['data'];
		$portal = substr($data, 14);
		$vector = substr($data, 13, 1);
		$index = substr($data, 12, 1);
		$professions = include('data/professions.php');
		$pro = $professions[$portal][$vector][$index];

		$referral = "https://t.me/Lerna_career_bot?start=" . $id;
		$data = [
			'text' => "Чтобы получить промокод на дополнительную скидку, пригласите своих друзей пройти тест. Как только 3 ваших друга перейдут по ссылке и запустят бота, вы получите уведомление и персональный промокод со скидкой на образовательный курс! 😊\n\nВаша уникальная ссылка: ". $referral,
			'chat_id' => $id,
			'parse_mode' => 'html',
			'disable_web_page_preview' => true,
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => 'Оформить курс',
							'url' => $pro['url']
						]
					]
				]
			]
		];
		return $data;
	}

	/**
	 * Отправка "трэка"
	 */
	public static function sendTrack($index, $vector, $portal, $text, $id, $url) {
		global $config;
		$path = realpath(__DIR__ . '/../../templates/images') . '/' . $portal . '/' . $vector . '.jpg';
		$data = [
			'caption' => $text,
			'chat_id' => $id,
			'photo' => new CurlFile($path),
			'parse_mode' => 'html',
			'reply_markup' => json_encode([
				'inline_keyboard' => [
					[
						[
							'text' => 'Получить консультацию',
							'url' => $url
						]
					],
					[
						[
							'text' => 'Пригласить друзей',
							'callback_data' => 'sendReferral' . $index . $vector . $portal
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