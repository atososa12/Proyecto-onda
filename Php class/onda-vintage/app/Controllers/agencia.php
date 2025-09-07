<?php
require_once dirname(__DIR__).'/Models/agencia.php';

class AgenciaController {
    public function index(): void {
        $agencias = Agencia::allWithTrayecto();
        require dirname(__DIR__).'/Views/agencia/index.php';
    }
}