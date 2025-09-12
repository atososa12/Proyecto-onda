<?php
require_once dirname(__DIR__).'/Database.php';

class Agencia {
public static function allForMap(): array {
    $pdo = Database::get();
    $sql = "
      SELECT
        a.id,
        a.nombre,
        a.ubicacion,
        a.link_foto_agencia AS foto,

        MIN(ta.km_en_ruta) AS km_principal,  -- si tiene varias rutas, tomamos el km mínimo (solo para mostrar)
        CASE WHEN COUNT(DISTINCT t.id) = 1 THEN MAX(t.nombre) END AS ruta_unica,
        GROUP_CONCAT(DISTINCT t.nombre ORDER BY ta.km_en_ruta SEPARATOR ', ') AS rutas_lista
      FROM agencia a
      LEFT JOIN trayecto_agencia ta ON ta.agencia_id = a.id
      LEFT JOIN trayecto t          ON t.id = ta.trayecto_id
      GROUP BY a.id, a.nombre, a.ubicacion, a.link_foto_agencia
      ORDER BY a.nombre
    ";
    $rows = $pdo->query($sql)->fetchAll();

    $out = [];
    foreach ($rows as $r) {
        if (empty($r['ubicacion'])) continue;
        $p = array_map('trim', explode(',', $r['ubicacion']));
        if (count($p) !== 2) continue;
        [$lat, $lng] = $p;

        $out[] = [
            'id'     => (int)$r['id'],
            'nombre' => $r['nombre'],
            'lat'    => (float)$lat,
            'lng'    => (float)$lng,
            'foto'   => $r['foto'] ?? null,

            // claves que ya usa tu popup:
            'ruta'   => $r['ruta_unica'] ?: null,
            'km'     => $r['km_principal'] !== null ? (float)$r['km_principal'] : null,

            // por si querés mostrar todas las rutas cuando sean múltiples
            'rutas'  => $r['rutas_lista'] ?: null,
        ];
    }
    return $out;
}


    // Ruta: ordenadas por km + coords; incluye alias km
public static function forTrayectoOrderedWithRuta(int $trayectoId): array {
    $pdo = Database::get();
    $st = $pdo->prepare("
        SELECT a.id, a.nombre, a.ubicacion, ta.km_en_ruta AS km
        FROM trayecto_agencia ta
        JOIN agencia a ON a.id = ta.agencia_id
        WHERE ta.trayecto_id = ?
        ORDER BY (ta.km_en_ruta IS NULL), ta.km_en_ruta ASC, a.nombre ASC
    ");
    $st->execute([$trayectoId]);
    $rows = $st->fetchAll();

    $out = [];
    foreach ($rows as $r) {
        if (empty($r['ubicacion'])) continue;
        [$lat, $lng] = array_map('trim', explode(',', $r['ubicacion']));
        $out[] = [
            'id'     => (int)$r['id'],
            'nombre' => $r['nombre'],
            'lat'    => (float)$lat,
            'lng'    => (float)$lng,
            'km'     => $r['km'] !== null ? (float)$r['km'] : null,
        ];
    }
    return $out;
}


    /** NUEVO: tramo entre dos agencias del mismo trayecto (por km) */
public static function segmentBetweenAgencies(int $origenId, int $destinoId): ?array {
    $pdo = Database::get();

    // Todas las rutas donde está cada agencia con su km
    $q = "
      SELECT ta.trayecto_id, ta.agencia_id, ta.km_en_ruta
      FROM trayecto_agencia ta
      WHERE ta.agencia_id IN (?, ?)
    ";
    $st = $pdo->prepare($q);
    $st->execute([$origenId, $destinoId]);
    $raw = $st->fetchAll();
    if (count($raw) < 2) return null;

    // Agrupar por agencia
    $porAg = [];
    foreach ($raw as $r) {
        $porAg[(int)$r['agencia_id']][] = [
            'trayecto_id' => (int)$r['trayecto_id'],
            'km'          => $r['km_en_ruta'] !== null ? (float)$r['km_en_ruta'] : null,
        ];
    }
    if (!isset($porAg[$origenId], $porAg[$destinoId])) return null;

    // Encontrar un trayecto común a ambas agencias
    $setO = array_column($porAg[$origenId], 'km',  'trayecto_id'); // trayecto_id => km
    $setD = array_column($porAg[$destinoId], 'km', 'trayecto_id');

    $trayectoComun = null;
    foreach ($setO as $tid => $kmO) {
        if (array_key_exists($tid, $setD)) { $trayectoComun = (int)$tid; break; }
    }
    if ($trayectoComun === null) return null;

    $kmO = $setO[$trayectoComun] ?? null;
    $kmD = $setD[$trayectoComun] ?? null;
    if ($kmO === null || $kmD === null) return null; // necesitamos kms

    $kmMin = min($kmO, $kmD);
    $kmMax = max($kmO, $kmD);

    // Trayecto
    $stT = $pdo->prepare("SELECT id, nombre FROM trayecto WHERE id = ?");
    $stT->execute([$trayectoComun]);
    $tray = $stT->fetch();
    if (!$tray) return null;

    // Agencias en el tramo [kmMin, kmMax] dentro de ese trayecto
    $st2 = $pdo->prepare("
        SELECT a.id, a.nombre, a.ubicacion, ta.km_en_ruta AS km
        FROM trayecto_agencia ta
        JOIN agencia a ON a.id = ta.agencia_id
        WHERE ta.trayecto_id = ?
          AND ta.km_en_ruta IS NOT NULL
          AND ta.km_en_ruta BETWEEN ? AND ?
        ORDER BY ta.km_en_ruta ASC, a.nombre ASC
    ");
    $st2->execute([$trayectoComun, $kmMin, $kmMax]);
    $ags = $st2->fetchAll();
    if (!$ags) return null;

    $line = [];
    $out  = [];
    foreach ($ags as $r) {
        if (empty($r['ubicacion'])) continue;
        [$lat, $lng] = array_map('trim', explode(',', $r['ubicacion']));
        $out[] = [
            'id'     => (int)$r['id'],
            'nombre' => $r['nombre'],
            'lat'    => (float)$lat,
            'lng'    => (float)$lng,
            'km'     => $r['km'] !== null ? (float)$r['km'] : null,
        ];
        $line[] = [(float)$lat, (float)$lng];
    }
    if (count($line) < 2) return null;

    return [
        'trayecto'   => $tray,   // {id, nombre}
        'agencias'   => $out,    // tramo
        'line'       => $line,
        'origen_id'  => $origenId,
        'destino_id' => $destinoId,
        'km_desde'   => $kmMin,
        'km_hasta'   => $kmMax,
    ];
}


    public static function allWithTrayecto(): array {
    $pdo = Database::get();
    $sql = "
        SELECT
            a.id,
            a.nombre,
            a.ubicacion,           -- 'lat,lng' en texto
            a.km_en_ruta,
            a.trayecto_id,
            t.nombre AS trayecto_nombre,
            a.link_foto_agencia AS foto
        FROM agencia a
        LEFT JOIN trayecto t ON t.id = a.trayecto_id
        ORDER BY
            (t.nombre IS NULL), t.nombre,
            (a.km_en_ruta IS NULL), a.km_en_ruta,
            a.nombre
    ";
    return $pdo->query($sql)->fetchAll();
}
}
