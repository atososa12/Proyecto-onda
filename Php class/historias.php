<?php
class Historia {
    private int $id_historia;
    private string $titulo;
    private DateTime $fecha;
    private int $id_trayecto;
    private int $id_agencia;
    private int $id_omnibus;
    private string $URI_Historia;
    private string $URI_Fotos;

    // Constructor
    public function __construct($id, $titulo, $fecha, $id_trayecto, $id_agencia, $id_omnibus, $uri_historia, $uri_fotos) {
        $this->id_historia = $id;
        $this->titulo = $titulo;
        $this->fecha = new DateTime($fecha);
        $this->id_trayecto = $id_trayecto;
        $this->id_agencia = $id_agencia;
        $this->id_omnibus = $id_omnibus;
        $this->URI_Historia = $uri_historia;
        $this->URI_Fotos = $uri_fotos;
    }

    // Getters
    public function getTitulo(): string { return $this->titulo; }
    public function getFecha(): DateTime { return $this->fecha; }
    public function getURIFotos(): string { return $this->URI_Fotos; }
    public function getURIHistoria(): string { return $this->URI_Historia; }
}
