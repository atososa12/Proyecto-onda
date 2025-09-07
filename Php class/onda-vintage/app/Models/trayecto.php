<?php
// app/Models/Trayecto.php
require_once dirname(__DIR__).'/Database.php';

class Trayecto {
    public static function all(): array {
        $pdo = Database::get();
        return $pdo->query("SELECT id, nombre FROM trayecto ORDER BY nombre")->fetchAll();
    }

    public static function get(int $id): ?array {
        $pdo = Database::get();
        $st = $pdo->prepare("SELECT id, nombre FROM trayecto WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }
}
