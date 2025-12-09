<?php
namespace FW\Middleware;

use FW\Config\Config;

class Kernel {
	private $req;

	public function __construct($r) {
		$this->req = $r;
	}

	public function handle() {
		$m = Config::get('maintenance');
		if ($m['enabled']??false) {
			if (!$_SESSION['bypass']??false) {
				return file_get_contents(
					__DIR__.'/../../public/maintenance/index.php'
				);
			}
		}
		return 'OK'; // später Router
	}
}