<?php
namespace FW\Theme;

class AssetPublisher {

	public static function publish($theme) {
		$src = __DIR__.'/../../themes/'.$theme.'/assets/';
		$dst = __DIR__.'/../../../public/themes/'.$theme.'/assets/';

		self::copyDir($src, $dst);
	}

	private static function copyDir($src, $dst) {
		if (!is_dir($dst)) mkdir($dst, 0777, true);

		foreach (scandir($src) as $f) {
			if ($f === '.' || $f === '..') continue;

			$s = $src.$f;
			$d = $dst.$f;

			if (is_dir($s)) {
				self::copyDir($s.'/', $d.'/');
			} else {
				copy($s, $d);
			}
		}
	}
}