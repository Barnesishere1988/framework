<?php
namespace FW\Theme;

use FW\Config\Config;

class Theme {
	private static ?string $active = null;

	public static function active(): string
	{
		if (self::$active !== null) {
			return self::$active;
		}

		// Session override (Preview Mode)
		if (isset($_SESSION['fw_theme_preview'])) {
			self::$active = $_SESSION['fw_theme_preview'];
			return self::$active;
		}

		// Config default
		$cfg = Config::get('theme');
		self::$active = $cfg['active'] ?? 'default';
		return self::$active;
	}

	public static function set(string $theme): bool
	{
		$path = __DIR__ . '/../../themes/' . $theme;

		if (!is_dir($path)) {
			return false;
		}

		$_SESSION['fw_theme_preview'] = $theme;
		self::$active = $theme;
		return true;
	}

	public static function clearPreview(): void
	{
		unset($_SESSION['fw_theme_preview']);
		self::$active = null;
	}

	public static function path(): string {
		return __DIR__.'/../../themes/'.self::active().'/';
	}

	public static function viewPath(): string {
		return self::path().'views/';
	}

	public static function asset(string $p): string {
		return '/themes/'.self::active().'/assets/'.$p;
	}

	public static function manifest(): array {
		$file = self::path() . 'theme.json';

		if (!file_exists($file)) {
			return [];
		}
		
		$json = file_get_contents($file);
		$data = json_decode($json, true);

		if (!is_array($data)) {
			return [];
		}

		return $data;
	}
}