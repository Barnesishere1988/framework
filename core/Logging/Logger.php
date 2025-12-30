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

			// 🔧 WERT NORMALISIEREN
			if (is_array($v)) {
				$v = json_encode(
					$v,
					JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				);
			} elseif (is_object($v)) {
				// z. B. Closure, Exception, DTO
				if ($v instanceof \Throwable) {
					$v = $v->getMessage();
				} else {
					$v = get_class($v);
				}
			} elseif (is_bool($v)) {
				$v = $v ? 'true' : 'false';
			} elseif ($v === null) {
				$v = 'null';
			}

			$entry .= ucfirst($k) . ': ' . $v . PHP_EOL;
		}

		$entry .= str_repeat('-', 30) . PHP_EOL;

		file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
	}
}
