<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
		background: <?= $colors['bg'] ?>;
		color: <?= $colors['fg'] ?>;
		padding: 40px;
		font-family: Arial, sans-serif;
		min-height: 100vh;
">
	<h1 style="color: <?= $colors['error'] ?>;">500 - Interner Fehler</h1>

	<p>Es ist ein unerwarteter Fehler aufgetreten.</p>

	<p style="margin-top:30px;">
		<a href="/" style="color: <?=  $colors['accent'] ?>;">Zur Startseite</a>
	</p>
</div>