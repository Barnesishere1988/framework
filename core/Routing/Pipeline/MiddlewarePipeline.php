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
            // Name und Parameter trennen
            [$name, $param] = array_pad(explode(':', $mw, 2), 2, null);

            if (!isset(self::$registry[$name])) {
                return new Response("Middleware '$name' nicht registriert", 500);
            }

            $class = self::$registry[$name];
            $instance = new $class($param);

            $prev = $handler;

            $handler = function() use ($instance, $req, $prev) {
                return $instance->handle($req, $prev);
            };
        }

        return $handler();
    }
}
