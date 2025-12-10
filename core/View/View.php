<?php
namespace FW\View;

class View {
  public static string $cache = __DIR__.'/../../storage/views/compiled/';

  public static function base() {
    return \FW\Theme\Theme::viewPath();
  }

  public static function make($tpl,$vars=[]) {
    $src = self::base().$tpl.'.php';
    if (!file_exists($src)) return "View $tpl fehlt";

    $dst = self::$cache.$tpl.'.php';

    if (!file_exists($dst) || filemtime($dst) < filemtime($src))
      self::compile($src,$dst);

    extract($vars, EXTR_SKIP);

    ob_start();
    include $dst;
    return ob_get_clean();
  }

  private static function compile($src,$dst) {
    $x = file_get_contents($src);

    // {{ ... }} nur für nicht-HTML-Ausgabe
    $x = preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/',
      fn($m)=>'<?= htmlspecialchars('.$m[1].', ENT_QUOTES) ?>', $x);

    // @include('file')
    $x = preg_replace(
      '/@include\(\'(.+?)\'\)/',
      '<?= FW\View\View::make("$1") ?>',
      $x
    );

    // @if()
    $x = preg_replace('/@if\s*\((.*?)\)/','<?php if($1): ?>',$x);

    // @elseif()
    $x = preg_replace('/@elseif\s*\((.*?)\)/','<?php elseif($1): ?>',$x);

    // @else
    $x = preg_replace('/@else/','<?php else: ?>',$x);

    // @endif
    $x = preg_replace('/@endif/','<?php endif; ?>',$x);

    // @foreach()
    $x = preg_replace('/@foreach\s*\((.*?)\)/',
      '<?php foreach($1): ?>',$x);

    // @endforeach
    $x = preg_replace('/@endforeach/','<?php endforeach; ?>',$x);

    file_put_contents($dst, $x);
  }
}