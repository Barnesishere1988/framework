<?php

namespace FW\Debug;

class LogViewer
{
	public static function read(int $lines = 200): array
	{
		$file = __DIR__ . '/../../storage/logs/error.log';

		if (!file_exists($file)) {
			return [];
		}

		$content = @file($file, FILE_IGNORE_NEW_LINES);

		if ($content === false) {
			return [];
		}

		return array_slice(
			array_reverse($content),
			0,
			$lines
		);
	}

	public static function readByType(string $type, int $limit = 300): array
	{
		$base = __DIR__ . '/../../storage/logs/';
		$files = [];

		switch ($type) {
			case 'framework':
				$files[] = $base . 'framework.log';
				break;
			case 'error':
				$files[] = $base . 'error.log';
				break;
			case 'sql':
				$files[] = $base . 'sql.log';
				break;
			case 'plugin':
				$files[] = $base . 'plugin.log';
				break;
			case 'routing':
				$files[] = $base . 'routing.log';
				break;
			case 'all':
			default:
				$files[] = $base . 'framework.log';
				$files[] = $base . 'error.log';
				break;
		}

		$lines = [];

		foreach ($files as $file) {
			if (!file_exists($file)) {
				continue;
			}

			$content = file($file, FILE_IGNORE_NEW_LINES);
			$lines = array_merge($lines, $content);
		}

		return array_slice($lines, -$limit);
	}
}
