<?php
namespace FW\Routing\Pipeline;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;

class MiddlewarePipeline
{
    public static array $registry = []; // 'auth' => AuthMiddleware::class

    public static function register(string $name, string $class): void
    {
        self::$registry[$name] = $class;
    }

    public static function run(array $middlewares, Request $req, callable $next): Response
    {
        $pipeline = array_reverse($middlewares);

        $handler = $next;

        foreach ($pipeline as $mw) {
            if (!isset(self::$registry[$mw])) {
                return new Response("Middleware '$mw' nicht registriert", 500);
            }

            $class = self::$registry[$mw];
            $instance = new $class();

            $prev = $handler;

            $handler = function() use ($instance, $req, $prev) {
                return $instance->handle($req, $prev);
            };
        }

        return $handler();
    }
}
