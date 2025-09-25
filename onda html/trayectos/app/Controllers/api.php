 <?php
require_once __DIR__ . '/../Database.php';

// incluye modelos 
require_once __DIR__ . '/../Models/agencia.php';
require_once __DIR__ . '/../Models/trayecto.php';


class ApiController {
    // Un solo endpoint para Global o Ruta
    public function agencias(): string {
        header('Content-Type: application/json; charset=utf-8');
        $trayectoId = isset($_GET['trayecto_id']) ? (int)$_GET['trayecto_id'] : 0;

        if ($trayectoId > 0) {
            $t = Trayecto::get($trayectoId);
            if (!$t) {
                http_response_code(404);
                return json_encode(['error' => 'Trayecto no encontrado'], JSON_UNESCAPED_UNICODE);
            }

            // Principal
            $agMain = Agencia::forTrayectoOrderedWithRuta($trayectoId);
            $line   = array_map(fn($a) => [$a['lat'], $a['lng']], $agMain);

            // Hijos (ramales) por trayecto_padre
            $pdo = Database::get();
            $st  = $pdo->prepare("SELECT id, nombre FROM trayecto WHERE trayecto_padre = ?");
            $st->execute([$trayectoId]);
            $children = $st->fetchAll();

            $linesAlt = [];
            $agenciasAlt = []; // <<<< NUEVO

            foreach ($children as $ch) {
                $ags = Agencia::forTrayectoOrderedWithRuta((int)$ch['id']);
                $coords = array_map(fn($a) => [$a['lat'], $a['lng']], $ags);
                if (count($coords) >= 2) {
                    $linesAlt[] = [
                        'trayecto_id' => (int)$ch['id'],
                        'nombre'      => $ch['nombre'],
                        'coords'      => $coords,
                    ];
                }
                    // acumular agencias del ramal
                    foreach ($ags as $a) { $agenciasAlt[] = $a; }
            }

            return json_encode([
                'trayecto'  => $t,        // {id, nombre, ...}
                'agencias'  => $agMain,   // markers de la principal
                'agenciasAlt'=> $agenciasAlt, // <<<< NUEVO: pines de ramales
                'line'      => $line,     // línea principal
                'linesAlt'  => $linesAlt  // ramales del padre (incluye tu id=7)
            ], JSON_UNESCAPED_UNICODE);
        }

        // Vista "Global"
        return json_encode(Agencia::allForMap(), JSON_UNESCAPED_UNICODE);
    }

    // Para poblar el combo
    public function trayectos(): string {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode(Trayecto::all(), JSON_UNESCAPED_UNICODE);
    }

    // NUEVO: tramo entre dos agencias
    public function rutaEntreAgencias(): string {
        header('Content-Type: application/json; charset=utf-8');

        $origenId  = isset($_GET['origen_id'])  ? (int)$_GET['origen_id']  : 0;
        $destinoId = isset($_GET['destino_id']) ? (int)$_GET['destino_id'] : 0;
        if (!$origenId || !$destinoId) {
            http_response_code(400);
            return json_encode(['error' => 'Parámetros inválidos'], JSON_UNESCAPED_UNICODE);
        }

        $res = Agencia::segmentBetweenAgencies($origenId, $destinoId);
        if (!$res) {
            http_response_code(404);
            return json_encode(['error' => 'No se pudo resolver el tramo (mismo trayecto o KMs faltantes)'], JSON_UNESCAPED_UNICODE);
        }
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function trayecto_ficha(): string {
        header('Content-Type: application/json; charset=utf-8');

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) { http_response_code(400); return json_encode(['error'=>'Falta id']); }

        $t = Trayecto::getBasicsById($id);
        if (!$t) { http_response_code(404); return json_encode(['error'=>'No encontrado']); }

        // 1) Leer lead desde la BD (necesita que el modelo lo seleccione)
        $lead = $t['lead'] ?? null;

        // 2) Fallback opcional: si no cargaste lead aún, usar primer bloque del summary
        if (!$lead && !empty($t['summary'])) {
            $lead = $this->firstBlockFromText($t['summary']);
        }

        // 3) Responder JSON válido (sin warnings previos)
        return json_encode([
            'id'             => (int)$t['id'],
            'nombre'         => $t['nombre'],
            'slug'           => $t['slug'],
            'summary'        => $t['summary'],
            'lead'           => $lead,
            'hero_image_url' => $t['hero_image_url'],
            'article_url'    => $t['article_url'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    public function trayectos_basicos(): string {
    header('Content-Type: application/json; charset=utf-8');
    return json_encode(Trayecto::allBasics(), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

}
