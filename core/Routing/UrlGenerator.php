<?php
namespace FW\Routing;

use FW\Routing\Route\Route;

class UrlGenerator
{
	private RouteCollection $collection;

	public function __construct(RouteCollection $collection)
	{
		$this->collection = $collection;
	}

	public function route(string $name, array $params = []): string
	{
		$route = $this->collection->getNamed($name);

		if (!$route) {
			throw new \Exception("Route '{$name}' nicht gefunden.");
		}

		$pattern = $route->pattern;

		foreach ($params as $key => $value) {
			$pattern = preg_replace(
					'/\{'.$key.'(:[a-z]+)?\]/',
					$value,
					$pattern
			);
		}

		return $pattern;
	}

	public function to(string $path): string
	{
		return $path;
	}
}