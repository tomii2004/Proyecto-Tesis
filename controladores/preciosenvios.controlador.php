<?php
session_start();
require_once "modelos/PreciosEnvios.php";

class PreciosEnviosControlador {
    private $modelo;

    public function __construct() {
        $this->modelo = new PreciosEnvios();
    }

    // Método para mostrar la página principal con el dropdown de provincias
    public function Inicio() {
        $provincias = $this->modelo->obtenerProvincias();
        require_once "vistas/encabezado.php";
        require_once "vistas/pie.php";
        require_once "vistas/preciosenvios/preciosenvios.php";
        
    }

    // Acción AJAX para listar localidades según provincia
    public function ajaxListarLocalidades() {
        $id_provincia = $_POST['id_provincia'] ?? null;
        if (!$id_provincia) {
            echo json_encode([]);
            return;
        }
        $localidades = $this->modelo->obtenerLocalidadesPorProvincia($id_provincia);
        header('Content-Type: application/json');
        echo json_encode($localidades);
    }

    // Acción AJAX para actualizar el costo de envío
    public function ajaxActualizarCosto() {
        $id_localidad = $_POST['id_localidad'] ?? null;
        $costo = $_POST['costo'] ?? null;

        if (!$id_localidad || !is_numeric($costo)) {
            echo json_encode(['exito' => false]);
            return;
        }

        $exito = $this->modelo->actualizarCostoEnvio($id_localidad, $costo);

        header('Content-Type: application/json');
        echo json_encode(['exito' => $exito]);
    }
}
