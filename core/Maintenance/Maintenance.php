<?php
namespace FW\Maintenance;

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
}