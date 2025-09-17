<?php
// app/Controllers/home.php
require_once dirname(__DIR__).'/Models/agencia.php';

class HomeController {
  public function index(): string {
    $agencias = Agencia::allForMap();   // <--- importante
    ob_start();
    include dirname(__DIR__).'/Views/home/index.php';
    return ob_get_clean();
  }
}