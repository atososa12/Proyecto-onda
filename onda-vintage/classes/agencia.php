<?php
class Agencia {
    private $id;
    private $nombre; // Ej: "Terminal Baltasar Brum"
    private $ciudad;
    private $trayectos = []; // Array de objetos Trayecto

    public function __construct($nombre, $ciudad) {
        $this->nombre = $nombre;
        $this->ciudad = $ciudad;
    }

    public function agregarTrayecto(Trayecto $trayecto) {
        $this->trayectos[] = $trayecto;
    }
}
?>