<?php
namespace FW\Middleware;

use FW\Routing\Http\Request;
use FW\Routing\Http\Response;
use FW\Maintenance\Maintenance;

class MaintenanceMiddleware
{
	/** @var string[] */
	private array $allowedPaths = [
		'/_maintenance',
		'/_maintenance/toggle',
		'/_maintenance/bypass',
		'/_maintenance/bypass/', // wichtig für Parameter
	];

	public function handle(Request $request, callable $next)
	{
		// Wartung nicht aktiv -> normal weiter
		if (!Maintenance::isActive()) {
			return $next();
		}

		$uri = rtrim($request->uri, '/');

		// Wartungs-UI & Bypass IMMER erlauben
		foreach ($this->allowedPaths as $path) {
			if (str_starts_with($uri, rtrim($path, '/'))) {
				return $next();
			}
		}

		// Bypass über Session
		if (!empty($_SESSION['maintenance_bypass']) && $_SESSION['maintenance_bypass'] === true) {
			return $next();
		}

		// Wartungsseite ausgeben
		return new Response(
			view('errors/maintenance'),
			503
		);
	}
}