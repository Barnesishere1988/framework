<?php

namespace FW\Logging;

use Throwable;

class Logger
{
	/** @var string */
	protected static string $logDir;

	/**
	 * Initialisierung (einmal beim Boot)
	 */
	public static function init(string $dir): void
	{
		self::$logDir = rtrim($dir, '/');

		if (!is_dir(self::$logDir)) {
			mkdir(self::$logDir, 0777, true);
		}
	}

	/**
	 * Zentrale Channel-Funktion
	 */
	public static function channel(string $channel, array $data): void
	{
		self::write($channel, $data);
	}

	/**
	 * Error Helper
	 */
	public static function error(Throwable $e): void
	{
		self::write('error', [
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
			'trace'   => $e->getTraceAsString(),
			'request' => $_SERVER['REQUEST_METHOD'] . ' ' . ($_SERVER['REQUEST_URI'] ?? ''),
		]);
	}

	/**
	 * Interner Writer
	 */
	protected static function write(string $channel, array $data): void
	{
		if (!isset(self::$logDir)) {
			// Failsafe
			return;
		}

		$file = self::$logDir . '/' . strtolower($channel) . '.log';

		$entry  = strtoupper($channel) . PHP_EOL;
		foreach ($data as $k => $v) {
			$entry .= ucfirst($k) . ': ' . $v . PHP_EOL;
		}
		$entry .= str_repeat('-', 30) . PHP_EOL;

		file_put_contents($file, $entry, FILE_APPEND);
	}
}
