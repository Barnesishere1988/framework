<?php
namespace FW\Routing;

use FW\Routing\Route;

class Router {
  private array $routes = [];

  public function add($m,$u,$h) {
    $this->routes[$m][] = new Route($m,$u,$h);
  }

  public function get($u,$h) {
    $this->add('GET',$u,$h);
  }

  public function match($req) {
    $list = $this->routes[$req->method] ?? [];
    foreach ($list as $r) {
      $pattern = preg_replace('/\{[^\/]+\}/','([^/]+)',$r->uri);
      $pattern = '#^'.$pattern.'$#';
      if (preg_match($pattern,$req->uri,$m)) {
        array_shift($m);
        $r->params = $m;
        return $r;
      }
    }
    return null;
  }
}