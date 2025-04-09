<!DOCTYPE html>
<html lang="en">
<head>
	<title>Carrito de Compras</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://sdk.mercadopago.com/js/v2"></script>
	
<!--===============================================================================================-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
<!--===============================================================================================-->
<!--===============================================================================================-->	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="../imagenes/favicon-32x32.png"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body class="animsition">

	<?php
	include '../modelos/basededatos.php';

	$conexion = BasedeDatos::Conectar();
	
	require '../modelos/configproduct-detail.php';

	////////////// MERCADO PAGO//////////

	// Desactiva la notificación de errores deprecados en PHP
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	require_once 'vendor/autoload.php';
	
	// Importa las clases necesarias del SDK de MercadoPago
	use MercadoPago\Client\Preference\PreferenceClient;
	use MercadoPago\MercadoPagoConfig;

	MercadoPagoConfig::setAccessToken(TOKEN_MP);

	$client = new PreferenceClient();
	

	
	///////////////////////////////////////


	$productos = isset($_SESSION['carrito']['variantes']) ? $_SESSION['carrito']['variantes'] : null;

	// print_r($_SESSION);

	$lista_carrito = array();
	if($productos != null){
		foreach ($productos as $clave => $info) {
			$id_variante = $info['id_variante'];
			$cantidad = $info['cantidad'];
		
			$consulta = $conexion->prepare("
				SELECT p.ID_producto, p.nombre, p.ruta_imagen, 
					   v.precio, v.ID_producvar, v.ID_talla, v.ID_color 
				FROM producto p 
				INNER JOIN productos_variantes v ON p.ID_producto = v.ID_producto 
				WHERE v.ID_producvar = ?
			");
			$consulta->execute([$id_variante]);
			$datos = $consulta->fetch(PDO::FETCH_ASSOC);
		
			if ($datos) {
				$datos['cantidad'] = $cantidad;
				$datos['clave'] = $clave;
				$lista_carrito[] = $datos;
			}
		}
	}else{
        header("Location: index.php");
        exit;
    }

	
	?>
	
	<?php include 'menu.php' ;?>

	


	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
				Inicio
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Carrito de Compras
			</span>
		</div>
	</div>
		

	<!-- Shoping Cart -->
	<form class="bg0 p-t-75 p-b-85">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
					<div class="m-l-25 m-r--38 m-lr-0-xl">
						<div class="wrap-table-shopping-cart">
							<table class="table-shopping-cart">
								<tr class="table_head">
									<th class="column-1">Producto</th>
									<th class="column-2"></th>
									<th class="column-5">Subtotal</th>
	
								</tr>
								<?php if($lista_carrito == null){
										echo '<tr><td colspan= "5" class= "txt-center"><b>Lista Vacia</b></td></tr>';
									}else{
										$total = 0;
										$productos_mp = array();

										foreach($lista_carrito as $producto){
											$id = $producto['ID_producto'];
											$nombre = $producto['nombre'];
											$precio = $producto['precio'];
											$cantidad = $producto['cantidad'];
											$imagen = '../'. $producto['ruta_imagen'];
											$subtotal = $cantidad * $precio;
											$total += $subtotal;	
											
											$productos_mp[] = [
												"id" => $producto['clave'], // Usamos IDproducto|IDvariante
												"title" => $producto['nombre'] . " - Talle " . $producto['ID_talla'] . " / Color " . $producto['ID_color'],
												"quantity" => (int) $producto['cantidad'], // Asegurar que sea int
												"unit_price" => (float) $producto['precio'], // Asegurar que sea float
												"currency_id" => "ARS"
											];
								?>
								<tr class="table_row">
									
									<td class="column-1">
										<div class="how-itemcart1">
											<img src="<?php echo $imagen ?>" alt="IMG">
										</div>
									</td>
									<td class="column-2"><?php echo $nombre; ?></td>
									<td class="column-5">
										<div id= "subtotal_<?php echo $id?>" name="subtotal[]">
											<?php echo MONEY;?> <?php echo number_format($subtotal,2,'.',','); ?>
										</div>
									</td>
									
								</tr>
								<?php }}?>
							</table>
						</div>

					</div>
				</div>

				<div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
					<div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
						<h4 class="mtext-109 cl13 p-b-30">
							Carrito de Compras
						</h4>

						<div class="flex-w flex-t bor12 p-b-13">
							<div class="size-208">
								<span class="stext-110 cl13" >
									Total: 
								</span>
							</div>

							<div class="size-209">
								<span class="mtext-110 cl13" id="total">
									<?php echo MONEY; ?><?php echo number_format($total,2,'.',','); ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<div id="wallet_container"></div> 
	


	<?php include 'footer.php' ; ?>
<!--=============================MERCADO PAGO================================================-->
	

	<?php
	$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/pruebastesis/front/clases/";
	$preference = null;
	try {
		$preference = $client->create([
			"items" => $productos_mp,
			"back_urls" => [
				"success" => $base_url . "captura_MP.php",
				"failure" => $base_url . "failure.php",
				"pending" => $base_url . "pending.php"
			],
			"auto_return" => "approved",
			"binary_mode" => true,
			
		]);
	} catch (Exception $e) {
		echo "Error al crear la preferencia: " . $e->getMessage();
		exit;
	}

	$preferenceId = $preference ? $preference->id : null;
	?>

<script>
    const mp = new MercadoPago("<?php echo PUBLIC_KEY_MP?>", { locale: 'es-AR' });

    mp.bricks().create("wallet", "wallet_container", {
        initialization: {
            preferenceId: "<?php echo $preferenceId; ?>"
        },
        customization: {
            texts: {
                action: "pay",
                valueProp: 'security_safety',
            },
        },
        callbacks: {
            onError: function(error) {
				alert("Error en el pago: " + error.message);
			}
        }
    });
</script>

<!--===============================================================================================-->
								
<!--===============================================================================================-->	
	
<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
	<script>
		$(".js-select2").each(function(){
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script>
		$('.js-pscroll').each(function(){
			$(this).css('position','relative');
			$(this).css('overflow','hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function(){
				ps.update();
			})
		});
	</script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>

</html>