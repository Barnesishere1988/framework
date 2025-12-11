<?php
namespace FW\Routing\Route;

class Parameter
{
	public string $name;
	public string $type;

	public function __construct(string $name, string $type = 'any')
	{
		$this->name = $name;
		$this->type = $type;
	}

	public function validate(string $value): bool
	{
		return match ($this->type) {
			'int' => preg_match('/^\d+$/', $value),
			'str' => preg_match('/^[A-Za-z0-9\-_]+$/', $value),
			'any' => true,
			default => false
		};
	}

	public function cast(string $value): mixed
	{
		return match ($this->type) {
			'int' => (int)$value,
			'str' => (string)$value,
			default => $value
		};
	}
}