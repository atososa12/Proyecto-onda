<?php

class Agencia {
    public $id; //idetificador de la agencia
    public $nombre; //Nombre de la agencia
    public $direccion; //direccion física de la agencia
    public $trayectos = []; // array que guarda todos los trayectos

    public function __construct($id, $nombre, $direccion) { //Inicializa la agencia con su ID, nombre y dirección.
        $this->id = $id;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
    }

    public function agregarTrayecto(Trayecto $trayecto) { //Permite añadir trayectos al array $trayectos.
        $this->trayectos[] = $trayecto;
    }
}
//Cada Agencia puede tener varios Trayectos.
//Cada Trayecto está asociado a un único Omnibus.
?>
