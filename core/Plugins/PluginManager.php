<?php
namespace FW\Plugins;

class PluginManager
{
	private static array $paths = [];

	public static function register(string $pluginName, string $path): void
	{
		self::$paths[$pluginName] = rtrim($path, '/');
	}

	public static function viewPaths(): array
	{
		$result = [];

		foreach (self::$paths as $path) {
			$viewPath = $path . '/views/';
			if (is_dir($viewPath)) {
				$result[] = $viewPath;
			}
		}

		return $result;
	}
}