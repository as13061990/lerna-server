<?php

class Crontab extends \Basic\Basic {
	
	/**
	 * Маршрут для crontab. Запускается раз в 5 минут
	 */
	public static function main() {
		self::sendler5min();
		self::sendler1nf();
		self::sendler2nf();
		self::sendler3nf();
		self::sendler1f();
		self::sendler2f();
		self::sendler3f();
	}

	/**
	 * Отправка аватара спустя 5 минут, если не заполнил форму
	 */
	private static function sendler5min() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - (4 * 60 + 30);
		$timeMax = $time - (10 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 0));

		foreach ($users as $user) {
			if ($user['sendler_5_min'] == 0) {
				$db->query("UPDATE users SET sendler_5_min = {?} WHERE id = {?}", array(1, $user['id']));
				self::sendAvatar($user);
			}
		}
	}

	/**
	 * Отправка сообщения спустя сутки, если не заполнил форму
	 */
	private static function sendler1nf() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400;
		$timeMax = $time - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 0));

		foreach ($users as $user) {
			if ($user['sendler_1_nf'] == 0) {
				$db->query("UPDATE users SET sendler_1_nf = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$text = $portal === 'skillbox' ? "В чем преимущество онлайн-обучения?\nНе все знают, но сотрудники карьерного центра помогают со стажировками и трудоустройством, поэтому по статистике более 70% выпускников онлайн-курсов находят работу по специальности.\nНадеемся, вы уже сумели приступить к освоению новой профессии!\nНо если еще не было времени выбрать подходящий курс, то напоминаем про промокод <b>LernaTelegramSB</b>, который дает скидку на покупку любых онлайн-курсов от школы Skillbox\nЖелаем приятного обучения" : "В чем преимущество онлайн-обучения?\nНе все знают, но сотрудники карьерного центра помогают со стажировками и трудоустройством, поэтому по статистике более 70% выпускников онлайн-курсов находят работу по специальности.\nНадеемся, вы уже сумели приступить к освоению новой профессии!\nНо если еще не было времени выбрать подходящий курс, то напоминаем про промокод <b>LernaTelegramGB</b>, который дает скидку на покупку любых онлайн-курсов от школы Geekbrains\nЖелаем приятного обучения";
				
				$buttons = [
					'inline_keyboard' => [
						[
							[
								'text' => 'Получить консультацию',
								'url' => $profession['url']
							]
						],
						[
							[
								'text' => 'Пригласить друзей',
								'callback_data' => 'sendReferral' . $user['pro']
							]
						]
					]
				];
				$db->query("UPDATE users SET buttons = {?} WHERE id = {?}", array(json_encode($buttons), $user['id']));
				
				$message = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
					'disable_web_page_preview' => true,
					'reply_markup' => $buttons
				];
				Bot::sendTelegram('sendMessage', $message);
			}
		}
	}

	/**
	 * Отправка сообщения спустя двое суток, если не заполнил форму
	 */
	private static function sendler2nf() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400 - 86400;
		$timeMax = $time - 86400 - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 0));

		foreach ($users as $user) {
			if ($user['sendler_2_nf'] == 0) {
				$db->query("UPDATE users SET sendler_2_nf = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$vectors = include('data/vectors.php');
				$text = $vectors[$portal][$vector];
				Bot::sendTrack($index, $vector, $portal, $text, $user['id'], $profession['url']);
			}
		}
	}

	/**
	 * Отправка сообщения спустя трое суток, если не заполнил форму
	 */
	private static function sendler3nf() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400 - 86400 - 86400;
		$timeMax = $time - 86400 - 86400 - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 0));

		foreach ($users as $user) {
			if ($user['sendler_3_nf'] == 0) {
				$db->query("UPDATE users SET sendler_3_nf = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$text = $portal === 'skillbox' ? "Привет, это снова мы!😊\nОбучение на онлайн-платформе нацелено на практику: в каждом курсе — только актуальные темы, востребованные навыки и задания для их отработки. Вы сможете стать специалистом с нуля, собрать портфолио из готовых проектов и начать карьеру уже во время обучения!\nВы ведь не забыли про ваш промокод <b>LernaTelegramSB</b>?\nКстати, он дает скидку на все курсы от онлайн-школы Skillbox! Переходите на сайт и выбирайте подходящий😉" : "Привет, это снова мы!😊\nОбучение на онлайн-платформе нацелено на практику: в каждом курсе — только актуальные темы, востребованные навыки и задания для их отработки. Вы сможете стать специалистом с нуля, собрать портфолио из готовых проектов и начать карьеру уже во время обучения!\nВы ведь не забыли про ваш промокод <b>LernaTelegramGB</b>?\nКстати, он дает скидку на все курсы от онлайн-школы GeekBrains! Переходите на сайт и выбирайте подходящий😉";
				
				$buttons = [
					'inline_keyboard' => [
						[
							[
								'text' => 'Получить консультацию',
								'url' => $profession['url']
							]
						],
						[
							[
								'text' => 'Пригласить друзей',
								'callback_data' => 'sendReferral' . $user['pro']
							]
						]
					]
				];
				$db->query("UPDATE users SET buttons = {?} WHERE id = {?}", array(json_encode($buttons), $user['id']));
				
				$message = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
					'disable_web_page_preview' => true,
					'reply_markup' => $buttons
				];
				Bot::sendTelegram('sendMessage', $message);
			}
		}
	}

	/**
	 * Отправка сообщения спустя сутки, если заполнил форму
	 */
	private static function sendler1f() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400;
		$timeMax = $time - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 1));

		foreach ($users as $user) {
			if ($user['sendler_1_f'] == 0) {
				$db->query("UPDATE users SET sendler_1_f = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$text = $portal === 'skillbox' ? "А вы знали, что сотрудники карьерного центра помогают со стажировками и трудоустройством, поэтому по статистике более 70% выпускников онлайн-курсов находят работу по специальности?\nНадеемся, вы уже сумели приступить к освоению новой профессии!\nНо если еще не было времени выбрать подходящий курс, то напоминаем про промокод <b>LernaTelegramSB</b>, который дает скидку на покупку любых онлайн-курсов от школы Skillbox\nЖелаем приятного обучения" : "А вы знали, что сотрудники карьерного центра помогают со стажировками и трудоустройством, поэтому по статистике более 70% выпускников онлайн-курсов находят работу по специальности?\nНадеемся, вы уже сумели приступить к освоению новой профессии!\nНо если еще не было времени выбрать подходящий курс, то напоминаем про промокод <b>LernaTelegramGB</b>, который дает скидку на покупку любых онлайн-курсов от школы Geekbrains\nЖелаем приятного обучения";
				
				$buttons = [
					'inline_keyboard' => [
						[
							[
								'text' => 'Получить консультацию',
								'url' => $profession['url']
							]
						],
						[
							[
								'text' => 'Пригласить друзей',
								'callback_data' => 'sendReferral' . $user['pro']
							]
						]
					]
				];
				$db->query("UPDATE users SET buttons = {?} WHERE id = {?}", array(json_encode($buttons), $user['id']));
				
				$message = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
					'disable_web_page_preview' => true,
					'reply_markup' => $buttons
				];
				Bot::sendTelegram('sendMessage', $message);
			}
		}
	}

	/**
	 * Отправка сообщения спустя двое суток, если заполнил форму
	 */
	private static function sendler2f() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400 - 86400;
		$timeMax = $time - 86400 - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 1));

		foreach ($users as $user) {
			if ($user['sendler_2_f'] == 0 && $user['track'] == 0) {
				$db->query("UPDATE users SET sendler_2_f = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$vectors = include('data/vectors.php');
				$text = $vectors[$portal][$vector];
				Bot::sendTrack($index, $vector, $portal, $text, $user['id'], $profession['url']);
			}
		}
	}

	/**
	 * Отправка сообщения спустя трое суток, если заполнил форму
	 */
	private static function sendler3f() {
		$db = parent::getDb();
		$time = time();
		$timeAgo = $time - 86400 - 86400 - 86400;
		$timeMax = $time - 86400 - 86400 - 86400 - (15 * 60);
		$users = $db->select("SELECT * FROM users WHERE time_pro < {?} AND time_pro > {?} AND send_form = {?}", array($timeAgo, $timeMax, 1));

		foreach ($users as $user) {
			if ($user['sendler_3_f'] == 0) {
				$db->query("UPDATE users SET sendler_3_f = {?} WHERE id = {?}", array(1, $user['id']));
				$pro = $user['pro'];
				$portal = substr($pro, 2);
				$vector = substr($pro, 1, 1);
				$index = substr($pro, 0, 1);
				$professions = include('data/sendler.php');
				$profession = $professions[$portal][$vector][$index];
				$text = $portal === 'skillbox' ? "Привет, это снова мы!😊\nОбучение на онлайн-платформе нацелено на практику: в каждом курсе — только актуальные темы, востребованные навыки и задания для их отработки. Вы сможете стать специалистом с нуля, собрать портфолио из готовых проектов и начать карьеру уже во время обучения!\nВы ведь не забыли про ваш промокод <b>LernaTelegramSB</b>?\nКстати, он дает скидку на все курсы от онлайн-школы Skillbox! Переходите на сайт и выбирайте подходящий😉" : "Привет, это снова мы!😊\nОбучение на онлайн-платформе нацелено на практику: в каждом курсе — только актуальные темы, востребованные навыки и задания для их отработки. Вы сможете стать специалистом с нуля, собрать портфолио из готовых проектов и начать карьеру уже во время обучения!\nВы ведь не забыли про ваш промокод <b>LernaTelegramGB</b>?\nКстати, он дает скидку на все курсы от онлайн-школы GeekBrains! Переходите на сайт и выбирайте подходящий😉";
				
				$buttons = [
					'inline_keyboard' => [
						[
							[
								'text' => 'Получить консультацию',
								'url' => $profession['url']
							]
						],
						[
							[
								'text' => 'Пригласить друзей',
								'callback_data' => 'sendReferral' . $user['pro']
							]
						]
					]
				];
				$db->query("UPDATE users SET buttons = {?} WHERE id = {?}", array(json_encode($buttons), $user['id']));
				
				$message = [
					'text' => $text,
					'chat_id' => $user['id'],
					'parse_mode' => 'html',
					'disable_web_page_preview' => true,
					'reply_markup' => $buttons
				];
				Bot::sendTelegram('sendMessage', $message);
			}
		}
	}

	/**
	 * Отправка аватара
	 */
	public static function sendAvatar($user) {
		$file = realpath(__DIR__ . '/../../templates/images') . '/' . $user['texture'] . '.png';

		$pro = $user['pro'];
		$old = $user['old'];
		$id = $user['id'];
		$portal = substr($pro, 2);
		$vector = substr($pro, 1, 1);
		$index = substr($pro, 0, 1);
		$texture = $user['texture'];
		$professions = include('data/sendler.php');
		$profession = $professions[$portal][$vector][$index];
		
		if (is_numeric($user['id']) && file_exists($file)) {
			$old = $old ? 1 : 0;
			$image = $user['texture'] . $old . '-' . $vector;
			$exist = file_exists(realpath(__DIR__ . '/../../uploads') . '/' . $image . '.png');

			if (!$exist) {
				$width = 2122;
				$height = 1400;
				$gender = substr($texture, 0, 4) === 'male' ? 'male' : 'female';
				$canvas = self::createCanvas($width, $height);
				
				$bg = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/result-' . $vector . '.png');
				imagealphablending($bg, false);
				imagesavealpha($bg, true);
				self::imageCopyMergeAlpha($canvas, $bg, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($bg);

				$user = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $gender . '.png');
				imagealphablending($user, false);
				imagesavealpha($user, true);
				self::imageCopyMergeAlpha($canvas, $user, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($user);

				$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $texture . '.png');
				imagealphablending($hair, false);
				imagesavealpha($hair, true);
				self::imageCopyMergeAlpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($hair);

				if ($old) {
					$old = $gender . '-old-' . substr($texture, -1);
					$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $old . '.png');
					imagealphablending($hair, false);
					imagesavealpha($hair, true);
					self::imageCopyMergeAlpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
					ImageDestroy($hair);
				}
				imagepng($canvas, realpath(__DIR__ . '/../../uploads') . '/' . $image . '.png');
			}
			Bot::sendPhoto($id, $image, $profession, true);
		}
	}

	/**
	 * Создание холста картинки
	 */
	private static function createCanvas($width, $height) {
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);
		$col = imagecolorallocatealpha($image, 255, 255, 255, 127);
		imagefilledrectangle($image, 0, 0, $width, $height, $col);
		imagesavealpha($image, true);
		return $image;
	}
	
	/**
	 * Объединение картинок
	 */
	private static function imageCopyMergeAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
        ImageDestroy($cut);
	}
}