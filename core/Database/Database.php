<?php

namespace FW\Database;

use PDO;
use PDOStatement;
use FW\Config\Config;
use FW\Logging\Logger;

class Database
{
	protected PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Execute SELECT / INSERT / UPDATE / DELETE
	 */
	public function query(string $sql, array $params = []): PDOStatement
	{
		$start = microtime(true);

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);

		$duration = round((microtime(true) - $start) * 1000, 2);

		Logger::channel('sql', [
			'query'		=> $sql,
			'params'	=> json_encode($params, JSON_UNESCAPED_UNICODE),
			'time_ms'	=> $duration,
		]);

		$perf = Config::get('performance');

		if ($duration >= ($perf['slow_sql_ms'] ?? 100)) {
			Logger::channel('slow-sql', [
				'query'		=> $sql,
				'params'	=> $params,
				'time_ms'	=> $duration,
			]);
		}

		return $stmt;
	}

	/**
	 * Shortcut: fetchAll
	 */
	public function fetchAll(string $sql, array $params = []): array
	{
		return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Shortcut: fetch
	 */
	public function fetch(string $sql, array $params = []): ?array
	{
		$res = $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
		return $res ?: null;
	}
}
