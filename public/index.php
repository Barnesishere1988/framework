<?php
require __DIR__.'/../bootstrap.php';

use FW\Http\Request;
use FW\Middleware\Kernel;

$req = new Request();
$kernel = new Kernel($req);
$res = $kernel->handle();
echo $res;