<?php
namespace FW\Middleware;

use FW\Routing\Router;
use FW\Routing\Controller\ControllerResolver;
use FW\Routing\Pipeline\MiddlewarePipeline;
use FW\Routing\Http\Response;
use FW\Routing\Http\Request;
use FW\Debug\LogViewer;
use FW\Config\Config;
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

        // UI anzeigen
        $router->get('/_maintenance', function () {
            return view('debug/maintenance_toggle');
        });
        $router->get('/_maintenance/on', function () {
            Maintenance::enable();
            return redirect('/_maintenance');
        });

        $router->get('/_maintenance/off', function () {
            Maintenance::disable();
            return redirect('/_maintenance');
        });


        // Routen definieren
        $router->get('/', fn() => 'Startseite');
        $router->get('/test', 'DemoController@index');
        $router->get('/hello/{name:str}', 'DemoController@hello');
        $router->get('/user/{id:int}', 'UserController@show');

        $router->get('/viewtest', fn() => view('home', ['name' => 'Felix']));


        $router->get('/layout', fn() => view('home', ['name' => 'Felix']));

        $router->get('/themes/{name:str}', function($name) {
            if (\FW\Theme\Theme::set($name)) {
                return redirect('/')->header('X-Theme-Change', 'OK');
            }
            return "Theme '$name' existiert nicht.";
        });
        $router->get('/t1', fn()=> 'ok');

        $router->get('/test404', function() {
            return view('errors/404');
        });
        $router->get('/errtest', function() {
            throw new \RuntimeException("Testfehler!");
        });
        $router->get('/themeinfo', function() {
            return '<pre>' . print_r(\FW\Theme\Theme::manifest(), true) . '</pre>';
        });

        // THEME SWITCH ROUTES
        $router->get('/theme/switch/{name:str}', function($name) {
            if (\FW\Theme\Theme::set($name)) {
                return redirect('/')
                    ->header('X-Theme-Change', 'OK');
            }

        });

        $router->get('/theme/clear', function() {
            \FW\Theme\Theme::clearPreview();
            return redirect('/')
                ->header('X-Theme-Change', 'Cleared');
        });

        $router->get('/_debug/logs', function () {
            $env = Config::get('app')['env'] ?? 'prod';

            if ($env !== 'dev') {
                http_response_code(403);
                return 'Zugriff verweigert';
            }

            $logs = LogViewer::read(300);
            return view('debug/logs', ['logs' => $logs]);
        });

        $router->get('/_maintenance/bypass/{key}', function($key) {
            if ($key === 'letmein') {
                $_SESSION['maintenance_bypass'] = true;
                return redirect('/');
            }

            return 'Ungültiger Schlüssel';
        });

        // MATCHING
        $match = $router->match($this->req);

        // 404
        if (isset($match['error']) && $match['error'] === 404) {
            $res = new Response(view('errors/404_styled'), 404);
            $res->send();
            return;
        }

        // 405
        if (isset($match['error']) && $match['error'] === 405) {
            $res = new Response(view('errors/405_styled'), 405);
            $res->send();
            return;
        }

        $route  = $match['route'];      // Route-Objekt
        $params = $match['params'];     // Parameter-Werte

        // MIDDLEWARES → Pipeline
        
        $middlewares = $route->middlewares;
        array_unshift($middlewares, 'maintenance');

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