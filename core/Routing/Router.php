<?php
namespace FW\Routing;

class Router {
	private array $routes = [];

	public function add(string $m, string $u,$h) {
		$this->routes[$m][$u] = $h;
	}

	public function run($req) {
		return $this->routes[$req->method][$req->uri] ?? null;
	}
}