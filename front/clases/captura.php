
<?php

date_default_timezone_set('America/Argentina/Buenos_Aires'); //para que la hora sea argentina

include '../../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();

require '../../modelos/configproduct-detail.php';

$json = file_get_contents('php://input'); //captura toda la informacion
$datos = json_decode($json,true); //esto lo procesa

echo "<pre>";
print_r($datos);
echo "<pre>";



if(is_array($datos)){

    $idCliente = $_SESSION['user_cliente'];
    $consulta_cliente = $conexion -> prepare("SELECT email FROM clientes_compras WHERE ID_clientes_compras = ? AND estado = 1; ");
    $consulta_cliente ->execute([$idCliente]);
    $row_cliente = $consulta_cliente -> fetch(PDO::FETCH_ASSOC);

    $id_transaccion = $datos['detalles']['id'];
    $total = $datos['detalles']['purchase_units'][0]['amount']['value'];
    $estado = $datos['detalles']['status'];
    $fecha = $datos['detalles']['update_time'];
    $fecha_nueva = date('Y-m-d H:i:s',strtotime($fecha));
    //$email = $datos['detalles']['payer']['email_address'];
    $email = $row_cliente['email'];
    //$id_cliente = $datos['detalles']['payer']['payer_id'];
    

    $sql = $conexion -> prepare("INSERT INTO compras(ID_transaccion,fecha,estado,email,ID_cliente,total,medio_pago)VALUES(?,?,?,?,?,?,?);");
    $sql -> execute([$id_transaccion,$fecha_nueva,$estado,$email,$idCliente,$total,'PayPal']);
    $id = $conexion->lastInsertId();


    if($id > 0){
        $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
        if($productos != null){
            foreach($productos as $clave => $cantidad){ // la clave es el id del producto 
                $consulta_productos = $conexion -> prepare("SELECT * FROM producto WHERE ID_producto = ? AND estado = 1; ");
                $consulta_productos ->execute([$clave]);
                $row_prod = $consulta_productos -> fetch(PDO::FETCH_ASSOC); // va a traer producto por producto 

                $precio = $row_prod['precio'];
				
                $sql_insert = $conexion -> prepare("INSERT INTO ventas_producto(ID_compra,ID_producto,cantidad,nombre,precio)VALUES (?,?,?,?,?);");
                if($sql_insert -> execute([$id,$clave,$cantidad,$row_prod['nombre'],$precio])){
                    restarStock($clave,$cantidad,$conexion);
                }
            }
        }
        require 'mailer.php'; // esto lo hago para tomar las variables de este script 

        $asunto = "Detalle de su compra";
        $cuerpo = '<h4>Gracias por su compra </h4>';
        $cuerpo .='<p>El ID de su compra es <b>'. $id_transaccion. '</b></p>';
        
        $mailer = new Mailer();
        $mailer ->enviarEmail($email,$asunto,$cuerpo);


    }
    unset($_SESSION['carrito']); //para eliminar el carrito luego de la compra

    
}

function restarStock($id, $cantidad, $conexion) {
    // Primero, restamos el stock
    $sql = $conexion->prepare("UPDATE producto SET stock = GREATEST(stock - ?, 0) WHERE ID_producto = ?");
    $sql->execute([$cantidad, $id]);

    // Luego, obtenemos el stock actualizado para ver si llegó a 0
    $consulta = $conexion->prepare("SELECT stock FROM producto WHERE ID_producto = ?");
    $consulta->execute([$id]);
    $row = $consulta->fetch(PDO::FETCH_ASSOC);
    
    // Si el stock es 0, actualizamos el estado a inactivo
    if ($row['stock'] == 0) {
        $sql_estado = $conexion->prepare("UPDATE producto SET estado = 0 WHERE ID_producto = ?");
        $sql_estado->execute([$id]);
    }
}






?>