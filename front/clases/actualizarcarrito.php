<?php

require '../../modelos/configproduct-detail.php';
require '../../modelos/basededatos.php';

header('Content-Type: application/json');
$datos = ['ok' => false];

$action = $_POST['action'] ?? null;
$idVarianteCompleto = $_POST['id_variante'] ?? '0-0-0';

// Validar estructura de clave
$partes = explode('-', $idVarianteCompleto);
if (count($partes) !== 3) {
    $datos['error'] = "Formato inválido de id_variante";
    echo json_encode($datos);
    exit;
}

[$idProducto, $idTalla, $idColor] = array_map('intval', $partes);

// Buscar variante válida
$idProducVar = buscarIDProducVar($idProducto, $idTalla, $idColor);

if (!$idProducVar) {
    $datos['error'] = "Variante no encontrada con esos datos";
    echo json_encode($datos);
    exit;
}

// Asegurar array del carrito
if (!isset($_SESSION['carrito']['variantes'])) {
    $_SESSION['carrito']['variantes'] = [];
}
if (!isset($_SESSION['carrito']['nombres'])) {
    $_SESSION['carrito']['nombres'] = [];
}

if ($action === 'agregar') {
    $cantidad = max(1, intval($_POST['cantidad'] ?? 0));

    // Si deseas acumular cantidades (como un addToCart), suma acá:
    // $cantidad += $_SESSION['carrito']['variantes'][$idVarianteCompleto]['cantidad'] ?? 0;

    $subtotal = agregar($idProducVar, $cantidad, $idVarianteCompleto);

    if ($subtotal > 0) {
        $_SESSION['carrito']['variantes'][$idVarianteCompleto] = [
            'id' => $idProducto,
            'id_variante' => $idProducVar,
            'id_talla' => $idTalla,
            'id_color' => $idColor,
            'cantidad' => $cantidad
        ];

        $datos['ok'] = true;
        $datos['sub'] = number_format($subtotal, 2, '.', ',');
    } else {
        $datos['error'] = "Stock insuficiente";
    }

} elseif ($action === 'eliminar') {
    $datos['ok'] = eliminar($idVarianteCompleto);
}

echo json_encode($datos);

// FUNCIONES

function buscarIDProducVar(int $idProducto, int $idTalla, int $idColor): ?int {
    $conexion = BasedeDatos::Conectar();
    $sql = $conexion->prepare("
        SELECT ID_producvar 
        FROM productos_variantes 
        WHERE ID_producto = ? AND ID_talla = ? AND ID_color = ? LIMIT 1
    ");
    $sql->execute([$idProducto, $idTalla, $idColor]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row ? intval($row['ID_producvar']) : null;
}

function agregar(int $idProducVar, int $cantidad, string $idVarianteCompleto): float {
    if ($idProducVar <= 0 || $cantidad <= 0) return 0;

    $conexion = BasedeDatos::Conectar();
    $sql = $conexion->prepare("
        SELECT pv.precio, pv.stock, p.nombre AS nombre_producto, c.nombre AS color, t.nombre AS talla
        FROM productos_variantes pv
        INNER JOIN producto p ON pv.ID_producto = p.ID_producto
        INNER JOIN c_colores c ON pv.ID_color = c.ID_colores
        INNER JOIN c_talla t ON pv.ID_talla = t.ID_talla
        WHERE pv.ID_producvar = ? LIMIT 1
    ");
    $sql->execute([$idProducVar]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);

    if ($row && intval($row['stock']) >= $cantidad) {
        $_SESSION['carrito']['nombres'][$idVarianteCompleto] = 
            "{$row['nombre_producto']} - {$row['color']} - Talle {$row['talla']}";
        return $cantidad * floatval($row['precio']);
    }

    return 0;
}

function eliminar(string $idVarianteCompleto): bool {
    unset($_SESSION['carrito']['variantes'][$idVarianteCompleto]);
    unset($_SESSION['carrito']['nombres'][$idVarianteCompleto]);
    return true;
}
