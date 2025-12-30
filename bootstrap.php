<?php

require __DIR__ . '/core/Autoloader/Autoloader.php';
require __DIR__ . '/core/helpers.php';

use FW\Autoloader\Autoloader;

Autoloader::run();

use FW\Config\Config;
use FW\Theme\AssetPublisher;
use FW\Logging\Logger;

Logger::init(__DIR__ . '/storage/logs');

use FW\Database\Database;
use FW\App\App;

$dbConfig = Config::get('database');

if ($dbConfig === null) {
	throw new RuntimeException('DB-Config "database" nicht gefunden');
}

$dbConfig = $dbConfig['default'];

$pdo = new PDO(
	$dbConfig['dsn'],
	$dbConfig['user'],
	$dbConfig['pass'],
	$dbConfig['options']
);

$db = new Database($pdo);

// Hier der entscheidende Schritt
App::set('db', $db);

$theme = Config::get('theme')['active'];
AssetPublisher::publish($theme);

use FW\Plugins\PluginManager;
use FW\Routing\Pipeline\MiddlewarePipeline;
use FW\Middleware\MaintenanceMiddleware;
use FW\Middleware\RoleMiddleware;
use FW\Middleware\AuthMiddleware;

MiddlewarePipeline::register(
	'maintenance',
	MaintenanceMiddleware::class
);
MiddlewarePipeline::register('role', RoleMiddleware::class);
MiddlewarePipeline::register('auth', AuthMiddleware::class);

// Plugins automatisch laden
foreach (glob(__DIR__ . '/plugins/*', GLOB_ONLYDIR) as $pluginDir) {
	$pluginName = basename($pluginDir);
	PluginManager::register($pluginName, $pluginDir);
}
