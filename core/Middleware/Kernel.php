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
use FW\Controller\ErrorController;

class Kernel
{
	private Request $req;

	public function __construct(Request $req)
	{
		$this->req = $req;
	}

	public function handle(): void
	{
		/*
        |--------------------------------------------------------------------------
        | PRE-MAINTENANCE CHECK (vor Routing!)
        |--------------------------------------------------------------------------
        */
		$path = $this->req->uri ?? '';

		$maintenanceSkip = [
			'/_maintenance',
			'/_maintenance/on',
			'/_maintenance/off',
			'/_maintenance/bypass',
		];

		$skipMaintenance = false;
		foreach ($maintenanceSkip as $skip) {
			if (str_starts_with($path, $skip)) {
				$skipMaintenance = true;
				break;
			}
		}

		if (!$skipMaintenance && Maintenance::isActive()) {
			if (empty($_SESSION['maintenance_bypass'])) {
				ErrorController::error503()->send();
				return;
			}
		}

		/*
        |--------------------------------------------------------------------------
        | Router Setup
        |--------------------------------------------------------------------------
        */
		$router = new Router();

		/*
        |--------------------------------------------------------------------------
        | Maintenance UI & Control (immer erreichbar)
        |--------------------------------------------------------------------------
        */
		$router->get(
			'/_maintenance',
			fn() =>
			view('debug/maintenance_toggle')
		);

		$router->get('/_maintenance/on', function () {
			Maintenance::enable();
			return redirect('/_maintenance');
		});

		$router->get('/_maintenance/off', function () {
			Maintenance::disable();
			return redirect('/_maintenance');
		});

		$router->get('/_maintenance/bypass/{key}', function (string $key) {
			if ($key === 'letmein') {
				$_SESSION['maintenance_bypass'] = true;
				return redirect('/');
			}
			return new Response('Ungültiger Schlüssel', 403);
		});

		/*
        |--------------------------------------------------------------------------
        | DEV-ONLY Debug & Test Routes
        |--------------------------------------------------------------------------
        */
		if ((Config::get('app')['env'] ?? 'prod') === 'dev') {

			$router->get('/_debug/logs', function () {
				$logs = LogViewer::read(300);
				return view('debug/logs', ['logs' => $logs]);
			});

			$router->get(
				'/_test/layout-error',
				fn() =>
				view('test_layout_error')
			);
		}

		/*
        |--------------------------------------------------------------------------
        | Application Routes
        |--------------------------------------------------------------------------
        */
		$router->get('/', fn() => 'Startseite');

		$router->get('/test', 'DemoController@index');
		$router->get('/hello/{name:str}', 'DemoController@hello');
		$router->get('/user/{id:int}', 'UserController@show');

		$router->get(
			'/viewtest',
			fn() =>
			view('home', ['name' => 'Felix'])
		);

		$router->get(
			'/layout',
			fn() =>
			view('home', ['name' => 'Felix'])
		);

		/*
        |--------------------------------------------------------------------------
        | Theme Handling
        |--------------------------------------------------------------------------
        */
		$router->get('/themes/{name:str}', function (string $name) {
			if (\FW\Theme\Theme::set($name)) {
				return redirect('/')
					->header('X-Theme-Change', 'OK');
			}
			return new Response("Theme '$name' existiert nicht.", 404);
		});

		$router->get('/theme/clear', function () {
			\FW\Theme\Theme::clearPreview();
			return redirect('/')
				->header('X-Theme-Change', 'Cleared');
		});

		/*
        |--------------------------------------------------------------------------
        | Error Test
        |--------------------------------------------------------------------------
        */
		$router->get('/errtest', function () {
			throw new \RuntimeException('Testfehler!');
		});

		/*
        |--------------------------------------------------------------------------
        | Routing Match
        |--------------------------------------------------------------------------
        */
		$match = $router->match($this->req);

		if (isset($match['error'])) {

			if ($match['error'] === 404) {
				ErrorController::error404()->send();
				return;
			}

			if ($match['error'] === 405) {
				ErrorController::error405()->send();
				return;
			}
		}

		$route  = $match['route'];
		$params = $match['params'];

		/*
        |--------------------------------------------------------------------------
        | Middleware Pipeline (Route-Middleware)
        |--------------------------------------------------------------------------
        */
		$middlewares = $route->middlewares ?? [];

		$response = MiddlewarePipeline::run(
			$middlewares,
			$this->req,
			function () use ($route, $params) {
				return ControllerResolver::run(
					$route,
					$this->req,
					$params
				);
			}
		);

		$response->send();
	}
}
