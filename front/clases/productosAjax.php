<?php
require_once '../../modelos/basededatos.php';


$datos = [];

if(isset($_POST['action'])){
    $action = $_POST['action'];
    $conexion = BasedeDatos::Conectar();

    //aca se puede agregar un switch con diferentes opciones pero por ahora lo hacemos asi
    if($action == 'buscarColoresPorTalla'){ 
        $datos['colores'] = buscarColoresPorTalla($conexion);
    }elseif($action == 'buscarIdVariante'){
        $datos['variantes'] = buscarIdVariante($conexion);
    }
}

function buscarColoresPorTalla($conexion){
    $idProducto = $_POST['ID_producto'] ?? 0;
    $idTalla = $_POST['ID_talla'] ?? 0;

    $sqlColores =  $conexion ->prepare("SELECT DISTINCT c.ID_colores,c.nombre FROM productos_variantes AS pv INNER JOIN c_colores AS c ON pv.ID_color = c.ID_colores WHERE pv.ID_producto = ?  AND pv.ID_talla = ?");
    $sqlColores ->execute([$idProducto,$idTalla]);
    $colores = $sqlColores->fetchAll(PDO::FETCH_ASSOC);

    $html = '';

    foreach($colores as $color){
        $html .= '<option value="'.$color['ID_colores']. '">'. $color['nombre'] . '</option>';
    }
    return $html;


}
function buscarIdVariante($conexion){
    $idProducto = $_POST['ID_producto'] ?? 0;
    $idTalla = $_POST['ID_talla'] ?? 0;
    $idColor = $_POST['ID_colores'] ?? 0;

    $sql =  $conexion ->prepare("SELECT ID_producvar,precio,stock FROM productos_variantes WHERE ID_producto = ?  AND ID_talla = ? AND ID_color = ? LIMIT 1");
    $sql ->execute([$idProducto,$idTalla,$idColor]);
    return $sql->fetch(PDO::FETCH_ASSOC);


}

echo json_encode($datos);


?>