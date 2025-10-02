<?php
// Base dinámica (ajusta una sola vez acá)
$BASE = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/');
if ($BASE === '/') $BASE = '';

$NAV_ITEMS = [
  ['href' => $BASE . '/index.php','label' => 'Inicio'],
  ['href' => $BASE . '/?r=trayecto/index','label' => 'Quiénes somos'],
  ['href' => $BASE . '/?r=agencia/index','label' => 'Agencias'],
  ['href' => $BASE . '/?r=omnibus/index','label' => 'Galería'],
  ['href' => $BASE . '/?r=historia/index','label' => 'Omnibus'],
  ['href' => $BASE . '/?r=historia/index','label' => 'Relatos'],
];
?>
<header class="nvbar" role="banner">
  <div class="nvbar__inner">
    <a class="nvbar__brand" href="<?= $BASE ?>/">
      <span class="nvbar__logo" aria-label="ONDA Vintage">
        <img
          src="<?= $BASE ?>/assets/img/logo.png"
          alt="ONDA Vintage"
          decoding="async"
          fetchpriority="high"
          style="width:140px; height:auto;">
    </a>

    <button class="nvbar__toggle" id="nvToggle" type="button"
            aria-label="Abrir menú" aria-expanded="false">☰</button>

    <nav class="nvbar__nav" id="nvMenu" role="navigation" aria-label="Navegación principal">
      <?php foreach ($NAV_ITEMS as $it): ?>
        <a class="nvbar__link" href="<?= htmlspecialchars($it['href']) ?>">
          <?= htmlspecialchars($it['label']) ?>
        </a>
      <?php endforeach; ?>
      <a class="nvbar__cta" href="<?= $BASE ?>/?r=partner">¡Juega con Onda!</a>
    </nav>
  </div>
</header>
