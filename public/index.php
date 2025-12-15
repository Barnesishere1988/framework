<?php
session_start();

require __DIR__.'/../bootstrap.php';

use FW\Logging\Logger;
use FW\Config\Config;

set_exception_handler(function(Throwable $e) {
    
	Logger::error($e);

	http_response_code(500);
	
	$env = Config::get('app')['env'] ?? 'prod';

	// DEV MODE -> volle Debug-Ausgabe
	if ($env === 'dev') {
		try {
			echo view('errors/500_dev_styled', ['error' => $e]);
			return;
		} catch(Throwable $inner) {
			echo "<h1>500 - Fehler beim Rendern der Fehlerseite (DEV)</h1>";
			echo "<h2>Originalfehler:</h2><pre>" . htmlspecialchars($e) . "</pre>";
			echo "<h2>Renderer-Fehler:</h2><pre>" . htmlspecialchars($inner) . "</pre>";
		}
		return;
	}

	// PROD MODE -> möglichst hübsche Seite
	try {
		echo view('errors/500_styled');
	} catch (Throwable $inner) {
		// Fallback: absolut sicher
		echo "<h1>500 - Interner Fehler</h1>";
		echo "<p>Bitte wenden Sie sich an den Administrator.</p>";
	}
});


set_error_handler(function($severity, $message, $file, $line) {
	$e = new ErrorException($message, 0, $severity, $file, $line);
	Logger::error($e);
	throw $e;
});

use FW\Routing\Http\Request;
use FW\Middleware\Kernel;

$req = new Request();
$kernel = new Kernel($req);
$kernel->handle();