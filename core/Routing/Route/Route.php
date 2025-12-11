<?php
namespace FW\Routing\Route;

class Route
{
	public string $method;
	public string $pattern;
	public mixed  $handler;  // Closure oder "Controller@method"
	public ?string $name = null;
	public array  $middlewares = [];
	public ?CompiledRoute $compiled = null;

	public function __construct(string $method, string $pattern, mixed $handler)
	{
		$this->method  = strtoupper($method);
		$this->pattern = $pattern;
		$this->handler = $handler;
	}

	public function name(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function middleware(array|string $mw): self
	{
		if (is_string($mw)) {
			$this->middlewares[] = $mw;
		} else {
			$this->middlewares = array_merge($this->middlewares, $mw);
		}
		return $this;
	}

	public function compile(): self
	{
		$this->compiled = CompiledRoute::compile($this);
		return $this;
	}
}