@extends('layout')

@section('content')
<h1>Debug – Logs</h1>

<form method="get" style="margin-bottom: 10px;">
	<label>
		Typ:
		<select name="type">
			<?php
			$types = ['all', 'framework', 'error', 'sql', 'plugin', 'routing'];
			foreach ($types as $t):
			?>
				<option value="<?= $t ?>" <?= ($type ?? 'all') === $t ? 'selected' : '' ?>>
					<?= $t ?>
				</option>
			<?php endforeach; ?>
		</select>
	</label>

	<button type="submit">Filtern</button>
</form>

<pre style="background:#111;color:#0f0;padding:10px;max-height:70vh;overflow:auto;">
<?php
foreach ($logs as $line) {
	echo htmlspecialchars($line, ENT_QUOTES) . "\n";
}
?>
</pre>
@endsection