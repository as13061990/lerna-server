<?php

class API extends \Basic\Basic {
	
	/**
	 * Тестовый маршрут
	 */
	public static function test() {
		parent::success(time() - 86400 - 86400);
	}

	/**
	 * Страница не найдена
	 */
	public static function notFound() {
		parent::error(1);
	}

	/**
	 * Маршрут возврата данных пользователя
	 */
	public static function getData() {
		if (is_numeric($_POST['id'])) {
			$user = parent::checkUser($_POST);
			Statistics::openApp();

			if (!(boolean) $user['web_app']) {
				Statistics::markOpenApp($_POST['id']);
			}
			parent::success(true);
		} else {
			parent::error(2, 'bad user id');
		}
	}

	/**
	 * Маршрут результата тестирования
	 */
	public static function sendResult() {
		if (is_numeric($_POST['id'])) {
			$user = parent::checkUser($_POST);
			$portal = $_POST['skillbox'] ? 'skillbox' : 'geekbrains';
			$vector = $_POST['vector'];
			$index = $_POST['index'];
			$texture = $_POST['texture'];
			$old = $_POST['old'] ? '1' : '0';
			$pro = $index . $vector . $portal;
			$db = parent::getDb();
			$db->query("UPDATE users SET pro = {?}, time_pro = {?}, texture = {?}, old = {?} WHERE id = {?}", array($pro, time(), $texture, $old, $user['id']));
			Statistics::writeResult($user['id'], $pro);
		} else {
			parent::error(2, 'bad user id');
		}
	}

	/**
	 * Маршрут отправки аватара
	 */
	public static function sendAvatar() {
		$file = realpath(__DIR__ . '/../../templates/images') . '/' . $_POST['texture'] . '.png';

		$portal = $_POST['skillbox'] ? 'skillbox' : 'geekbrains';
		$vector = $_POST['vector'];
		$index = $_POST['index'];
		$data = include('data/professions.php');
		$profession = $data[$portal][$vector][$index];
		
		if (is_numeric($_POST['id']) && is_bool($_POST['old']) && file_exists($file) && $data) {
			$old = $_POST['old'] ? 1 : 0;
			$texture = $_POST['texture'] . $old . '-' . $_POST['vector'];
			$exist = file_exists(realpath(__DIR__ . '/../../uploads') . '/' . $texture . '.png');

			$db = parent::getDb();
			$db->query("UPDATE users SET send_form = {?} WHERE id = {?}", array(1, $_POST['id']));

			if (!$exist) {
				$width = 2122;
				$height = 1400;
				$gender = substr($_POST['texture'], 0, 4) === 'male' ? 'male' : 'female';
				$canvas = self::createCanvas($width, $height);
				
				$bg = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/result-' . $_POST['vector'] . '.png');
				imagealphablending($bg, false);
				imagesavealpha($bg, true);
				self::imageCopyMergeAlpha($canvas, $bg, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($bg);

				$user = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $gender . '.png');
				imagealphablending($user, false);
				imagesavealpha($user, true);
				self::imageCopyMergeAlpha($canvas, $user, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($user);

				$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $_POST['texture'] . '.png');
				imagealphablending($hair, false);
				imagesavealpha($hair, true);
				self::imageCopyMergeAlpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($hair);

				if ($_POST['old']) {
					$old = $gender . '-old-' . substr($_POST['texture'], -1);
					$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $old . '.png');
					imagealphablending($hair, false);
					imagesavealpha($hair, true);
					self::imageCopyMergeAlpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
					ImageDestroy($hair);
				}
				imagepng($canvas, realpath(__DIR__ . '/../../uploads') . '/' . $texture . '.png');
			}
			Bot::sendPhoto($_POST['id'], $texture, $profession);
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

	/**
	 * Маршрут кнопки "Отправить еще профессии"
	 */
	public static function moreProfessions() {
		$id = $_POST['id'];
		$portal = $_POST['portal'];
		$vector = $_POST['vector'];
		$index = $_POST['index'];
		$data = include('data/professions.php');
		$professions = $data[$portal][$vector];
		$result = [];
		$count = count($professions);

		for ($i = 0; $i < $count; $i++) {
			$pro = $professions[$i];

			if ($i !== $index) {
				array_push($result, $pro);
			}
		}
		shuffle($result);
		$referral = "https://t.me/Lerna_career_bot?start=" . $id;
		$link1 = "    1. <u><a href=\"" . $result[0]['url'] . "\">" . $result[0]['name'] . "</a></u>\n    " . $result[0]['short_descr'];
		$link2 = "    2. <u><a href=\"" . $result[1]['url'] . "\">" . $result[1]['name'] . "</a></u>\n    " . $result[1]['short_descr'];
		$message = [
			'text' => "Также вам подойдут такие профессии, как:\n" . $link1 . "\n" . $link2 . "\n\nДля получения дополнительной скидки на курс не забудьте пригласить трех друзей: " . $referral,
			'chat_id' => $id,
			'parse_mode' => 'html',
			'disable_web_page_preview' => true
		];
		Bot::sendTelegram('sendMessage', $message);
	}

	/**
	 * Маршрут кнопки "Пригласить друзей"
	 */
	public static function referral() {
		$id = $_POST['id'];
		$portal = $_POST['portal'];
		$vector = $_POST['vector'];
		$index = $_POST['index'];
		$data = include('data/professions.php');
		$pro = $data[$portal][$vector][$index];
		$referral = "https://t.me/Lerna_career_bot?start=" . $id;
		$message = [
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
		Bot::sendTelegram('sendMessage', $message);
	}

	/**
	 * Маршрут скачивания "трэка"
	 */
	public static function downloadTrack() {
		global $config;
		$id = $_POST['id'];
		$portal = $_POST['portal'];
		$vector = $_POST['vector'];
		$index = $_POST['index'];
		$professions = include('data/professions.php');
		$pro = $professions[$portal][$vector][$index];
		$vectors = include('data/vectors.php');
		$text = $vectors[$portal][$vector];
		$db = parent::getDb();
		$db->query("UPDATE users SET track = {?} WHERE id = {?}", array(1, $id));
		Bot::sendTrack($index, $vector, $portal, $text, $id, $pro['url']);
	}
}