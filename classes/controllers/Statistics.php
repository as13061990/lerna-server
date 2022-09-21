<?php

class Statistics extends \Basic\Basic {
	
	public static function openApp() {
		$db = parent::getDb();
		$db->query("UPDATE main SET open_app = open_app + 1");
	}
}