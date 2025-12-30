<?php

namespace FW\Middleware;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;
use FW\Maintenance\Maintenance;

class MaintenanceMiddleware
{
	private array $allowedPrefixes = [
		'/_maintenance',
		'/_debug',
	];

	public function handle(Request $req, callable $next): Response
	{
		$path = $req->path();

		foreach ($this->allowedPrefixes as $prefix) {
			if (str_starts_with($path, $prefix)) {
				return $next();
			}
		}

		if (Maintenance::isActive() && !Maintenance::bypassAllowed($req)) {
			return new Response(
				view('maintenance'),
				503
			);
		}

		return $next();
	}
}
