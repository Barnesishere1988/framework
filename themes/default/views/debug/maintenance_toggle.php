<?php
$active = \FW\Maintenance\Maintenance::isActive();
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
		background: <?= $colors['bg'] ?>;
		color: <?=  $colors['fg'] ?>;
		min-height: 100vh;
		padding: 40px;
		font-family: Arial, sans-serif;
">
	<h1>Wartungsmodus</h1>

	<p>
		Status:
		<strong style="color: <?=  $active ? '#f55' : '#0f0' ?>">
			<?= $active ? 'AKTIV' : 'INAKTIV' ?>
		</strong>
	</p>

		<a href="/_maintenance/<?= $active ? 'off' : 'on' ?>"
			style="
					display:inline-block;
					padding:10px 20px;
					background: <?= $active ? '#f55' : '#0af' ?>;
					color:#fff;
					text-decoration:none;
			">
			<?= $active ? 'Wartung deaktivieren' : 'Wartung aktivieren' ?>
		</a>

	<p style="margin-top:30px; opacity:0.7;">
		Anderungen wirken sofort.
	</p>
</div>