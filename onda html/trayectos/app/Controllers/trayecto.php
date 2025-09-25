<?php
require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Models/trayecto.php';

class TrayectoController {

  public function ver(): void {
    $slug = $_GET['slug'] ?? '';
    if ($slug === '') { http_response_code(400); echo "Falta slug"; return; }

    $t = Trayecto::getBySlug($slug);
    if (!$t) { http_response_code(404); echo "Trayecto no encontrado"; return; }

    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $articleAbs = $docRoot . $t['article_url'];

    if (!is_file($articleAbs)) { http_response_code(404); echo "Artículo no encontrado"; return; }

    $md   = file_get_contents($articleAbs);
    $html = $this->mdToHtmlBasic($md);

    require __DIR__ . '/../Views/trayecto/ver.php';
  }

  /**
   * Quita escapes de Markdown comunes: \:, \-, \*, \#, \[, \], \(, \), \!, \>, \|, \_, \`, \/, \.
   * y también limpia \\ al inicio de línea.
   */
  private function unescapeMarkdown(string $md): string {
    // 1) Normaliza BOM y saltos
    $md = preg_replace('/^\xEF\xBB\xBF/', '', $md);
    $md = str_replace(["\r\n", "\r"], "\n", $md);

    // 2) Des-escape genérico: quita "\" cuando precede a signos/puntuación de MD (incluye ":")
    //    Ejemplos: "\:" -> ":", "\-" -> "-", "\*" -> "*", "\[" -> "[", etc.
    $md = preg_replace('/\\\\([:#\-\*\#\.\(\)\[\]\{\}!_`>~|\/\\\\])/', '$1', $md);

    // 3) Si quedaron barras al inicio de línea (caso "\\Inicio"), quítalas.
    //    Mantiene el primer carácter no-espacio de la línea.
    $md = preg_replace('/^(\s*)\\\\+(\S)/m', '$1$2', $md);

    return $md;
  }

  // Parser Markdown básico y robusto
  private function mdToHtmlBasic(string $md): string {
    // ---- NUEVO: des-escapar antes de parsear ----
    $md = $this->unescapeMarkdown($md);

    // (Conservamos tu lógica de títulos/listas/inline)
    $lines  = explode("\n", $md);
    $out    = [];
    $inList = false;

    foreach ($lines as $line) {
      $t = rtrim($line);

      if ($t === '') {
        if ($inList) { $out[] = '</ul>'; $inList = false; }
        continue;
      }

      // ###, ##, #
      if (preg_match('/^\s*###\s+(.*)$/', $t, $m)) { if ($inList){$out[]='</ul>'; $inList=false;} $out[]='<h3>'.htmlspecialchars($m[1]).'</h3>'; continue; }
      if (preg_match('/^\s*##\s+(.*)$/',  $t, $m)) { if ($inList){$out[]='</ul>'; $inList=false;} $out[]='<h2>'.htmlspecialchars($m[1]).'</h2>'; continue; }
      if (preg_match('/^\s*#\s+(.*)$/',   $t, $m)) { if ($inList){$out[]='</ul>'; $inList=false;} $out[]='<h1>'.htmlspecialchars($m[1]).'</h1>'; continue; }

      // Listas - o *
      if (preg_match('/^\s*[-*]\s+(.*)$/', $t, $m)) {
        if (!$inList) { $out[] = '<ul>'; $inList = true; }
        $item = $m[1];
        // inline: links + énfasis
        $item = preg_replace_callback(
          '/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/',
          fn($m)=>'<a href="'.htmlspecialchars($m[2]).'" target="_blank" rel="noopener">'.htmlspecialchars($m[1]).'</a>',
          $item
        );
        $item = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $item);
        $item = preg_replace('/\*(.+?)\*/',     '<em>$1</em>',        $item);
        $out[] = '<li>'.$item.'</li>';
        continue;
      }

      // Párrafos con inline
      $p = $t;
      $p = preg_replace_callback(
        '/\[(.+?)\]\((https?:\/\/[^\s)]+)\)/',
        fn($m)=>'<a href="'.htmlspecialchars($m[2]).'" target="_blank" rel="noopener">'.htmlspecialchars($m[1]).'</a>',
        $p
      );
      $p = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $p);
      $p = preg_replace('/\*(.+?)\*/',     '<em>$1</em>',        $p);
      $out[] = '<p>'.$p.'</p>';
    }

    if ($inList) $out[] = '</ul>';
    return implode("\n", $out);
  }

}


