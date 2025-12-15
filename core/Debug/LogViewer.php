<?php
namespace FW\Debug;

use FW\Config\Config;

class LogViewer
{
	public static function read(int $lines = 200): array
	{
		$dir = __DIR__ . '/../../storage/logs';
		if (!is_dir($dir)) {
			return ['error' => 'Log-Verzeichnis nicht gefunden'];
		}

		$files = glob($dir . '/*.log');
		if (!$files) {
			return ['error' => 'Keine Logdateien vorhanden'];
		}

		rsort($files); // neueste zuerst
		$file = $files[0];

		$content = file($file, FILE_IGNORE_NEW_LINES);
		if (!$content) {
			return ['error' => 'Logdatei leer'];
		}

		return array_slice($content, -$lines);
	}
}