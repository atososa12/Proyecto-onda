<!-- app/Views/home/index.php -->
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>ONDA Vintage</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <base href="/onda-vintage/public/">
  <link rel="stylesheet" href="assets/css/style.css?v=5">
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

      <!-- Selector de Rutas -->
      <section class="sb__section">
        <h3>Rutas</h3>
        <div id="routeChips" class="chips"></div>
      </section>

      <!-- Historias -->
      <section class="sb__section">
        <h3>Historias</h3>
        <ul id="stories" class="list"></ul>
      </section>

      <!-- Detalle de Agencia -->
      <section class="sb__section">
        <h3>Detalle</h3>
        <div id="panelInfo" class="card muted">Selecciona una agencia…</div>
      </section>

      <footer class="sb__footer">
        <button id="sbToggle" class="sb-hide-btn" type="button">Ocultar ▸</button>
      </footer>
    </aside>

    <!-- Mapa -->
    <main id="mapWrap">
      <button id="sbEdge" class="edge-toggle" type="button" aria-label="Mostrar panel">◂ Mostrar</button>
      <div id="map" aria-label="Mapa de agencias"></div>
    </main>
  </div>



  <!-- Scripts -->
<!-- Inyectar datos del controlador ANTES de script -->
<script>
  window.AG = <?= json_encode($agencias ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<!-- Leaflet SIEMPRE antes de script -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Mi JS -->
<script src="assets/js/script.js?v=7"></script>
</body>
</html>
