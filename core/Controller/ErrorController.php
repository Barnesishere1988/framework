<?php

namespace FW\Controller;

use FW\Routing\Http\Response;

class ErrorController
{
	public static function error404(): Response
	{
		return new Response(
			view('errors/404'),
			404
		);
	}

	public static function error405(): Response
	{
		return new Response(
			view('errors/405'),
			405
		);
	}

	public static function error403(): Response
	{
		return new Response(
			view('errors/403'),
			403
		);
	}

	public static function error500(): Response
	{
		return new Response(
			view('errors/500'),
			500
		);
	}

	public static function error503(): Response
	{
		return new Response(
			view('maintenance'),
			503
		);
	}
}
