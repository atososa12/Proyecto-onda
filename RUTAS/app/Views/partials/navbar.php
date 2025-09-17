<?php
// Define tus items acá (puedes mover esto a config si quieres)
$NAV_ITEMS = [
  ['href' => '/onda-vintage/public/',       'label' => 'Inicio'],
  ['href' => '?r=trayecto/index','label' => 'Rutas'],
  ['href' => '?r=agencia/index','label' => 'Agencias'],
  ['href' => '?r=omnibus/index','label' => 'Ómnibus'],
  ['href' => '?r=historia/index','label' => 'Historias'],
  ['href' => '/onda-vintage/public/?r=trayecto/index','label' => '¡Juega con ONDA!'],
];

$uri = $_SERVER['REQUEST_URI'] ?? '/';

?>
<header class="nvbar">
  <div class="nvbar__inner">
    <a class="nvbar__brand" href="/onda-vintage/public/">
      <span class="nvbar__logo">ON</span>
      <span class="nvbar__brand-text">ONDA Vintage</span>
    </a>

    <button class="nvbar__toggle" id="nvToggle" type="button"
        aria-label="Abrir menú" aria-expanded="false">☰</button>

    <nav class="nvbar__nav" id="nvMenu">
      <?php foreach ($NAV_ITEMS as $it): ?>
          <a class="nvbar__link" href="<?= htmlspecialchars($it['href']) ?>">
          <?= htmlspecialchars($it['label']) ?>
        </a>
      <?php endforeach; ?>
      <a class="nvbar__cta" href="/onda-vintage/public/?r=partner">Mi Cuenta</a>
    </nav>
  </div>
</header>
