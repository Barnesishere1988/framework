<?php
$colors = \FW\Error\ErrorTheme::colors();
?>

<div style="
    background: <?= $colors['bg'] ?>;
    color: <?= $colors['fg'] ?>;
    padding: 30px;
    font-family: monospace;
    min-height: 100vh;
">
    <h1 style="color: <?= $colors['error'] ?>;">500 – Fehler (DEV)</h1>

    <h2><?= htmlspecialchars($error->getMessage(), ENT_QUOTES) ?></h2>

    <pre style="
        background: #000;
        padding: 20px;
        border-left: 5px solid <?= $colors['accent'] ?>;
        white-space: pre-wrap;
        margin-top: 20px;
    ">
<?= htmlspecialchars($error->getTraceAsString(), ENT_QUOTES) ?>
    </pre>
</div>
