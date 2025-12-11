<?php
require __DIR__.'/../bootstrap.php';

use FW\Routing\Http\Request;
use FW\Middleware\Kernel;

$req = new Request();
$kernel = new Kernel($req);
$kernel->handle();