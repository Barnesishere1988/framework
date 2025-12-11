<?php
namespace FW\Routing\Controller;

use FW\Routing\Route\Route;
use FW\Routing\Http\Request;
use FW\Routing\Http\Response;

class ControllerResolver
{
    public static function run(Route $route, Request $req, array $params): Response
    {
        $handler = $route->handler;

        // 1. Closure
        if ($handler instanceof \Closure) {
            $body = call_user_func_array($handler, $params);
            return self::normalizeResponse($body);
        }

        // 2. Controller@method
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);

            // Autoload kümmert sich um Namespaces
            $controller = new $class();

            if (!method_exists($controller, $method)) {
                return new Response("Controller-Methode nicht gefunden", 500);
            }

            $body = call_user_func_array([$controller, $method], $params);
            return self::normalizeResponse($body);
        }

        return new Response("Ungültiger Handler", 500);
    }

    private static function normalizeResponse(mixed $data): Response
    {
        if ($data instanceof Response) {
            return $data;
        }

        // View-System liefert Strings → automatisch Response
        return new Response((string)$data);
    }
}