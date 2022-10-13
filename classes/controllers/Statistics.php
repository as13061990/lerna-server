<?php

class Statistics extends \Basic\Basic {
	
	public static function openApp() {
		$db = parent::getDb();
		$db->query("UPDATE main SET open_app = open_app + 1");
	}

	public static function markOpenApp($id) {
		$db = parent::getDb();
		$db->query("UPDATE users SET web_app = {?} WHERE id = {?}", array(1, $id));
	}
}