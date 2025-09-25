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

    public static function listForSidebar(): array {
    $db = Database::get();
    $sql = "SELECT id, nombre, slug, hero_image_url, article_url, summary, lead
            FROM trayecto
            ORDER BY nombre";
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById(int $id): ?array {
    $db = Database::get();
    $st = $db->prepare("SELECT id, nombre, slug, hero_image_url, article_url, summary, lead
                        FROM trayecto WHERE id = :id");
    $st->execute([':id'=>$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public static function getBySlug(string $slug): ?array {
    $db = Database::get();
    $st = $db->prepare("SELECT id, nombre, slug, hero_image_url, article_url, summary, lead
                        FROM trayecto WHERE slug = :slug");
    $st->execute([':slug'=>$slug]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

    public static function allBasics(): array {
    $db = Database::get();
    $sql = "SELECT id, nombre, slug, hero_image_url, article_url, summary, lead
            FROM trayecto ORDER BY nombre";
    return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getBasicsById(int $id): ?array {
    $db = Database::get();
    $st = $db->prepare("SELECT id, nombre, slug, hero_image_url, article_url, summary, lead
                        FROM trayecto WHERE id=:id");
    $st->execute([':id'=>$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

}
