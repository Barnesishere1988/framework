<?php

namespace FW\App;

class App
{
	protected static array $container = [];

	public static function set(string $key, mixed $value): void
	{
		self::$container[$key] = $value;
	}

	public static function get(string $key): mixed
	{
		return self::$container[$key] ?? null;
	}

	public static function has(string $key): bool
	{
		return array_key_exists($key, self::$container);
	}
}
