<?php
// app/Controllers/historia.php
require_once dirname(__DIR__).'/Models/historia.php';

class HistoriaController {
    public function index(): void {
        $historias = Historia::all();
        require dirname(__DIR__).'/Views/historia/index.php';
    }
}