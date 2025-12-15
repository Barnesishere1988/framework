<?php
namespace FW\Logging;

use Throwable;

class Logger
{
	public static function error(Throwable $e): void
	{
		$logDir = __DIR__ . '/../../storage/logs/';
		if (!is_dir($logDir)) {
			mkdir($logDir, 0777, true);
		}

		$file = $logDir . 'framework.log';

		$msg =
				"[" . date('Y-m-d H:i:s') . "] ERROR\n" .
				"Message: " . $e->getMessage() . "\n" .
				"File: " . $e->getFile() . "\n" .
				"Line: " . $e->getLine() . "\n" .
				"Request: " . ($_SERVER['REQUEST_METHOD'] ?? 'CLI') . " " . ($_SERVER['REQUEST_URI'] ?? '') . "\n" .
				"Trace:\n" . $e->getTraceAsString() . "\n" .
				"-------------------------------\n";

				file_put_contents($file, $msg, FILE_APPEND);
	}
}