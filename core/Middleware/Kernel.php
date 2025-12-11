<?php
namespace FW\Middleware;

use FW\Routing\Router;
use FW\Routing\Controller\ControllerResolver;
use FW\Routing\Pipeline\MiddlewarePipeline;
use FW\Routing\Http\Response;
use FW\Routing\Http\Request;

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

        // Routen definieren
        $router->get('/', fn() => 'Startseite');
        $router->get('/test', 'DemoController@index');
        $router->get('/hello/{name:str}', 'DemoController@hello');
        $router->get('/user/{id:int}', 'UserController@show');

        $router->get('/viewtest', fn() => view('home', ['name' => 'Felix']));
        $router->get('/layout', fn() => view('home', ['name' => 'Felix']));

        // MATCHING
        $match = $router->match($this->req);

        // 404
        if (isset($match['error']) && $match['error'] === 404) {
            $res = new Response(view('errors.404'), 404);
            $res->send();
            return;
        }

        // 405
        if (isset($match['error']) && $match['error'] === 405) {
            $res = new Response(view('errors.405'), 405);
            $res->send();
            return;
        }

        $route  = $match['route'];      // Route-Objekt
        $params = $match['params'];     // Parameter-Werte

        // MIDDLEWARES → Pipeline
        $middlewares = $route->middlewares;

        $response = MiddlewarePipeline::run(
            $middlewares,
            $this->req,
            function() use ($route, $params) {
                return ControllerResolver::run($route, $this->req, $params);
            }
        );

        $response->send();
    }
}