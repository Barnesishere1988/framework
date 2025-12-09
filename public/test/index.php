<?php
require __dir__.'/../../bootstrap.php';

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

echo "<pre>";
passthru('php '.escapeshellarg(
	__DIR__.'/../../vendor/bin/phpunit'));
echo "</pre>";