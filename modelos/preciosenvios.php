<?php
class PreciosEnvios {
    private $pdo;

    public function __construct() {
        $this->pdo = BasedeDatos::Conectar();
    }

    public function obtenerProvincias() {
        $stmt = $this->pdo->query("SELECT id, provincia FROM provincias ORDER BY provincia");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerLocalidadesPorProvincia($id_provincia) {
        $stmt = $this->pdo->prepare("SELECT id, localidad, costo_envio FROM localidades WHERE id_provincia = :id_provincia ORDER BY localidad");
        $stmt->execute([':id_provincia' => $id_provincia]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarCostoEnvio($id_localidad, $costo) {
        $stmt = $this->pdo->prepare("UPDATE localidades SET costo_envio = :costo WHERE id = :id_localidad");
        return $stmt->execute([':costo' => $costo, ':id_localidad' => $id_localidad]);
    }
}
