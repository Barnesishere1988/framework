<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
		background: <?=  $colors['bg'] ?>;
		color: <?=  $colors['fg'] ?>;
		min-height: 100vh;
		padding: 50px;
		font-family: Arial, sans-serif;
">
		<h1 style="color: <?=  $colors['accent'] ?>;">Wartungsarbeiten</h1>

		<p>
			Diese Webseite befindet sich derzeit in Wartung.<br>
			Bitte versuche es später erneut.
		</p>

		<p style="margin-top:30px; opacity:0.7;">
			Status 503 - Service Unavailable
		</p>
</div>