<?php

class Statistics extends \Basic\Basic {
	
	/**
	 * Запись в статистику открытий приложений
	 */
	public static function openApp() {
		$db = parent::getDb();
		$db->query("UPDATE main SET open_app = open_app + 1");
	}

	/**
	 * Запись в статистику первичного открытия приложения пользователем
	 */
	public static function markOpenApp($id) {
		$db = parent::getDb();
		$db->query("UPDATE users SET web_app = {?} WHERE id = {?}", array(1, $id));
	}
}