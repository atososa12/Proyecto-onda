<?php
class Trayecto {
    public $id;//Identificador del trayecto.
    public $origen;//ciudad o punto de partida
    public $destino;// ciudad o punto de llegada
    public $duracion; //tiempo que lleva el trayecto
    public $omnibus; // instancia de Omnibus

    public function __construct($id, $origen, $destino, $duracion, Omnibus $omnibus) {
        $this->id = $id;
        $this->origen = $origen;
        $this->destino = $destino;
        $this->duracion = $duracion;
        $this->omnibus = $omnibus;
    }
}

//Recibe todos los datos del trayecto y también el ómnibus que lo realiza. Así se vinculan ambas clases.
?>
