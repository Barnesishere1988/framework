<?php
namespace FW\Routing;

use FW\Routing\Route\Route;

class RouteCollection
{
	/** @var array<string,Route[]> GET => [Route,...] */
	private array $routes = [];

	/*+ @var array<string,Route> routeName => Route */
	private array $namedRoutes = [];

	public function add(Route $route): Route
	{
		$method = $route->method;

		if (!isset($this->routes[$method])) {
			$this->routes[$method] = [];
		}

		$route->compile();
		$this->routes[$method][] = $route;

		if ($route->name) {
			$this->namedRoutes[$route->name] = $route;
		}

		return $route;
	}

	public function getRoutes(string $method): array
	{
		return $this->routes[$method] ?? [];
	}

	public function getNamed(string $name): ?Route
	{
		return $this->namedRoutes[$name] ?? null;
	}

	public function all(): array
	{
		return $this->routes;
	}
}