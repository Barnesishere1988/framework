<?php

namespace FW\Routing\Source;

use FW\Routing\Route\Route;

class FileRouteSource implements RouteSourceInterface
{
	/** @var Route[] */
	private array $routes = [];

	public function add(Route $route): void
	{
		$this->routes[] = $route;
	}

	public function load(): array
	{
		return $this->routes;
	}
}
