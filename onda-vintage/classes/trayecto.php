<?php
class Trayecto {
    private $id;
    private $origen; // Ej: "Montevideo"
    private $destino; // Ej: "Paysandú"
    private $duracion; // "6 horas"
    private $precio; // 120.50 (pesos uruguayos vintage)
    private $omnibusAsignado; // Objeto Omnibus

    public function __construct($origen, $destino, $duracion, $precio) {
        $this->origen = $origen;
        $this->destino = $destino;
        $this->duracion = $duracion;
        $this->precio = $precio;
    }

    public function asignarOmnibus(Omnibus $omnibus) {
        $this->omnibusAsignado = $omnibus;
    }
}
?>