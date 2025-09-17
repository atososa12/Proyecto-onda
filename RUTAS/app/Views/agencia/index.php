<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Agencias (sólo lectura)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Inter,Arial,sans-serif; margin:20px;}
    h1{margin:0 0 12px}
    table{width:100%; border-collapse:collapse; font-size:14px}
    th,td{border:1px solid #e5e7eb; padding:8px 10px; vertical-align:top}
    th{background:#f8fafc; text-align:left; white-space:nowrap}
    td.small{color:#666; font-size:12px}
    img.thumb{max-width:90px; border-radius:6px}
    .actions a{text-decoration:none; color:#2563eb}
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
  <h1>Agencias</h1>
  <p class="small">Listado de todas las agencias (solo lectura). <a href="?r=home/index">Volver</a></p>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Trayecto</th>
        <th>Km</th>
        <th>Ubicación (lat,lng)</th>
        <th>Foto</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (($agencias ?? []) as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['id']) ?></td>
          <td><?= htmlspecialchars($a['nombre'] ?? '—') ?></td>
          <td><?= htmlspecialchars($a['trayecto_nombre'] ?? '—') ?></td>
          <td><?= isset($a['km_en_ruta']) ? htmlspecialchars($a['km_en_ruta']) : '—' ?></td>
          <td class="small"><?= htmlspecialchars($a['ubicacion'] ?? '—') ?></td>
          <td>
            <?php if (!empty($a['foto'])): ?>
              <img class="thumb" src="<?= htmlspecialchars($a['foto']) ?>" alt="">
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($agencias)): ?>
        <tr><td colspan="6">No hay agencias cargadas.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
