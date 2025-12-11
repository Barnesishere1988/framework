<?php
session_start();
// globaler Fehler-Handler
/*set_exception_handler(function(Throwable $e) {
	http_response_code(500);

	// Falls View-System bereits geladen ist
	if (function_exists('view')) {
		echo view('errors/500', ['error' => $e]);
	} else {
		// Fallback (extrem unwahrscheinlich)
		echo "<h1>500 Internal Server Error</h1>";
		echo "<pre>".$e."</pre>";
	}
});

// globaler Error Handler (PHP errors)
set_error_handler(function($severity, $message, $file, $line) {
	throw new ErrorException($message, 0, $severity, $file, $line);
});*/

require __DIR__.'/../bootstrap.php';

use FW\Routing\Http\Request;
use FW\Middleware\Kernel;

$req = new Request();
$kernel = new Kernel($req);
$kernel->handle();