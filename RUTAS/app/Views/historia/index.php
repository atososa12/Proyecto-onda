<!-- app/Views/historia/index.php -->
   <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
<h1>Historias</h1>

<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>ID</th>
      <th>Título</th>
      <th>Fecha</th>
      <th>Trayecto</th>
      <th>Origen</th>
      <th>Destino</th>
      <th>Agencia</th>
      <th>Ómnibus</th>
      <th>Enlaces</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach (($historias ?? []) as $h): 
      $fechaRaw = $h['fecha'] ?? null;
      // si guardaste unix timestamp como INT:
      $fecha = is_numeric($fechaRaw) ? date('Y-m-d', (int)$fechaRaw) : ($fechaRaw ?: '—');
    ?>
      <tr>
        <td><?= htmlspecialchars($h['id']) ?></td>
        <td><?= htmlspecialchars($h['titulo'] ?? '—') ?></td>
        <td><?= htmlspecialchars($fecha) ?></td>
        <td><?= htmlspecialchars($h['trayecto_nombre'] ?? '—') ?></td>
        <td><?= htmlspecialchars($h['origen_nombre'] ?? '—') ?></td>
        <td><?= htmlspecialchars($h['destino_nombre'] ?? '—') ?></td>
        <td><?= htmlspecialchars($h['agencia_nombre'] ?? '—') ?></td>
        <td><?= htmlspecialchars($h['omnibus_nombre'] ?? '—') ?></td>
        <td>
          <?php if (!empty($h['uri_historia'])): ?>
            <a href="<?= htmlspecialchars($h['uri_historia']) ?>" target="_blank">Historia</a>
          <?php endif; ?>
          <?php if (!empty($h['uri_fotos'])): ?>
            <?= !empty($h['uri_historia']) ? ' · ' : '' ?>
            <a href="<?= htmlspecialchars($h['uri_fotos']) ?>" target="_blank">Fotos</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
