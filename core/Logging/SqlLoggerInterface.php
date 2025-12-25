<?php

namespace FW\Logging;

interface SqlLoggerInterface
{
	public function log(
		string $query,
		array $params,
		float $duration,
		bool $success
	): void;
}
