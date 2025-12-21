@extends('layout')

@section('content')
<h1>Log Viewer</h1>

<?php if (empty($logs)): ?>
	<p><em>Keine Logeinträge vorhanden.</em></p>
<?php else: ?>
	<pre style="
		background:#111;
		color:#0f0;
		padding:10px;
		max-height:60vh;
		overflow:auto;
	">
<?php foreach ($logs as $line): ?>
<?= htmlspecialchars($line, ENT_QUOTES) . "\n" ?>
<?php endforeach; ?>
	</pre>
<?php endif; ?>
@endsection