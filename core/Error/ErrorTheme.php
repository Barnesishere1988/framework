<?php
namespace FW\Error;

use FW\Theme\Theme;

class ErrorTheme
{
	public static function colors(): array
	{
		$theme = Theme::active();

		$cssFile = __DIR__ . "/../../themes/$theme/assets/css/style.css";

		// Wenn Theme existiert und CSS lesbar ist
		if (file_exists($cssFile)) {
			return [
				'bg' => '#111',
				'fg' => '#eee',
				'accent' => '#0af',
				'error' => '#f55'
			];
		}

		// Fallback Farben
		return [
			'bg' => '#111',
			'fg' => '#eee',
			'accent' => '#0af',
			'error' => '#f55'
		];
	}
}