<?php
class Omnibus {
    private $id;
    private $matricula;
    private $modelo; // Ej: "Mercedes-Benz O-364"
    private $anio; // 1985
    private $capacidad;
    private $estado; // "activo", "vintage", "baja"

    public function __construct($matricula, $modelo, $anio, $capacidad) {
        $this->matricula = $matricula;
        $this->modelo = $modelo;
        $this->anio = $anio;
        $this->capacidad = $capacidad;
        $this->estado = "activo";
    }

    // Getters y setters...
    public function getFotoVintage() {
        return "assets/omnibus/" . $this->matricula . ".jpg";
    }
}
?>