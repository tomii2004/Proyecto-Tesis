<?php
// guardar_direccion_sesion.php
session_start();

// Leemos el JSON que nos envía el fetch de pago.php
$datos = json_decode(file_get_contents('php://input'), true);

// Si no viene nada, terminamos
if (!is_array($datos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Formato inválido']);
    exit;
}

// Guardamos en sesión los tres campos de dirección
$_SESSION['direccion_envio'] = [
    'calle'         => trim($datos['calle'] ?? ''),
    'numero'        => trim($datos['numero'] ?? ''),
    'codigo_postal' => trim($datos['codigopostal'] ?? '')
];

// Respondemos OK (puede ser vacío)
http_response_code(200);
echo json_encode(['status' => 'ok']);
exit;
