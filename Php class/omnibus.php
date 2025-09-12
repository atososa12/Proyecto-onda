<?php
class Omnibus {
    private int $id;
    private int $numero;
    private string $apodo;
    private string $modelo;
    private DateTime $anioInicio;
    private ?DateTime $anioFin; // puede ser null el valor, por eso use ?DateTime y no DateTime 
    private string $foto;

    //este seria el constructor 
    public function __construct($id, $numero, $apodo, $modelo, $anioInicio, $anioFin, $foto) {
        $this->id = $id;
        $this->numero = $numero;
        $this->apodo = $apodo;
        $this->modelo = $modelo;
        $this->anioInicio = new DateTime($anioInicio);
        $this->anioFin = $anioFin ? new DateTime($anioFin) : null;
        $this->foto = $foto;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNumero(): int { return $this->numero; }
    public function getApodo(): string { return $this->apodo; }
    public function getModelo(): string { return $this->modelo; }
    public function getAnioInicio(): DateTime { return $this->anioInicio; }
    public function getAnioFin(): ?DateTime { return $this->anioFin; }
    public function getFoto(): string { return $this->foto; }

    // Setters
    public function setNumero(int $numero): void { $this->numero = $numero; }
    public function setApodo(string $apodo): void { $this->apodo = $apodo; }
    public function setModelo(string $modelo): void { $this->modelo = $modelo; }
    public function setAnioFin(?string $anioFin): void {
        $this->anioFin = $anioFin ? new DateTime($anioFin) : null;
    }
    public function setFoto(string $foto): void { $this->foto = $foto; }
}
