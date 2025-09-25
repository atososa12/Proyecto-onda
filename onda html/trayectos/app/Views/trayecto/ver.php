<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($t['nombre']) ?> | ONDA</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
    // Calcula el base real según dónde se sirva index.php
    $BASE = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/');
    if ($BASE === '/') $BASE = '';
  ?>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= $BASE ?>/assets/css/style.css?v=7">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;margin:0;color:#0f172a;background:#fff}
    .container{max-width:880px;margin:24px auto;padding:0 16px}
    .hero{margin-bottom:18px}
    .hero img{width:100%;max-height:380px;object-fit:cover;border-radius:12px}
    .article h1{font-size:clamp(28px,3.2vw,40px);margin:12px 0 8px}
    .article h2{font-size:clamp(20px,2.4vw,26px);margin:24px 0 8px}
    .article h3{font-size:clamp(18px,2vw,22px);margin:20px 0 8px}
    .article p{line-height:1.7;margin:12px 0}
    .back{display:inline-flex;gap:8px;align-items:center;text-decoration:none;color:#2563eb}
    .back:hover{text-decoration:underline}
    html, body {overflow: auto !important;}
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include __DIR__ . '/../partials/navbar.php'; ?>
  <main class="container">
    <p><a class="back" href="/">← Volver</a></p>

    <?php if (!empty($t['hero_image_url'])): ?>
      <div class="hero">
        <img src="<?= htmlspecialchars($t['hero_image_url']) ?>" alt="<?= htmlspecialchars($t['nombre']) ?>">
      </div>
    <?php endif; ?>

    <article class="article">
      <?= $html ?>
    </article>
  </main>
</body>
</html>

