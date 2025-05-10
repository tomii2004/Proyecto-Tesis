<?php
require '../../modelos/basededatos.php';
$pdo = BasedeDatos::Conectar();

if (isset($_POST['id_producto'], $_POST['id_talla'], $_POST['id_color'])) {
    $id_producto = $_POST['id_producto'];
    $id_talla = $_POST['id_talla'];
    $id_color = $_POST['id_color'];

    $stmt = $pdo->prepare("SELECT ID_producvar, stock FROM productos_variantes 
                           WHERE ID_producto = ? AND ID_talla = ? AND ID_color = ?");
    $stmt->execute([$id_producto, $id_talla, $id_color]);
    $variante = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($variante) {
        echo json_encode([
            'success' => true,
            'id_variante' => $variante['ID_producvar'],
            'stock' => $variante['stock']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Variante no encontrada. Verifique el producto, talle y color.'
        ]);
    }
}

