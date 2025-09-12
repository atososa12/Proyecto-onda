
?>
<?php
class Agencia {
    private int $id_agencia;
    private string $ubicacion;
    private string $nombre;
    private string $link_a_foto_agencia;

    // Ceste es el constructor 
    public function __construct($id_agencia, $ubicacion, $nombre, $foto) {
        $this->id_agencia = $id_agencia;
        $this->ubicacion = $ubicacion;
        $this->nombre = $nombre;
        $this->link_a_foto_agencia = $foto;
    }

    // Getters 
    public function getIdAgencia(): int {
        return $this->id_agencia;
    }

    public function getUbicacion(): string {
        return $this->ubicacion;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getFoto(): string {
        return $this->link_a_foto_agencia;
    }

    // Setters
    public function setUbicacion(string $ubicacion): void {
        $this->ubicacion = $ubicacion;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setFoto(string $foto): void {
        $this->link_a_foto_agencia = $foto;
    }
}
