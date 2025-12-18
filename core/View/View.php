<?php
namespace FW\View;

use FW\Theme\Theme;
use FW\View\ViewException;

class View {

    private static array $sections = [];
    private static string $extends = '';
    private static array $extendStack = [];

    public static function base() {
        return Theme::viewPath();
    }

    private static function normalizeName(string $tpl): string
    {
        // Punkte zu Slashes
        $tpl = str_replace('.', '/', $tpl);

        // Mehrfache Slashes entfernen
        $tpl = preg_replace('#/+#', '/', $tpl);

        // Keine relativen Pfade erlauben
        $tpl = trim($tpl, '/');
        $tpl = str_replace(['../', './'], '', $tpl);

        return $tpl;
    }

    public static function make($tpl, $vars = []) {
    self::$sections = [];
    self::$extends = '';
    self::$extendStack = [];

    // 1. Pfad im aktiven Theme berechnen
    $tpl = self::normalizeName($tpl);

    $src = null;

    foreach (\FW\Plugins\PluginManager::viewPaths() as $pluginViewPath) {
        $pluginFile = $pluginViewPath . $tpl . '.php';
        if (file_exists($pluginFile)) {
            $src = $pluginFile;
            break;
        }
    }

    if (!isset($src)) {
        $src = self::base() . $tpl . '.php';
    }

    // 2. Wenn View nicht existiert -> Fallback auf default Theme
    if (!file_exists($src)) {
        $fallback = __DIR__ . '/../../themes/default/views/' . $tpl . '.php';

        if (file_exists($fallback)) {
            $src = $fallback;
        }
    }

    if ($src === null) {
        throw new ViewException("View '$tpl' nicht gefunden");
    }

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
        // Fallback auf default Theme
        $fallback = __DIR__ . '/../../themes/default/views/' . self::$extends . '.php';

        if (file_exists($fallback)) {
            $layoutPath = $fallback;
        } else {
            throw new ViewException("Layout '" . self::$extends . "' nicht gefunden");
        }
    }

    // 5. LAYOUT KOMPILIEREN  (ENTSCHEIDEND!)
    $compiledLayout = self::compileFile($layoutPath, $vars);

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
    $x = @file_get_contents($path);
    if ($x === false) {
        throw new ViewException("View-Datei konnte nicht gelesen werden: $path");
    }

    // 1) EXTENDS (merken, Zeile entfernen)
    if (preg_match('/@extends\(\'(.+?)\'\)/', $x, $m)) {
        $parent = self::normalizeName($m[1]);

        // Mehrfach-Extends verhindern
        if (in_array($parent, self::$extendStack, true)) {
            throw new \RuntimeException(
                "Mehrfach-Extends erkannt: " . implode(' -> ', self::$extendStack) . " -> $parent"
            );
        }

        self::$extendStack[] = $parent;
        self::$extends = $parent;

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