<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
include '../../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();
require '../../modelos/configproduct-detail.php';

// Obtener la información de Mercado Pago
$id_transaccion = isset($_GET['collection_id']) ? $_GET['collection_id'] : '';

if ($id_transaccion) {
    // Obtener datos de la API de Mercado Pago
    $url = "https://api.mercadopago.com/v1/payments/{$id_transaccion}?access_token=" . TOKEN_MP;
    $response = file_get_contents($url);
    $datos = json_decode($response, true);

    if ($datos && isset($datos['status']) && $datos['status'] == "approved") {
        $idCliente = $_SESSION['user_cliente'];
        $consulta_cliente = $conexion->prepare("SELECT email FROM clientes_compras WHERE ID_clientes_compras = ? AND estado = 1;");
        $consulta_cliente->execute([$idCliente]);
        $row_cliente = $consulta_cliente->fetch(PDO::FETCH_ASSOC);

        $total = $datos['transaction_details']['total_paid_amount'];
        $estado = $datos['status'];
        $fecha_nueva = date('Y-m-d H:i:s', strtotime($datos['date_approved']));
        $email = $row_cliente['email'];

        // Guardar la compra en la base de datos
        $sql = $conexion->prepare("INSERT INTO compras (ID_transaccion, fecha, estado, email, ID_cliente, total, medio_pago) VALUES (?, ?, ?, ?, ?, ?, ?);");
        $sql->execute([$id_transaccion, $fecha_nueva, $estado, $email, $idCliente, $total, 'MercadoPago']);
        $id = $conexion->lastInsertId();

        if ($id > 0) {
            $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
            if ($productos != null) {
                foreach ($productos as $clave => $cantidad) {
                    $consulta_productos = $conexion->prepare("SELECT * FROM producto WHERE ID_producto = ? AND estado = 1;");
                    $consulta_productos->execute([$clave]);
                    $row_prod = $consulta_productos->fetch(PDO::FETCH_ASSOC);

                    $precio = $row_prod['precio'];
                    $sql_insert = $conexion->prepare("INSERT INTO ventas_producto (ID_compra, ID_producto, cantidad, nombre, precio) VALUES (?, ?, ?, ?, ?);");
                    if ($sql_insert->execute([$id, $clave, $cantidad, $row_prod['nombre'], $precio])) {
                        restarStock($clave, $cantidad, $conexion);
                    }
                }
            }
            require 'mailer.php';

            $asunto = "Detalle de su compra";
            $cuerpo = "<h4>Gracias por su compra</h4>";
            $cuerpo .= "<p>El ID de su compra es <b>{$id_transaccion}</b></p>";

            $mailer = new Mailer();
            $mailer->enviarEmail($email, $asunto, $cuerpo);
        }
        unset($_SESSION['carrito']);
        header("Location: ../completado.php?key=" . $id_transaccion);
        exit;
    }
}

echo "Error al procesar el pago.";
exit;

function restarStock($id, $cantidad, $conexion) {
    $sql = $conexion->prepare("UPDATE producto SET stock = GREATEST(stock - ?, 0) WHERE ID_producto = ?");
    $sql->execute([$cantidad, $id]);

    $consulta = $conexion->prepare("SELECT stock FROM producto WHERE ID_producto = ?");
    $consulta->execute([$id]);
    $row = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($row['stock'] == 0) {
        $sql_estado = $conexion->prepare("UPDATE producto SET estado = 0 WHERE ID_producto = ?");
        $sql_estado->execute([$id]);
    }
}
?>
