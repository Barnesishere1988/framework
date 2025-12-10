<?php
namespace FW\Theme;

use FW\Config\Config;

class Theme {

	public static function name() {
		return Config::get('theme')['active'];
	}

	public static function path() {
		return __DIR__.'/../../themes/'.self::name().'/';
	}

	public static function viewPath() {
		return self::path().'views/';
	}

	public static function asset($p) {
		return '/themes/'.self::name().'/assets/'.$p;
	}
}