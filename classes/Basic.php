<?php

namespace Basic;

class Basic extends \DB\Db {

	/**
	 * Шаблонизатор
	 */
	protected static function loadView($strViewPath, $arrayOfData) {
		extract($arrayOfData);
		ob_start();
		require($strViewPath);
		$strView = ob_get_contents();
		ob_end_clean();
		return $strView;
	}

	/**
	 * Dev log
	 */
	protected static function log($text) {
		$text = json_encode($text, JSON_UNESCAPED_UNICODE);
		file_put_contents('logs.txt', $text."\n", FILE_APPEND);
	}
	
	/**
	 * Ответ ошибки
	 */
	protected static function error($type = 0, $data = null) {
		echo json_encode(array(
			'error' => true,
			'error_type' => $type,
			'data' => $data,
		));
		exit();
	}

	/**
	 * Успешный ответ
	 */
	protected static function success($data = null) {
		if (is_array($data)) {

			foreach ($data as $key => $value) {
				if (is_numeric($value)) {
					$data[$key] = (int) $value;
				}

				if (is_array($value)) {

					foreach ($value as $key2 => $value2) {
						if (is_numeric($value2)) {
							$data[$key][$key2] = (int) $value2;
						} else if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_numeric($value3)) {
									$data[$key][$key2][$key3] = (int) $value3;
								}
							}
						}
					}
				}
			}
		}
		
		echo json_encode(array(
			'data' => $data,
			'error' => false,
			'error_type' => null,
		));
		exit();
	}

	/**
	 * Сохранение в БД юзера
	 */
	protected static function checkUser($data) {
		$db = parent::getDb();
		$user = $db->select("SELECT * FROM users WHERE id = {?}", array($data['id']))[0];

		if (!$user) {
			$username = $data['username'] ?? '';
			$name = $data['first_name'] ?? '';
			$lastname = $data['last_name'] ?? '';
			$db->query("INSERT IGNORE INTO users SET id = {?}, username = {?}, first_name = {?}, last_name = {?}", array($data['id'], $username, $name, $lastname));
			$user = $db->select("SELECT * FROM users WHERE id = {?}", array($data['id']))[0];
		}
		return $user;
	}
}

?>
