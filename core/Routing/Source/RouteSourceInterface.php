<?php

namespace FW\Routing\Source;

use FW\Routing\Route\Route;

interface RouteSourceInterface
{
	/**
	 * @return Route[]
	 */
	public function load(): array;
}
