<?php
namespace FW\Http;

class Request {
	public string $uri;
	public string $method;

	public function __construct() {
		$this->uri = strtok($_SERVER['REQUEST_URI'],'?');
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
}