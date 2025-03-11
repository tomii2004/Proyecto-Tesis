<?php 
	//esto es para que se sume el numero del carrito en todas las pantallas
	require '../../modelos/configproduct-detail.php';
	require '../../modelos/basededatos.php';

	header('Content-Type: application/json');

	$datos['ok']= false;

	if(isset($_POST['id'])){
		$id = $_POST['id'];
		$cantidad = isset($_POST['cantidad'])? $_POST['cantidad'] : 1;
		$token = $_POST['token'];
		$token_tmp = hash_hmac('sha1', $id , KEY_TOKEN);

		if($token == $token_tmp && $cantidad > 0 && is_numeric($cantidad)){

			if(isset($_SESSION['carrito']['productos'][$id])){
				$cantidad += $_SESSION['carrito']['productos'][$id];
			}

			$conexion = BasedeDatos::Conectar();
            $sql_producto = $conexion ->prepare("SELECT stock FROM producto where ID_producto = ? AND estado = 1 LIMIT 1;");
			$sql_producto ->execute([$id]);
			$row = $sql_producto->fetch(PDO::FETCH_ASSOC);
			$stock = $row['stock'];

			if($stock >= $cantidad){
				$datos['ok'] = true;
				$_SESSION['carrito']['productos'][$id] = $cantidad;
				$datos['numero']= count($_SESSION['carrito']['productos']);
			}
		}
	}

	echo json_encode($datos);
?>