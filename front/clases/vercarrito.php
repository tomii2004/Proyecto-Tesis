<?php
session_start();
header('Content-Type: application/json');

// Mostramos todo el contenido del carrito
echo json_encode([
    'carrito' => isset($_SESSION['carrito']) ? $_SESSION['carrito'] : null
], JSON_PRETTY_PRINT);
