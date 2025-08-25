<?php
class Omnibus {
    public $id; //Identificador único del ómnibus.
    public $matricula; //Matrícula del vehículo
    public $capacidad; //Número de pasajeros
    public $modelo; //Marca o modelo del ómnibus

    public function __construct($id, $matricula, $capacidad, $modelo) {
        $this->id = $id;
        $this->matricula = $matricula;
        $this->capacidad = $capacidad;
        $this->modelo = $modelo;
    }
}
?>