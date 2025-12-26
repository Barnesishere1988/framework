<?php

namespace FW\Logging;

class NullPluginLogger implements PluginLoggerInterface
{
	public function log(
		string $event,
		string $plugin,
		array $context = []
	): void {
		// absichtlich leer
	}
}
