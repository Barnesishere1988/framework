<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
    background: <?= $colors['bg'] ?>;
    color: <?= $colors['fg'] ?>;
    padding: 40px;
    font-family: Arial, sans-serif;
    min-height: 100vh;
">
    <h1 style="color: <?= $colors['error'] ?>;">405 – Methode nicht erlaubt</h1>

    <p>Die HTTP-Methode ist für diese Route nicht zulässig.</p>

    <p style="margin-top:30px;">
        <a href="/" style="color: <?= $colors['accent'] ?>;">Zur Startseite</a>
    </p>
</div>
