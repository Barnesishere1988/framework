<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="<?= theme_asset('css/style.css') ?>">
<title><?= htmlspecialchars($title ?? 'Framework' , ENT_QUOTES) ?></title>
</head>
<body data-theme="default">
<div style="padding: 10px; background: #222; color: #fff;">
	<a href="/theme/switch/default" style="color: #0af;">Default Theme</a> |
	<a href="/theme/switch/dark" style="color: #0af;">Dark Theme</a> |
	<a href="/theme/clear" style="color: #f55;">Theme Preview entfernen</a>
</div> 
<p>Aktives Theme: <?=  \FW\Theme\Theme::active() ?></p>
@yield('content')
</body>
</html>