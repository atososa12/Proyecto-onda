<?php
// app/Models/historia.php
require_once dirname(__DIR__).'/Database.php';

class Historia {
    public static function all(): array {
        $pdo = Database::get();

        $sql = "
            SELECT
                h.id,
                h.titulo,
                h.fecha,
                h.uri_historia,
                h.uri_fotos,

                h.id_trayecto,
                h.id_agencia,
                h.id_agencia_origen,
                h.id_agencia_destino,
                h.id_omnibus,

                a.nombre  AS agencia_nombre,
                ao.nombre AS origen_nombre,
                ad.nombre AS destino_nombre,

                t.id      AS trayecto_id_resuelto,
                t.nombre  AS trayecto_nombre

            FROM historia h
            LEFT JOIN agencia  a  ON a.id  = h.id_agencia
            LEFT JOIN agencia  ao ON ao.id = h.id_agencia_origen
            LEFT JOIN agencia  ad ON ad.id = h.id_agencia_destino
            LEFT JOIN trayecto t  ON t.id  = COALESCE(h.id_trayecto, ao.trayecto_id)
            -- NO seleccionamos columnas de omnibus para evitar 42S22
            -- LEFT JOIN omnibus o ON o.id = h.id_omnibus

            ORDER BY COALESCE(h.fecha, 0) DESC, h.id DESC
        ";

        return $pdo->query($sql)->fetchAll();
    }
}
