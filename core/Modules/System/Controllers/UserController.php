<?php
namespace FW\Modules\System\Controllers;

class UserController {
	public function show($req, $id) {
		return "User ID: $id";
	}
}