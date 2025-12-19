<?php
namespace FW\Middleware;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;

class AuthMiddleware
{
    public function handle(Request $req, callable $next): Response
    {
        if ($req->user() === null) {
            return new Response('Unauthorized', 401);
        }
        return $next();
    }
}
