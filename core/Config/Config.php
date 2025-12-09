<?php
namespace FW\Config;

class Config {
	public static function get(string $key) {
		$p = __DIR__ . '/../../config/';
		foreach (glob($p.'*.php') as $f) {
			$c = include $f;
			if (array_key_exists($key,$c)) return $c[$key];
		}
		return null;
	}
}