<?php
namespace FW\View;

use FW\Theme\Theme;

class View {

    private static array $sections = [];
    private static string $extends = '';

    public static function base() {
        return Theme::viewPath();
    }

    public static function make($tpl, $vars = []) {
    self::$sections = [];
    self::$extends = '';

    $src = self::base() . $tpl . '.php';
    if (!file_exists($src)) return "View $tpl fehlt";

    // 1. CHILD TEMPLATE KOMPILIEREN
   $compiledChild = self::compileFile($src, $vars);

    extract($vars, EXTR_SKIP);

    // 2. CHILD AUSFÜHREN
    ob_start();
    eval("?>".$compiledChild);
    $childOutput = ob_get_clean();

    // 3. Wenn KEIN extends, einfach Child zurückgeben
    if (!self::$extends) {
        return $childOutput;
    }

    // 4. LAYOUT PFAD LADEN
    $layoutPath = self::base() . self::$extends . '.php';

    if (!file_exists($layoutPath)) {
        return "Layout " . self::$extends . " fehlt";
    }

    // 5. LAYOUT KOMPILIEREN  (ENTSCHEIDEND!)
    $compiledLayout = self::compileFile($layoutPath);

    // 6. Sections bereitstellen
    $__sections = self::$sections;
    $__content = $childOutput;

    // Variablen wieder ins Layout scope extrahieren
    extract($vars, EXTR_SKIP);

    // 7. LAYOUT AUSFÜHREN  (MIT KOMPILIERTEM CODE!)
    ob_start();
    eval("?>".$compiledLayout);
    return ob_get_clean();
}


private static function compileFile(string $path, array $vars = []): string {
    $x = file_get_contents($path);

    // 1) EXTENDS (merken, Zeile entfernen)
    if (preg_match('/@extends\(\'(.+?)\'\)/', $x, $m)) {
        self::$extends = $m[1];
        $x = str_replace($m[0], '', $x);
    }

    // 2) VARIABLES zuerst ersetzen (WICHTIG!)
    $x = preg_replace('/\{\{\s*(.+?)\s*\}\}/',
        '<?= htmlspecialchars($1, ENT_QUOTES) ?>',
        $x
    );

    // 3) SECTIONS extrahieren
    preg_match_all('/@section\(\'(.+?)\'\)([\s\S]*?)@endsection/',
        $x, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        self::$sections[$m[1]] = self::evaluateSection($m[2], $vars);
        $x = str_replace($m[0], '', $x);
    }

    // 4) YIELD ersetzen
    $x = preg_replace('/@yield\(\'(.+?)\'\)/',
        '<?= $__sections["$1"] ?? "" ?>',
        $x
    );

    // 5) INCLUDE
    $x = preg_replace('/@include\(\'(.+?)\'\)/',
        '<?= FW\\View\\View::make("$1") ?>',
        $x
    );

    // 6) CONTROL STRUCTURES
    $patterns = [
        '/@if\s*\((.+?)\)/'       => '<?php if ($1): ?>',
        '/@elseif\s*\((.+?)\)/'   => '<?php elseif ($1): ?>',
        '/@else/'                 => '<?php else: ?>',
        '/@endif/'                => '<?php endif; ?>',
        '/@foreach\s*\((.+?)\)/'  => '<?php foreach ($1): ?>',
        '/@endforeach/'           => '<?php endforeach; ?>'
    ];

    foreach ($patterns as $p => $r) {
        $x = preg_replace($p, $r, $x);
    }

    return $x;
}

private static function evaluateSection($phpCode, $vars) {
    extract($vars, EXTR_SKIP);
    ob_start();
    eval("?>".$phpCode);
    return ob_get_clean();
}


}