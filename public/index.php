<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/../bootstrap.php';

use FW\Config\Config;
use FW\Logging\Logger;
use FW\Routing\Http\Request;
use FW\Middleware\Kernel;

/*
|--------------------------------------------------------
| Global Exception Handler
|--------------------------------------------------------
*/

set_exception_handler(function (Throwable $e) {
	Logger::error($e);

	http_response_code(500);

	$env = Config::get('app')['env'] ?? 'prod';

	if ($env === 'dev') {
		echo view('errors/500_dev', ['error' => $e]);
	} else {
		echo view('errors/500');
	}
	exit;;
});

/*
|------------------------------------------------------
| Global PHP Error handler -> Exception
|------------------------------------------------------
*/
set_error_handler(function ($severity, $message, $file, $line) {
	if (!(error_reporting() & $severity)) {
		return;
	}

	$e = new ErrorException($message, 0, $severity, $file, $line);
	Logger::error($e);
	throw $e;
});

/*
|------------------------------------------------------
| Run Application
|------------------------------------------------------
*/
$req = new Request();
$kernel = new Kernel($req);
$kernel->handle();
