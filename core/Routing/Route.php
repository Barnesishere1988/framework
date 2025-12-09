<?php
namespace FW\Routing;

class Route {
	public string $uri;
	public string $method;
	public $handler;
	public array $mw = [];
	public array $params = [];

	public function __construct($m,$u,$h) {
		$this->method = $m;
		$this->uri = $u;
		$this->handler = $h;
	}
}