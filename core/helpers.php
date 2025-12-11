<?php
use FW\View\View;
use FW\Theme\Theme;

function view($tpl,$vars=[]) {
	return View::make($tpl,$vars);
}

function theme_asset($p) {
	return Theme::asset($p);
}

use FW\Routing\Http\RedirectResponse;
use FW\Routing\UrlGenerator;
use FW\Routing\Router;

function redirect(?string $to = null)
{
	if ($to !== null) {
    return new \FW\Routing\Http\RedirectResponse($to);
	}

	return new class {
		public function route(string $name, array $params = [])
		{
			$router = new Router();
			$urlGen = new UrlGenerator($router->collection);
			$url = $urlGen->route($name, $params);
			return new RedirectResponse($url);
		}
	};
}

function url()
{
	return new class {
		public function route(string $name, array $params = [])
		{
			$router = new Router();
			$urlGen = new UrlGenerator($router->collection);
			return $urlGen->route($name, $params);
		}

		public function to(string $path)
		{
			return $path;
		}
	};
}
