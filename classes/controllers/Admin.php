<?php

class Admin extends \Basic\Basic {
	
	/**
	 * Главная страница админки
	 */
	public static function main() {
		$db = parent::getDb();
		$main = $db->select("SELECT * FROM main")[0];
		$users = $db->select("SELECT * FROM users");
		$allResults = $db->select("SELECT COUNT(*) FROM results")[0]['COUNT(*)'];
		$count = count($users);
		$webApp = 0;
		$results = 0;
		for ($i = 0; $i < $count; $i++) {
			if ($users[$i]['web_app'] == 1) $webApp++;
			if ($users[$i]['pro'] != '') $results++;
		}
		echo parent::loadView('templates/header.php', array());
		echo parent::loadView('templates/main.php', array(
			'main' => $main,
			'users' => $users,
			'webApp' => $webApp,
			'results' => $results,
			'allResults' => $allResults
		));
		echo parent::loadView('templates/footer.php', array());
	}
	
	/**
	 * Страница результатов прохождения тестирования
	 */
	public static function results() {
		$db = parent::getDb();
		$results = $db->select("SELECT results.*, users.username AS name FROM results LEFT JOIN users ON results.user_id = users.id");
		$professions = include('data/professions.php');
		echo parent::loadView('templates/header.php', array());
		echo parent::loadView('templates/results.php', array(
			'results' => $results,
			'professions' => $professions
		));
		echo parent::loadView('templates/footer.php', array());
	}
}