<?php
session_start();

include 'modelos/variantes.php';

class VariantesControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Variantes();
    }

    public function Inicio(){
        $this->modelo->VerificarAdmin();
        $variante1 = $this->modelo->TraerTalla();
        $variantes1 = $variante1;
        $variante2 = $this->modelo->TraerColor();
        $variantes2 = $variante2;
        require_once "vistas/encabezado.php";
        require_once "vistas/variantes/variantes.php";
        require_once "vistas/pie.php";
    }

    public function FormNuevo(){
        $variante = $this->modelo->VerificarAdmin();
        
        require_once "vistas/encabezado.php";
        require_once "vistas/variantes/nuevo.php";
        require_once "vistas/pie.php";
    }

    public function FormEditar(){
        $this->modelo->VerificarAdmin();
    
        $variante = null;
        if (isset($_GET['tipo']) && isset($_GET['id'])) {
            $tipo = $_GET['tipo'];
            $id = $_GET['id'];
    
            if ($tipo === 'talla') {
                $variante = $this->modelo->ModificarTalla($id); // Pasar ID para obtener datos específicos
            } elseif ($tipo === 'color') {
                $variante = $this->modelo->ModificarColor($id);
            }
        }
    
        require_once "vistas/encabezado.php";
        require_once "vistas/variantes/editar.php";
        require_once "vistas/pie.php";
    }

    public function AñadirVariante(){
        if(isset($_POST['variantes']) && isset($_POST['nombre'])){
            $nombre = $_POST['nombre'];
            $tipovariante = $_POST['variantes'];
            $resultado = false;

            if($tipovariante == 'talla'){
                $resultado = $this->modelo->AgregarTalla($nombre);
            }else if($tipovariante == 'color'){
                $resultado = $this->modelo->AgregarColor($nombre);
            }

            if ($resultado) {
                header("Location: ?c=variantes&alerta=success");
            } else {
                header("Location: ?c=variantes&alerta=error");
            }
            exit();
        }
        header('Location: ?c=variantes');
    }

    public function ActualizarVariante(){
        if (isset($_POST['tipo']) && isset($_POST['id']) && isset($_POST['nombre'])) {
            $tipo = $_POST['tipo'];
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
    
            if ($tipo === 'talla') {
                $this->modelo->ActualizarTallaModelo($id, $nombre);
            } elseif ($tipo === 'color') {
                $this->modelo->ActualizarColorModelo($id, $nombre);
            }
        }
        header('Location: ?c=variantes');
    }

    public function EliminarVariante() {
        if (isset($_POST['id']) && isset($_POST['tipo'])) {
            $id = $_POST['id'];
            $tipo = $_POST['tipo'];
    
            if ($tipo === 'talla') {
                $this->modelo->EliminarTallaModelo($id);
            } elseif ($tipo === 'color') {
                $this->modelo->EliminarColorModelo($id);
            }
    
            header('Location: ?c=variantes');
            exit;
        } else {
            echo "Error: No se recibió un ID o tipo válido.";
        }
    }

    
}


?>