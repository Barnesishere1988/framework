@extends('layout')

@section('content')
<h1>Debug – Logs</h1>

<form method="get" style="margin-bottom: 10px;">
	<label>
		Typ:
		<select name="type">
			<option value="all">Alle</option>
			<option value="framework">Framework</option>
			<option value="error">Error</option>
			<option value="sql">SQL</option>
			<option value="plugin">Plugin</option>
			<option value="routing">Routing</option>
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