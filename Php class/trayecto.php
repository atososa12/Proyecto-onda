<?php
class Trayecto {
    private int $id;
    private Agencia $origen;
    private Agencia $destino;
    private array $omnibusAsignados = []; // lista de Omnibus
    private string $descripcion;
    private DateTime $inicio;
    private DateTime $fin;

    // Constructor
    public function __construct($id, Agencia $origen, Agencia $destino, string $descripcion, $inicio, $fin) {
        $this->id = $id;
        $this->origen = $origen;
        $this->destino = $destino;
        $this->descripcion = $descripcion;
        $this->inicio = new DateTime($inicio);
        $this->fin = new DateTime($fin);
    }
 // CRUD simulados hasta que conectemos a la base de datos , la cual aun no tenemos jajajas
    
    public function asignarOmnibus(Omnibus $omnibus): void {
        $this->omnibusAsignados[] = $omnibus;
    }

    public function getOmnibusAsignados(): array {
        return $this->omnibusAsignados;
    }

    public function guardar(): bool {
        //  INSERT en BD
        return true;
    }

    public function listar(): array {
        // devuelve los trayectos con : (SELECT * FROM trayectos)
        return [];
    }

    public function buscarPorId(int $id): ?Trayecto {
        // gracias a esto buscamos los trayecto en BD por id
        return null;
    }

    public function actualizar(): bool {
        // UPDATE trayecto
        return true;
    }

    public function eliminar(int $id): bool {
        // DELETE trayecto
        return true;
    }
}

