<?php

namespace FW\Logging;

use Throwable;

class Logger
{
	protected static array $channels = [
		'framework' => __DIR__ . '/../../storage/logs/framework.log',
		'error'     => __DIR__ . '/../../storage/logs/error.log',

		// Phase 6 vorbereitet
		'sql'				=> __DIR__ . '/../../storage/logs/sql.log',
		'plugin'		=> __DIR__ . '/../../storage/logs/plugin.log',
		'routing'		=> __DIR__ . '/../../storage/logs/routing.log',
	];

	/**
	 * Öffentlicher Error-Logger
	 */
	public static function error(Throwable $e): void
	{
		$file = self::resolveLogFile($e);

		self::write($file, [
			'level'   => 'ERROR',
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
			'trace'   => $e->getTraceAsString(),
			'request' => ($_SERVER['REQUEST_METHOD'] ?? 'CLI')
				. ' '
				. ($_SERVER['REQUEST_URI'] ?? '')
		]);
	}

	/**
	 * Zentrale Entscheidung: wohin wird geloggt
	 */
	protected static function resolveLogFile(Throwable $e): string
	{
		// Phase 5: Alles was Throwable ist → error.log
		return self::$channels['error'];
	}

	/**
	 * Zentrale Schreibfunktion
	 *
	 * @param string $file
	 * @param array<string,mixed> $data
	 */
	protected static function write(string $file, array $data): void
	{
		$timestamp = date('Y-m-d H:i:s');

		$output  = '[' . $timestamp . '] ' . ($data['level'] ?? 'LOG') . PHP_EOL;
		$output .= 'Message: ' . ($data['message'] ?? '') . PHP_EOL;
		$output .= 'File: ' . ($data['file'] ?? '') . PHP_EOL;
		$output .= 'Line: ' . ($data['line'] ?? '') . PHP_EOL;
		$output .= 'Request: ' . ($data['request'] ?? '') . PHP_EOL;

		if (!empty($data['trace'])) {
			$output .= "Trace:\n" . $data['trace'] . PHP_EOL;
		}

		$output .= str_repeat('-', 30) . PHP_EOL;

		file_put_contents($file, $output, FILE_APPEND | LOCK_EX);
	}
}
