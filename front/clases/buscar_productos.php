<?php
require '../../modelos/basededatos.php';// Conexión a la base de datos

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($query !== '') {
    // Si hay un término de búsqueda, buscar productos que coincidan
    $stmt = $pdo->prepare("SELECT ID_producto, nombre, descripcion, precio, ruta_imagen FROM producto WHERE nombre LIKE :query LIMIT 10");
    $stmt->execute(['query' => "%$query%"]);
} else {
    // Si no hay búsqueda, devolver todos los productos
    $stmt = $pdo->query("SELECT ID_producto, nombre, descripcion, precio, ruta_imagen FROM producto LIMIT 20");
}

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($productos);
?>