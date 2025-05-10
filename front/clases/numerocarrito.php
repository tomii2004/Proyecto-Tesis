<?php 
require '../../modelos/configproduct-detail.php';
require '../../modelos/basededatos.php';

header('Content-Type: application/json');
$datos = ['ok' => false];

// Validar que todos los campos necesarios estén presentes
if (!isset($_POST['id'], $_POST['id_variante'], $_POST['id_talla'], $_POST['id_color'], $_POST['token'])) {
    echo json_encode($datos);
    exit;
}

$id_producto = intval($_POST['id']);
$id_variante = intval($_POST['id_variante']);
$id_talla = intval($_POST['id_talla']);
$id_color = intval($_POST['id_color']);
$cantidad = isset($_POST['cantidad']) ? max(1, intval($_POST['cantidad'])) : 1;
$token = $_POST['token'];
$token_tmp = hash_hmac('sha1', $id_producto, KEY_TOKEN);

// Validación del token
if ($token !== $token_tmp) {
    echo json_encode($datos);
    exit;
}

// Asegurar que el array del carrito existe
if (!isset($_SESSION['carrito']['variantes'])) {
    $_SESSION['carrito']['variantes'] = [];
}

$carrito = &$_SESSION['carrito']['variantes'];
$key = $id_producto . '-' . $id_talla . '-' . $id_color;

// Verificar si ya existe la combinación en el carrito
if (isset($carrito[$key])) {
    $carrito[$key]['cantidad'] += $cantidad;
} else {
    $carrito[$key] = [
        'id' => $id_producto,
        'id_variante' => $id_variante,
        'id_talla' => $id_talla,
        'id_color' => $id_color,
        'cantidad' => $cantidad
    ];
}

// Verificar stock actual
$conexion = BasedeDatos::Conectar();
$sql = $conexion->prepare("
    SELECT stock FROM productos_variantes 
    WHERE ID_producvar = ? AND ID_producto = ? 
    AND ID_talla = ? AND ID_color = ? LIMIT 1
");
$sql->execute([$id_variante, $id_producto, $id_talla, $id_color]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

// Comprobar si hay suficiente stock
if ($row && intval($row['stock']) >= $carrito[$key]['cantidad']) {
    $datos['ok'] = true;
    $datos['numero'] = count($carrito);  // Total de variantes únicas
} else {
    unset($carrito[$key]);  // Quitar si no hay stock suficiente
    $datos['ok'] = false;
    $datos['error'] = 'Stock insuficiente';
}

echo json_encode($datos);
