@extends('layout')

@section('content')
	<h1>Wartungsmodus</h1>
	<p>Die Anwendung befindet sich derzeit im Wartungsmodus.</p>

	<?php if (isset($_SESSION['maintenance_bypass'])): ?>
		<p><strong>Bypass aktiv.</strong></p>
	<?php else: ?>
		<p>Bitte versuchen Sie es später erneut.</p>
	<?php endif; ?>
@endsection