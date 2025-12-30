<?php

namespace FW\Middleware;

use FW\Routing\Router;
use FW\Routing\Controller\ControllerResolver;
use FW\Routing\Pipeline\MiddlewarePipeline;
use FW\Routing\Http\Response;
use FW\Routing\Http\Request;
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
		$router = new Router();

		/*
        |--------------------------------------------------------------------------
        | DEBUG ROUTES (DEV ONLY)
        |--------------------------------------------------------------------------
        */
		$router->get('/_debug/logs', function () {
			$env = Config::get('app')['env'] ?? 'prod';

			if ($env !== 'dev') {
				http_response_code(403);
				return 'Zugriff verweigert';
			}

			$logs = LogViewer::read(300);
			return view('debug/logs', [
				'logs' => $logs,
			]);
		});

		/*
        |--------------------------------------------------------------------------
        | MAINTENANCE UI
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
        | ROUTE MATCHING
        |--------------------------------------------------------------------------
        */
		$match = $router->match($this->req);

		if (isset($match['error'])) {
			$code = $match['error'];
			$res = new Response(
				view("errors/{$code}_styled"),
				$code
			);
			$res->send();
			return;
		}

		$route  = $match['route'];
		$params = $match['params'];

		$middlewares = $route->middlewares;
		array_unshift($middlewares, 'maintenance');

		$response = MiddlewarePipeline::run(
			$middlewares,
			$this->req,
			fn() => ControllerResolver::run($route, $this->req, $params)
		);

		$response->send();
	}
}
