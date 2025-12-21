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
}
