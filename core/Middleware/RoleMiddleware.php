<?php
namespace FW\Middleware;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;

class RoleMiddleware
{
    private ?string $role;

    public function __construct(?string $role = null)
    {
        $this->role = $role;
    }

    public function handle(Request $req, callable $next): Response
    {
        $user = $req->user();

        if ($user === null || !$user->hasRole($this->role)) {
            return new Response('Forbidden', 403);
        }

        return $next();
    }
}
