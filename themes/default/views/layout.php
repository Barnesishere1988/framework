<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="<?= theme_asset('css/style.css') ?>">
<title>{{ $title ?? 'Framework' }}</title>
</head>
<body data-theme="default">
@yield('content')
</body>
</html>