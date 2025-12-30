<?php

namespace FW\Maintenance;

use FW\Routing\Http\Request;

class Maintenance
{
	public static function flagPath(): string
	{
		return __DIR__ . '/../../storage/maintenance.flag';
	}

	public static function isActive(): bool
	{
		return file_exists(self::flagPath());
	}

	public static function enable(): void
	{
		file_put_contents(self::flagPath(), 'ON');
	}

	public static function disable(): void
	{
		if (file_exists(self::flagPath())) {
			unlink(self::flagPath());
		}
	}

	public static function isBypassed(Request $req): bool
	{
		return isset($_SESSION['maintenance_bypass']) &&
			$_SESSION['maintenance_bypass'] === true;
	}

	public static function bypassAllowed(\FW\Routing\Http\Request $req): bool
	{
		// Session-Bypass (z. B. /_maintenance/bypass/letmein)
		if (!empty($_SESSION['maintenance_bypass'])) {
			return true;
		}

		return false;
	}
}
