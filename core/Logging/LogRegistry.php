<?php

namespace FW\Logging;

class LogRegistry
{
	private static ?SqlLoggerInterface $sqlLogger = null;

	public static function sql(): SqlLoggerInterface
	{
		if (self::$sqlLogger === null) {
			self::$sqlLogger = new NullSqlLogger();
		}

		return self::$sqlLogger;
	}

	public static function setSqlLogger(SqlLoggerInterface $logger): void
	{
		self::$sqlLogger = $logger;
	}
}
