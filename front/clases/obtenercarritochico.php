<?php
session_start();
include '../../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();
$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $consulta_productos = $conexion->prepare("SELECT *, ? AS cantidad FROM producto WHERE ID_producto = ? AND estado = 1;");
        $consulta_productos->execute([$cantidad, $clave]);
        $lista_carrito[] = $consulta_productos->fetch(PDO::FETCH_ASSOC);
    }
}

header('Content-Type: application/json');
echo json_encode($lista_carrito);
?>
