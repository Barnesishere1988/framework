<?php

namespace FW\Middleware;

use FW\Routing\Router;
use FW\Routing\Controller\ControllerResolver;
use FW\Routing\Pipeline\MiddlewarePipeline;
use FW\Routing\Http\Response;
use FW\Routing\Http\Request;
use FW\Logging\Logger;
use FW\Config\Config;
use FW\Debug\LogViewer;
use FW\Maintenance\Maintenance;

class Kernel
{
	private Request $req;

	public function __construct(Request $req)
	{
		$this->req = $req;
	}

	public function handle(): void
	{
		// --------------------------------------------------
		// REQUEST START (Phase 7.4)
		// --------------------------------------------------
		$requestStart = microtime(true);

		$router = new Router();

		/*
        |--------------------------------------------------------------------------
        | DEBUG ROUTES (DEV ONLY)
        |--------------------------------------------------------------------------
        */
		$router->get('/_debug/logs', function () {
			$env = Config::get('app')['env'] ?? 'prod';

			if ($env !== 'dev') {
				return new Response('Zugriff verweigert', 403);
			}
			$type = $_GET['type'] ?? null;
			$logs = LogViewer::readByType($type, 300);
			return view('debug/logs', ['logs' => $logs]);
		});

		/*
        |--------------------------------------------------------------------------
        | MAINTENANCE UI + TOGGLE
        |--------------------------------------------------------------------------
        */
		$router->get('/_maintenance', fn() => view('debug/maintenance_toggle'));

		$router->get('/_maintenance/on', function () {
			Maintenance::enable();
			return redirect('/_maintenance');
		});

		$router->get('/_maintenance/off', function () {
			Maintenance::disable();
			return redirect('/_maintenance');
		});

		$router->get('/_maintenance/bypass/{key}', function ($key) {
			if ($key === 'letmein') {
				$_SESSION['maintenance_bypass'] = true;
				return redirect('/');
			}

			return new Response('Ungültiger Schlüssel', 403);
		});

		/*
        |--------------------------------------------------------------------------
        | APPLICATION ROUTES
        |--------------------------------------------------------------------------
        */
		$router->get('/', fn() => 'Startseite');

		$router->get('/test', 'DemoController@index');
		$router->get('/hello/{name:str}', 'DemoController@hello');
		$router->get('/user/{id:int}', 'UserController@show');

		$router->get('/errtest', function () {
			throw new \RuntimeException('Testfehler');
		});

		/*
        |--------------------------------------------------------------------------
        | ROUTE MATCHING (Phase 7.4)
        |--------------------------------------------------------------------------
        */
		$routeMatchStart = microtime(true);
		$match = $router->match($this->req);
		$routeMatchTime = round((microtime(true) - $routeMatchStart) * 1000, 2);

		Logger::channel('routing', [
			'stage'  => 'match',
			'method' => $this->req->getMethod(),
			'path'   => $this->req->getPath(),
			'time'   => $routeMatchTime . 'ms',
		]);

		// --------------------------------------------------
		// HTTP ERRORS
		// --------------------------------------------------
		if (isset($match['error'])) {
			$code = $match['error'];

			Logger::channel('routing', [
				'stage' => 'error',
				'code'  => $code,
				'path'  => $this->req->getPath(),
			]);

			(new Response(
				view("errors/{$code}_styled"),
				$code
			))->send();

			return;
		}

		$route  = $match['route'];
		$params = $match['params'];

		/*
        |--------------------------------------------------------------------------
        | MIDDLEWARE PIPELINE
        |--------------------------------------------------------------------------
        */
		$middlewares = $route->middlewares ?? [];
		array_unshift($middlewares, 'maintenance');

		$controllerStart = microtime(true);

		$response = MiddlewarePipeline::run(
			$middlewares,
			$this->req,
			fn() => ControllerResolver::run($route, $this->req, $params)
		);

		$controllerTime = round((microtime(true) - $controllerStart) * 1000, 2);

		$handler = $route->handler;

		if ($handler instanceof \Closure) {
			$handlerName = 'closure';
		} elseif (is_string($handler)) {
			$handlerName = $handler;
		} else {
			$handlerName = gettype($handler);
		}

		Logger::channel('routing', [
			'stage'   => 'controller',
			'handler' => $handlerName,
			'time'    => $controllerTime . 'ms',
		]);


		/*
        |--------------------------------------------------------------------------
        | TOTAL REQUEST TIME (Phase 7.4)
        |--------------------------------------------------------------------------
        */
		$totalTime = round((microtime(true) - $requestStart) * 1000, 2);

		Logger::channel('request', [
			'method' => $this->req->getMethod(),
			'path'   => $this->req->getPath(),
			'time'   => $totalTime . 'ms',
		]);

		$response->send();
	}
}
