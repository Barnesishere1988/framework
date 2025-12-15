<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
		background: <?=  $colors['bg'] ?>;
		color: <?=  $colors['fg'] ?>;
		padding: 40px;
		font-family: Arial, sans-serif;
		min-height: 100vh;
">
	<h1 style="color: <?=  $colors['accent'] ?>;">404 - Seite nicht gefunden</h1>

	<p>Die angeforderte Seite existiert nicht oder wurde verschoeben.</p>

	<p style="margin-top:30px;">
		<a href="/" style="color: <?=  $colors['accent'] ?>;">Zur Startseite</a>
	</p>
</div>