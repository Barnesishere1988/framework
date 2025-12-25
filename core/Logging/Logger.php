<?php

namespace FW\Logging;

use Throwable;

class Logger
{
	// Zukünftige Kanäle (NOCH NICHT GENUTZT)
	protected static array $channels = [
		'framework' => 'framework.log',
		'error'			=> 'error.log',
		// 'sql'		  => 'sql.log',
		// 'routing'	=> 'routing.log',
		// 'plugin'		=> 'plugin.log',
	];

	// Zukünftige Levels (NOCH NICHT GENUTZT)
	protected static array $levels = [
		'error',
		'info',
		'debug',
	];

	public static function channel(string $name): self
	{
		// Platzhalter für Phase 6+
		return new self();
	}

	private static function logFile(string $name): string
	{
		return __DIR__ . '/../../storage/logs/' . $name;
	}

	private static function write(string $file, string $content): void
	{
		file_put_contents(
			self::logFile($file),
			$content . PHP_EOL,
			FILE_APPEND | LOCK_EX
		);
	}

	private static function format(Throwable $e): string
	{
		$req = $_SERVER['REQUEST_METHOD'] . ' ' . ($_SERVER['REQUEST_URI'] ?? '-');

		return sprintf(
			"[%s] ERROR\nMessage: %s\nFile: %s\nLine: %d\nRequest: %s\nTrace:\n%s\n-------------------------------",
			date('Y-m-d H:i:s'),
			$e->getMessage(),
			$e->getFile(),
			$e->getLine(),
			$req,
			$e->getTraceAsString()
		);
	}

	/**
	 * Zentrale ERROR-Methode
	 * -> schreibt bewusst in ZWEI Logs
	 */
	public static function error(Throwable $e): void
	{
		$entry = self::format($e);

		// 1) Framweork-Gesamtlog
		self::write('framweork.log', $entry);

		// 2) Reines Error-Log
		self::write('error.log', $entry);
	}
}
