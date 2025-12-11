<?php
namespace FW\Routing\Route;

class CompiledRoute
{
	public string $regex;
	public array  $parameters = []; // list of Paramter objects

	public static function compile(Route $route): self
	{
		$obj = new self();

		$pattern = preg_replace_callback(
				'/\{([a-zA-Z_][a-zA-Z0-9_]*)(:([a-z]+))?\}/',
				function ($m) use ($obj) {
					$name = $m[1];
					$type = $m[3] ?? 'any';

					$param = new Parameter($name, $type);
					$obj->parameters[] = $param;

					$regex = match ($type) {
						'int' => '(\d+)',
						'str' => '([A-Za-z0-9\-_]+)',
						'any' => '(.+)',
						default => '(.+)',
					};

					return $regex;
				},
				$route->pattern
			);

			$obj->regex = '#^' . $pattern . '$#';

			return $obj;
	}

	public function extractParameters(string $uri): array
	{
		$values = [];

		if (!preg_match($this->regex, $uri, $matches)) {
			return [];
		}

		array_shift($matches);

		foreach ($this->parameters as $i => $param) {
			$value = $matches[$i];

			if (!$param->validate($value)) {
				return []; // Typfehler -> kein Match
			}

			$values[$param->name] = $param->cast($value);
		}

		return $values;
	}
}