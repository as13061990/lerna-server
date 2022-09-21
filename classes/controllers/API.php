<?php

class API extends \Basic\Basic {
	
	public static function test() {
		parent::success('test');
	}

	public static function notFound() {
		parent::error(1);
	}

	
}