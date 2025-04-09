<?php

require '../../modelos/configproduct-detail.php';
include '../../modelos/basededatos.php';

header('Content-Type: application/json');

$conexion = BasedeDatos::Conectar();

$productos_asociativo = isset($_SESSION['carrito']['variantes']) ? $_SESSION['carrito']['variantes'] : null;
$lista_carrito = [];
file_put_contents("debug_carrito.txt", print_r($_SESSION['carrito'], true));
if ($productos_asociativo && count($productos_asociativo) > 0) {
    // Convertir array asociativo a indexado
    $productos = array_values($productos_asociativo);

    // Extraer los ID de variantes
    $id_variantes = array_map(function($item) {
        return intval($item['id_variante']);
    }, $productos);

    // Crear marcadores de posición
    $placeholders = implode(',', array_fill(0, count($id_variantes), '?'));

    // Consulta SQL
    $sql = "SELECT 
                pv.ID_producvar,
                pv.precio,
                pv.ID_producto,
                p.nombre,
                p.ruta_imagen,
                t.nombre AS talla,
                c.nombre AS color
            FROM productos_variantes pv
            INNER JOIN producto p ON pv.ID_producto = p.ID_producto
            INNER JOIN c_talla t ON pv.ID_talla = t.ID_talla
            INNER JOIN c_colores c ON pv.ID_color = c.ID_colores
            WHERE pv.ID_producvar IN ($placeholders) AND p.estado = 1";

    $consulta = $conexion->prepare($sql);
    $consulta->execute($id_variantes);
    $productos_db = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Indexar por ID de variante
    $productos_indexados = [];
    foreach ($productos_db as $prod) {
        $productos_indexados[$prod['ID_producvar']] = $prod;
    }

    // Armar respuesta
    foreach ($productos as $detalle) {
        $id_variante = $detalle['id_variante'];
        $cantidad = intval($detalle['cantidad']);

        if (isset($productos_indexados[$id_variante])) {
            $producto = $productos_indexados[$id_variante];
            $producto['cantidad'] = $cantidad;
            $producto['precio'] = floatval($producto['precio']);
            $lista_carrito[] = $producto;
        }
    }
}

echo json_encode($lista_carrito);
