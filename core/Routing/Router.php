<?php
namespace FW\Routing;

use FW\Routing\Route\Route;
use FW\Routing\Route\CompiledRoute;
use FW\Routing\Http\Request;

class Router
{
    public RouteCollection $collection;
    private ?string $cacheFile = null;

    public function __construct()
    {
        $this->collection = new RouteCollection();
        $this->cacheFile = __DIR__ . '/../../storage/routes.php';

        // Lade Cache falls vorhanden
        if (file_exists($this->cacheFile)) {
            $data = include $this->cacheFile;
            if (is_array($data)) {
                foreach ($data as $method => $routes) {
                    foreach ($routes as $route) {
                        $this->collection->add($route);
                    }
                }
            }
        }
    }

    public function get(string $pattern, mixed $handler): Route
    {
        return $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, mixed $handler): Route
    {
        return $this->add('POST', $pattern, $handler);
    }

    public function add(string $method, string $pattern, mixed $handler): Route
    {
        $route = new Route($method, $pattern, $handler);
        return $this->collection->add($route);
    }

    public function match(Request $req): array|null
    {
        $method = $req->method;
        $uri    = $req->uri;

        $routes = $this->collection->getRoutes($method);

        $matchedAnyPattern = false;

        foreach ($routes as $route) {

            /** @var CompiledRoute $compiled */
            $compiled = $route->compiled;

            $params = $compiled->extractParameters($uri);

            if ($params === [] && $route->pattern !== $uri) {
                continue;
            }

            // Route matched
            $matchedAnyPattern = true;

            return [
                'route'  => $route,
                'params' => $params
            ];
        }

        // 405 handling – Methode falsch, aber URI existiert
        if ($this->uriExistsInOtherMethod($uri)) {
            return ['error' => 405];
        }

        // 404
        return ['error' => 404];
    }

    private function uriExistsInOtherMethod(string $uri): bool
    {
        foreach ($this->collection->all() as $method => $routes) {
            foreach ($routes as $route) {
                if ($route->pattern === $uri) {
                    return true;
                }
            }
        }
        return false;
    }

    public function cacheRoutes(): void
    {
        $routes = $this->collection->all();
        file_put_contents(
            $this->cacheFile,
            "<?php\nreturn " . var_export($routes, true) . ";"
        );
    }
}