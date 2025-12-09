<?php
require __DIR__.'/../../bootstrap.php';

$s = require __DIR__.'/../../config/security.php';
$h = $s['global_hash'];

if (!isset($_POST['pw'])||
 !password_verify($_POST['pw'],$h)) {
?>
<form method="post">
<input name="pw" type="password">
<button>Login</button>
</form>
<?php exit; }

$p = __DIR__.'/../../storage/logs/';

$log = $_GET['f'] ?? 'error.log';
$f = $p.$log;

echo "<a href='?f=error.log'>error</a> | ";
echo "<a href='?f=system.log'>system</a> | ";
echo "<a href='?f=debug.log'>debug</a><hr>";

echo "<pre>";
echo htmlspecialchars(file_get_contents($f));
echo "</pre>";