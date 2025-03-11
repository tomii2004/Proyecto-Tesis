<?php
session_start();
if (isset($_SESSION['carrito'])) {
    unset($_SESSION['carrito']); // Elimina el carrito de la sesión
}
echo json_encode(["success" => true]);
?>