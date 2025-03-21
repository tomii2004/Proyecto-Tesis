<?php
session_start();
require_once "modelos/estadisticas.php";

class InicioControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this -> modelo = new Estadisticas();
    }

    public function Inicio() {
        $this->modelo->VerificarAdmin();
    
        // Verificar si el resumen semanal ya está guardado
        $hoy = date('Y-m-d');
        $lunes = date('Y-m-d', strtotime('monday this week', strtotime($hoy)));
        $domingo = date('Y-m-d', strtotime('sunday this week', strtotime($hoy)));
    
        $historial = $this->modelo->ObtenerHistorialSemanal();
        $existeResumen = false;
    
        foreach ($historial as $semana) {
            if ($semana['inicio'] == date('d/m/Y', strtotime($lunes)) && $semana['fin'] == date('d/m/Y', strtotime($domingo))) {
                $existeResumen = true;
                break;
            }
        }
    
        // Si no existe resumen semanal, se guarda automáticamente
        if (!$existeResumen) {
            $totalVentas = array_sum(explode(',', $this->modelo->EstadisticasVentasSemana()));
            $productosVendidos = json_encode($this->modelo->ProductosMasVendidos(
                $this->modelo->getPdo(),
                new DateTime($lunes),
                new DateTime($domingo)
            ));
            $this->modelo->GuardarResumenSemanal($totalVentas, $productosVendidos);
        }
        
        // Mostrar estadísticas
        $diasVentasEstadisticas = $this->modelo->EstadisticasVentasSemana();
        list($nombreProductos, $cantidadProductos) = $this->modelo->EstadisticasProdVendido();
        $historial = $this->modelo->ObtenerHistorialSemanal();
    
        require_once "vistas/encabezado.php";
        require_once "vistas/inicio/principal.php";
        require_once "vistas/pie.php";
    }
    
    public function FormEditarPassword(){
        $this->modelo->VerificarAdmin();
        list($errors, $usuario) = $this->modelo->CambiarPasswordAdminModelo();
        
        require_once "vistas/encabezado.php";
        require_once "vistas/inicio/cambiaradmin.php";
        $this->modelo->mostrarMensajes($errors);
        require_once "vistas/pie.php";
    }

    public function LogOut(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_type']);
    
        session_destroy();

        header('Location: paginalogin/loginadmin/loginadmin.php');
    }
    
    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarHistorial($termino);
            echo json_encode($resultados);
        }
    }

    public function LimpiarHistorial() {
        // Verifica que el usuario tenga permisos para eliminar el historial
        $this->modelo->VerificarAdmin();
        
        // Llamar al modelo para limpiar el historial
        $this->modelo->EliminarHistorial();
    
        // Redirigir a la página de inicio después de limpiar el historial
        header("Location: index.php?c=inicio");
        exit();
    }
    
}