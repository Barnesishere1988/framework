<?php

namespace FW\Logging;

class LogRegistry
{
	private static ?SqlLoggerInterface $sqlLogger = null;
	private static ?PluginLoggerInterface $pluginLogger = null;

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

	public static function plugin(): PluginLoggerInterface
	{
		if (self::$pluginLogger === null) {
			self::$pluginLogger = new NullPluginLogger();
		}

		return self::$pluginLogger;
	}

	public static function setPluginLogger(PluginLoggerInterface $logger): void
	{
		self::$pluginLogger = $logger;
	}
}
