<?php
namespace FW\Modules\System\Controllers;

class DemoController {
	public function index($req) {
		return "DemoController: index()";
	}

	public function hello($req, $name) {
		return "Hallo $name, aus DemoController!";
	}
}