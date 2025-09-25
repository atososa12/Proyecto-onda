<?php
require_once dirname(__DIR__).'/Database.php';

class Agencia {

//Para listar todas las agencias en el mapa interactivo con allforMap()
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

//Para listar todas las agencias en index
public static function allForIndex(): array {
    $pdo = Database::get();
    $sql = "
        SELECT
            a.id,
            a.nombre,
            a.ubicacion,
            ta.km_en_ruta       AS km_en_ruta,     -- la vista lo muestra en la col 'Km'
            t.id                AS trayecto_id,
            t.nombre            AS trayecto_nombre, -- la vista lo muestra en 'Trayecto'
            a.link_foto_agencia AS foto
        FROM agencia a
        LEFT JOIN trayecto_agencia ta ON ta.agencia_id = a.id
        LEFT JOIN trayecto          t ON t.id = ta.trayecto_id
        ORDER BY
            a.nombre,
            (t.nombre IS NULL), t.nombre,
            (ta.km_en_ruta IS NULL), ta.km_en_ruta,  -- primero los que tienen km
            a.id
    ";
    return $pdo->query($sql)->fetchAll();
}

// NUEVO: ruta con alternativas basadas en rol = 'alt:<grupo>'
public static function forTrayectoWithAlternativas(int $trayectoId): array {
    $pdo = Database::get();

    // Traer todas las agencias del trayecto con rol y km
    $st = $pdo->prepare("
        SELECT a.id, a.nombre, a.ubicacion,
               ta.km_en_ruta AS km,
               ta.rol
        FROM trayecto_agencia ta
        JOIN agencia a ON a.id = ta.agencia_id
        WHERE ta.trayecto_id = ?
        ORDER BY (ta.km_en_ruta IS NULL), ta.km_en_ruta ASC, a.nombre ASC
    ");
    $st->execute([$trayectoId]);
    $rows = $st->fetchAll();

    // Info del trayecto
    $stT = $pdo->prepare("SELECT id, nombre FROM trayecto WHERE id = ?");
    $stT->execute([$trayectoId]);
    $tray = $stT->fetch();

    // Helpers
    $parsePoint = function (?string $ubic) {
        if (!$ubic) return null;
        $p = array_map('trim', explode(',', $ubic));
        if (count($p) !== 2) return null;
        $lat = (float)$p[0]; $lng = (float)$p[1];
        if (!is_finite($lat) || !is_finite($lng)) return null;
        return [$lat, $lng];
    };

    // Construir objetos de agencias (lat/lng, km, rol)
    $agObjs = [];
    foreach ($rows as $r) {
        $pt = $parsePoint($r['ubicacion']);
        if (!$pt) continue;
        $agObjs[] = [
            'id'     => (int)$r['id'],
            'nombre' => $r['nombre'],
            'lat'    => $pt[0],
            'lng'    => $pt[1],
            'km'     => $r['km'] !== null ? (float)$r['km'] : null,
            'rol'    => $r['rol'] ?? null,
        ];
    }

    // Separar principal vs alternativas
    $main = [];
    $alts = []; // grupo => [agencias...]
    foreach ($agObjs as $ag) {
        $isAlt = (isset($ag['rol']) && str_starts_with((string)$ag['rol'], 'alt:'));
        if ($isAlt) {
            $g = substr((string)$ag['rol'], 4); // 'a', 'b', ...
            $alts[$g][] = $ag;
        } else {
            $main[] = $ag;
        }
    }

    // Ordenar por km
    usort($main, fn($a,$b) => ($a['km'] ?? INF) <=> ($b['km'] ?? INF));
    foreach ($alts as $g => $list) {
        usort($list, fn($a,$b) => ($a['km'] ?? INF) <=> ($b['km'] ?? INF));
        $alts[$g] = $list;
    }

    // Línea principal
    $line = array_map(fn($ag) => [$ag['lat'], $ag['lng']], array_filter($main, fn($ag) => $ag['km'] !== null));

    // Helpers para anclas por km
    $findPrev = function (float $km) use ($main) {
        $prev = null;
        foreach ($main as $ag) {
            if ($ag['km'] === null) continue;
            if ($ag['km'] <= $km) $prev = $ag;
            else break;
        }
        return $prev;
    };
    $findNext = function (float $km) use ($main) {
        foreach ($main as $ag) {
            if ($ag['km'] === null) continue;
            if ($ag['km'] >= $km) return $ag;
        }
        return null;
    };

    // Alternativas conectadas (ancladas a principal)
    $linesAlt = []; // [{group, coords: [[lat,lng], ...]}]
    foreach ($alts as $g => $list) {
        $listHasKm = array_filter($list, fn($ag) => $ag['km'] !== null);
        if (count($listHasKm) < 1) continue;

        $startKm = $list[0]['km'];
        $endKm   = $list[count($list)-1]['km'];
        if ($startKm === null || $endKm === null) continue;

        $prev = $findPrev($startKm);
        $next = $findNext($endKm);

        $coords = [];
        if ($prev) $coords[] = [$prev['lat'], $prev['lng']];
        foreach ($list as $ag) $coords[] = [$ag['lat'], $ag['lng']];
        if ($next) $coords[] = [$next['lat'], $next['lng']];

        if (count($coords) >= 2) {
            $linesAlt[] = ['group' => $g, 'coords' => $coords];
        }
    }

    return [
        'trayecto' => $tray ?: ['id' => $trayectoId, 'nombre' => 'N/D'],
        'agencias' => $agObjs, // sigue siendo compatible con tu front
        'line'     => $line,   // principal
        'linesAlt' => $linesAlt, // NUEVO (el front ya lo pinta punteado si aplicaste mi patch)
    ];
}


}
