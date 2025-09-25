<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>ONDA Vintage</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php
    $BASE = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/');
    if ($BASE === '/') $BASE = '';
  ?>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css?v=7">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body>

  <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>

  <div id="app">
    <!-- Sidebar -->
    <aside id="sidebar" aria-label="Panel de navegación">
      <header class="sb__header">
        <button id="sbClose" class="icon-btn" aria-label="Cerrar panel" title="Cerrar">✕</button>
        <h2>Explorar ONDA</h2>
      </header>

      <!-- CONTENEDOR FIJO DEL CUERPO DEL SIDEBAR -->
      <div id="sbContent">
        <section class="sb__section">
          <h3>Rutas</h3>
          <div id="routeChips" class="chips"></div>
        </section>

        <section class="sb__section">
          <h3>Historias</h3>
          <ul id="stories" class="list"></ul>
        </section>

        <section class="sb__section">
          <h3>Detalle</h3>
          <div id="panelInfo" class="card muted">Selecciona una agencia…</div>
        </section>
      </div>

      <footer class="sb__footer">
        <button id="sbToggle" class="sb-hide-btn" type="button">Ocultar ▸</button>
      </footer>

      <!-- ======================= -->
      <!-- TEMPLATE FICHA DE RUTA  -->
      <!-- ======================= -->
      <template id="tplRouteSidebar">
        <!-- OJO: solo contenido que va dentro de #sbContent -->
        <header class="sb__header">
          <button class="icon-btn tpl-back" aria-label="Volver">←</button>
          <h2 class="tpl-title"></h2>
        </header>

        <section class="sb__section">
          <div class="card-hero tpl-hero" hidden>
            <img class="tpl-hero-img" alt="">
          </div>

          <div class="card-body">
            <p class="card-summary tpl-summary" hidden></p>
            <p class="card-lead tpl-lead" hidden></p>
            <a class="btn btn-primary tpl-link">Leer más</a>
          </div>
        </section>
      </template>
    </aside>

    <!-- Mapa -->
    <main id="mapWrap">
      <button id="sbEdge" class="edge-toggle" type="button" aria-label="Mostrar panel">◂ Mostrar</button>
      <div id="map" aria-label="Mapa de agencias"></div>
    </main>
  </div>

  <!-- Datos del backend -->
  <script>
    window.AG = <?= json_encode($agencias ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  </script>

  <!-- Leaflet -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <!-- JS propio -->
  <script type="module" src="/assets/js/app.js"></script>
</body>
</html>

