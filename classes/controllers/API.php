<?php

class API extends \Basic\Basic {
	
	public static function test() {
		Bot::sendPhoto(771545999, 771545999);
		// parent::success('test');
	}

	public static function notFound() {
		parent::error(1);
	}

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

	public static function sendAvatar() {
		$file = realpath(__DIR__ . '/../../templates/images') . '/' . $_POST['texture'] . '.png';

		if (is_numeric($_POST['id']) && is_bool($_POST['old']) && file_exists($file)) {
			$old = $_POST['old'] ? 1 : 0;
			$texture = $_POST['texture'] . $old;
			$exist = file_exists(realpath(__DIR__ . '/../../uploads') . '/' . $texture . '.png');

			if (!$exist) {
				$gender = substr($_POST['texture'], 0, 4) === 'male' ? 'male' : 'female';

				$width = $gender === 'male' ? 398 : 380;
				$height = $gender === 'male' ? 1128 : 1166;

				$canvas = self::createCanvas($width, $height);

				$user = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $gender . '.png');
				imagealphablending($user, false);
				imagesavealpha($user, true);
				self::imagecopymerge_alpha($canvas, $user, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($user);

				$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $_POST['texture'] . '.png');
				imagealphablending($hair, false);
				imagesavealpha($hair, true);
				self::imagecopymerge_alpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
				ImageDestroy($hair);

				if ($_POST['old']) {
					$old = $gender . '-old-' . substr($_POST['texture'], -1);
					$hair = Imagecreatefrompng(realpath(__DIR__ . '/../../templates/images') . '/' . $old . '.png');
					imagealphablending($hair, false);
					imagesavealpha($hair, true);
					self::imagecopymerge_alpha($canvas, $hair, 0, 0, 0, 0, $width, $height, 100);
					ImageDestroy($hair);
				}
				imagepng($canvas, realpath(__DIR__ . '/../../uploads') . '/' . $texture . '.png');
			}
			Bot::sendPhoto($_POST['id'], $texture);
		}
	}

	private static function createCanvas($width, $height) {
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);
		$col = imagecolorallocatealpha($image, 255, 255, 255, 127);
		imagefilledrectangle($image, 0, 0, $width, $height, $col);
		imagesavealpha($image, true);
		return $image;
	}
	
	private static function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
        ImageDestroy($cut);
	}

	public static function sendResult() {
		
	}
}