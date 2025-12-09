<?php
namespace FW\Routing;

class ControllerResolver {

	public static function run($route,$req) {
		$h = $route->handler;

		// Closure
		if (is_callable($h)) 
			return $h($req, ...$route->params);

		// "Controller@method"
		if (is_string($h) && str_contains($h,'@')) {
			[$c,$m] = explode('@',$h);
			$cls = 'FW\\Modules\\System\\Controllers\\'.$c;
			$o = new $cls;
			return $o->$m($req, ...$route->params);
		}

		// [Controller::class, 'method']
		if (is_array($h)) {
			return $h[0]::$h[1]($req, ...$route->params);
		}

		return 'Handler error';
	}
}