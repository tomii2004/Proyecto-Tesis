<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
include '../../modelos/basededatos.php';
require '../../modelos/configproduct-detail.php';

$conexion = BasedeDatos::Conectar();


$id_transaccion = $_GET['collection_id'] ?? $_GET['payment_id'] ?? '';
if ($id_transaccion) {
    $url = "https://api.mercadopago.com/v1/payments/{$id_transaccion}?access_token=" . TOKEN_MP;
    $response = file_get_contents($url);
    $datos = json_decode($response, true);
   

    if (!$datos) {
        header("Location: ../error.php?msg=json");
        exit;
    }

    if ($datos['status'] === "approved") {
        

        if (empty($_SESSION['user_cliente'])) {
            header("Location: ../login.php");
            exit;
        }

        $idCliente = $_SESSION['user_cliente'];
        $consulta_cliente = $conexion->prepare("SELECT email FROM clientes_compras WHERE ID_clientes_compras = ? AND estado = 1;");
        $consulta_cliente->execute([$idCliente]);
        $row_cliente = $consulta_cliente->fetch(PDO::FETCH_ASSOC);

        if (!$row_cliente) {
            header("Location: ../error.php?msg=cliente");
            exit;
        }

        $email = $row_cliente['email'];
        $total = $datos['transaction_details']['total_paid_amount'];
        $estado = $datos['status'];
        $fecha_nueva = date('Y-m-d H:i:s', strtotime($datos['date_approved']));

        // Verificar duplicado
        $verificar = $conexion->prepare("SELECT 1 FROM compras WHERE ID_transaccion = ?");
        $verificar->execute([$id_transaccion]);

        if ($verificar->fetch()) {
            header("Location: ../completado.php?key=" . $id_transaccion);
            exit;
        }

        // Obtener carrito
        $variantes = $_SESSION['carrito']['variantes'] ?? [];

        // Validar stock antes de continuar
        foreach ($variantes as $item) {
            $idVariante = $item['id_variante'];
            $cantidad = $item['cantidad'];
        
            $stmt = $conexion->prepare("SELECT stock FROM productos_variantes WHERE ID_producvar = ?");
            $stmt->execute([$idVariante]);
            $stock = $stmt->fetchColumn();
            
        
            if ($stock === false || $stock < $cantidad) {
                $_SESSION['error_stock'] = "Stock insuficiente para una o más variantes.";
                header("Location: ../carrito.php");
                exit;
            }
        }

        // Registrar compra
        $sql = $conexion->prepare("INSERT INTO compras (ID_transaccion, fecha, estado, email, ID_cliente, total, medio_pago) VALUES (?, ?, ?, ?, ?, ?, ?);");
        $sql->execute([$id_transaccion, $fecha_nueva, $estado, $email, $idCliente, $total, 'MercadoPago']);
        $idCompra = $conexion->lastInsertId();

        // Insertar productos
        foreach ($variantes as $item) {
            $idProducto = $item['id'];
            $idVariante = $item['id_variante'];
            $cantidad = $item['cantidad'];

            $stmt = $conexion->prepare("SELECT pv.precio, p.nombre FROM productos_variantes pv JOIN producto p ON pv.ID_producto = p.ID_producto WHERE pv.ID_producvar = ?");
            $stmt->execute([$idVariante]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $insert = $conexion->prepare("INSERT INTO ventas_producto (ID_compra, ID_producto, ID_variante, cantidad, nombre, precio) VALUES (?, ?, ?, ?, ?, ?);");
                $insert->execute([$idCompra, $idProducto, $idVariante, $cantidad, $row['nombre'], $row['precio']]);

                restarStock($idVariante, $cantidad, $conexion);
            }
        }

        // Enviar mail
        require 'mailer.php';
       
        $mailer = new Mailer();
        $asunto = "¡Gracias por tu compra! Aquí están los detalles";
        $cuerpo = "<h3>¡Gracias por confiar en nosotros!</h3>";
        $cuerpo .= "<p>Tu compra ha sido procesada con éxito. A continuación, te dejamos los detalles:</p>";
        $cuerpo .= "<p><b>ID de la compra:</b> {$id_transaccion}</p>";
        $cuerpo .= "<p><b>Fecha de compra:</b> {$fecha_nueva}</p>";
        $cuerpo .= "<p><b>Total pagado:</b> $" . number_format($total, 2) . "</p>";
        $cuerpo .= "<p><b>Método de pago:</b> Mercado Pago</p>";
        $cuerpo .= "<br><p><b>¡Esperamos que disfrutes tu compra!</b></p>";
        $mailer->enviarEmail($email, $asunto, $cuerpo);
      
        unset($_SESSION['carrito']);
        header("Location: ../completado.php?key=" . $id_transaccion);
        exit;
    }
}

header("Location: ../error.php?msg=pago");
exit;

function restarStock($idVariante, $cantidad, $conexion) {
    $conexion->prepare("UPDATE productos_variantes SET stock = GREATEST(stock - ?, 0) WHERE ID_producvar = ?")
             ->execute([$cantidad, $idVariante]);

    $stmt = $conexion->prepare("SELECT ID_producto FROM productos_variantes WHERE ID_producvar = ?");
    $stmt->execute([$idVariante]);
    $idProducto = $stmt->fetchColumn();

    $check = $conexion->prepare("SELECT COUNT(*) FROM productos_variantes WHERE ID_producto = ? AND stock > 0");
    $check->execute([$idProducto]);
    $conStock = $check->fetchColumn();

    $estado = ($conStock == 0) ? 0 : 1;
    $conexion->prepare("UPDATE producto SET estado = ? WHERE ID_producto = ?")->execute([$estado, $idProducto]);
}
