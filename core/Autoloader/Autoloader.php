<?php
namespace FW\Autoloader;

class Autoloader {
	public static function run() {
		spl_autoload_register(function($c) {
			$p = str_replace('FW\\','',$c);
			$p = str_replace('\\','/',$p);
			$f = __DIR__.'/../'.$p.'.php';
			if (file_exists($f)) require $f;
		});
	}
}