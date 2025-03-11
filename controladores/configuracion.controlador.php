<?php
session_start();

include 'modelos/configuracion.php';

class ConfiguracionControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Configuracion();
    }

    public function Inicio(){
        $config = $this->modelo->VerificarAdmin();
        $config = $this->modelo->Tomarvalues();

        require_once "vistas/encabezado.php";
        require_once "vistas/configuracion/configuracion.php";
        require_once "vistas/pie.php";
    }

    public function GuardarConfiguracion(){
        error_log("Se llamó a GuardarConfiguracion");
        if($this->modelo->ActualizarConfiguracion()){
            $_SESSION['mensajeExito'] = "Los cambios se guardaron con éxito.";
        }else{
            $_SESSION['mensajeError'] = "No se pudo guardar los cambios.";
        }
        header("Location: ?c=configuracion");
        exit();
    }
    
}