<?php

namespace FW\Logging;

class NullSqlLogger implements SqlLoggerInterface
{
	public function log(
		string $query,
		array $params,
		float $duration,
		bool $success
	): void {
		// absichtlich leer
	}
}
