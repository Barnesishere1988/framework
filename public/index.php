<?php
require __DIR__.'/../bootstrap.php';

use FW\Http\Request;
use FW\Routing\Router;
use FW\Middleware\Kernel;

$r = new Request();
$m = new Kernel($r);
$res = $m->handle();
echo $res;