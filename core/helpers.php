<?php
use FW\View\View;
use FW\Theme\Theme;

function view($tpl,$vars=[]) {
	return View::make($tpl,$vars);
}

function theme_asset($p) {
	return Theme::asset($p);
}