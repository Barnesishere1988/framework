<?php

namespace FW\Logging;

interface PluginLoggerInterface
{
	public function log(
		string $event,
		string $plugin,
		array $context = []
	): void;
}
