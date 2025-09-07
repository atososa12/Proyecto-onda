<!-- app/Views/home/index.php -->
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>ONDA Vintage</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

</head>
<body>
  <h1>ONDA Vintage</h1>
  <p>Pantalla inicial del proyecto. Mapa de agencias + accesos rápidos.</p>

  <!-- Mapa -->
  <div id="map" aria-label="Mapa de agencias"></div>

  <!-- Enlaces existentes -->
  <div class="grid">
    <a class="btn" href="?r=historia/index">📖 Historias</a>
    <a class="btn" href="?r=agencia/index">🏢 Agencias <small>(sólo lectura)</small></a>
    <a class="btn" href="?r=omnibus/index">🚌 Ómnibus <small>(sólo lectura)</small></a>
    <a class="btn" href="?r=trayecto/index">🧭 Trayectos <small>(sólo lectura)</small></a>
  </div>

  <!-- Scripts -->
<!-- Inyectar datos del controlador ANTES de script -->
<script>
  window.AG = <?= json_encode($agencias ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<!-- Leaflet SIEMPRE antes de script -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Mi JS -->
<script src="assets/js/script.js?v=2" defer></script>
</body>
</html>
