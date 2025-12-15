<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
		background: <?=  $colors['bg'] ?>;
		color: <?=  $colors['fg'] ?>;
		padding: 30px;
		font-family: monospace;
		min-height: 100vh;
">
		<h1 style="color: <?=  $colors['accent'] ?>;">Log Viewer</h1>

		<pre style="
				background: #000;
				padding: 20px;
				overflow-x: auto;
				white-space: pre-wrap;
				border-left: 5px solid <?= $colors['accent'] ?>;
		">
	<?php
	foreach ($logs as $line) {
		echo htmlspecialchars($line, ENT_QUOTES) . "\n";
	}	
	?>
	</pre>
</div>