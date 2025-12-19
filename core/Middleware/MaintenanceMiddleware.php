<?php
namespace FW\Middleware;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;
use FW\Maintenance\Maintenance;

class MaintenanceMiddleware
{
	public function handle(Request $req, callable $next): Response
	{
		// Bypass erlaubt?
		if (Maintenance::isActive() && !Maintenance::isBypassed($req)) {
			return new Response(
				view('errors/maintenance'),
				503
			);
		}

		return $next();
	}
}