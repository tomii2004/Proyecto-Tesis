<?php
require_once '../modelos/basededatos.php';

require_once 'funcionesregister.php';

$datos = [];

if(isset($_POST['action'])){
    $action = $_POST['action'];
    $conexion = BasedeDatos::Conectar();

    //aca se puede agregar un switch con diferentes opciones pero por ahora lo hacemos asi
    if($action == 'existeUsuario'){ 
        $datos['ok'] = usuarioExiste($_POST['usuario'],$conexion);
    }elseif($action == 'existeEmail'){
        $datos['ok'] = emailExiste($_POST['email'],$conexion);
    }elseif($action == 'existeDni'){
        $datos['ok'] = dniExiste($_POST['dni'],$conexion);
    }
}

echo json_encode($datos);


?>