<?php
require '../../modelos/configproduct-detail.php';
require '../../modelos/basededatos.php';

if(isset($_POST['action'])){
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    if($action == 'agregar'){
        $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 0;
        $respuesta = agregar($id,$cantidad);
        if($respuesta > 0){ //significa que encontro informacion 
            $_SESSION['carrito']['productos'][$id] = $cantidad;
            $datos['ok'] = true;
        }else{
            $datos['ok'] = false;
            $datos['cantidadAnterior'] = $_SESSION['carrito']['productos'][$id];
        }
        $datos['sub'] = '$' . number_format($respuesta,2,'.',',');
    }else if($action == 'eliminar'){
        $datos['ok'] = eliminar($id);

    }else{
        $datos['ok'] = false;
    }
}else{
    $datos['ok'] = false;
}

echo json_encode($datos);

function agregar($id,$cantidad){
    
    if($id > 0 && $cantidad > 0 && is_numeric($cantidad) && isset($_SESSION['carrito']['productos'][$id])){

        $conexion = BasedeDatos::Conectar();
        $sql_producto = $conexion ->prepare("SELECT precio,stock FROM producto where ID_producto = ? AND estado = 1 LIMIT 1;");
        $sql_producto ->execute([$id]);
        $row = $sql_producto->fetch(PDO::FETCH_ASSOC);
        $precio = $row['precio'];
        $stock = $row['stock'];
        if($stock >= $cantidad){
            return $cantidad * $precio;
        }
       

    }
    return 0;
    
}


function eliminar($id){
    if($id > 0){
        if(isset($_SESSION['carrito']['productos'][$id])){
            unset($_SESSION['carrito']['productos'][$id]);
            return true;
        }
    }else{
        return false;
    }
}




?>