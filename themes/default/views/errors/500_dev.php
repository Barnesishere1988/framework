@extends('layout')

@section('content')
<h1>500 - Fehler (DEV)</h1>

<p><strong><?= htmlspecialchars($error->getMessage(), ENT_QUOTES) ?></strong></p>

<pre><?= htmlspecialchars($error->getTraceAsString(), ENT_QUOTES) ?></pre>
@endsection