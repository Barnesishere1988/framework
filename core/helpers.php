<?php
use FW\View\View;

function view($tpl,$vars=[]) {
	return View::make($tpl,$vars);
}