<?php
require_once dirname(__DIR__).'/Models/agencia.php';
require_once dirname(__DIR__).'/Models/Trayecto.php';

class ApiController {
    // Un solo endpoint para Global o Ruta
    public function agencias(): string {
        header('Content-Type: application/json; charset=utf-8');
        $trayectoId = isset($_GET['trayecto_id']) ? (int)$_GET['trayecto_id'] : 0;

        if ($trayectoId > 0) {
            // Vista "Ruta"
            $t = Trayecto::get($trayectoId);
            if (!$t) {
                http_response_code(404);
                return json_encode(['error' => 'Trayecto no encontrado'], JSON_UNESCAPED_UNICODE);
            }

            $agencias = Agencia::forTrayectoOrderedWithRuta($trayectoId); // incluye km y coords
            $line = [];
            foreach ($agencias as $a) {
                $line[] = [$a['lat'], $a['lng']];
            }

            return json_encode([
                'trayecto' => $t,          // {id, nombre}
                'agencias' => $agencias,   // [{id,nombre,lat,lng,km}]
                'line'     => $line
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
}

