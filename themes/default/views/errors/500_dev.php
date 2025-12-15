<h1>500 - Internat Error (DEV)</h1>

<p><strong><?=  htmlspecialchars($error->getMessage(), ENT_QUOTES) ?></strong></p>

<pre style="white-space: pre-wrap; background: #111; color: #eee; padding: 10px;">
	<?= htmlspecialchars($error->getTraceAsString(), ENT_QUOTES) ?>
</pre>