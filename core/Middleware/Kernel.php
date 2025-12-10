<?php
namespace FW\Middleware;

use FW\Routing\Router;
use FW\Routing\ControllerResolver;

class Kernel {
	private $req;

	public function __construct($r) {
		$this->req = $r;
	}

	public function handle() {
		$r = new Router();

		// einfache Seiten
		$r->get('/', fn()=> 'Startseite');
		$r->get('/test', 'DemoController@index');

		// Parameter-Routing
		$r->get('/hello/{name}', 'DemoController@hello');
		$r->get('/user/{id}', 'UserController@show');

		$r->get('/viewtest', function() {
			return view('home', ['name'=>'Felix']);
		});

		$r->get('/theme', function() {
			return view('themetest', ['name'=>'Felix']);
		});

		
		$r->get('/layout', function() {
				return view('home', ['name' => 'Felix']);
		});

		if ($this->req->uri == '/scan') {
    	return \FW\Debug\DebugScanner::run();
		}


		$route = $r->match($this->req);
		if (!$route) return '404';

		// Callback ausführen
		if ($route->callback instanceof \Closure) {
				return ($route->callback)();
		}

		// Sonst Controller
		return ControllerResolver::run($route, $this->req);
	}
}