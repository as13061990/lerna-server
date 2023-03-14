<?php

class Admin extends \Basic\Basic {
	
	/**
	 * Главная страница админки
	 */
	public static function main() {
		$db = parent::getDb();
		$main = $db->select("SELECT * FROM main")[0];
		$users = $db->select("SELECT * FROM users");

		echo parent::loadView('templates/header.php', array());
		echo parent::loadView('templates/main.php', array(
			'main' => $main,
			'users' => $users
		));
		echo parent::loadView('templates/footer.php', array());
	}
}