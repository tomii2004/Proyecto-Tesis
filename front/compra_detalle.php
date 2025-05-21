<?php 

include '../paginalogin/funcionesregister.php';
include '../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();

require '../modelos/configproduct-detail.php';

// print_r($_SESSION);


$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if($orden == null || $token == null || $token != $token_session){
    header("Location: miscompras.php");
    exit;
}

$sqlcompra = $conexion ->prepare ("SELECT ID_compra,ID_transaccion,fecha,total,costo_envio FROM compras WHERE ID_transaccion = ? LIMIT 1");
$sqlcompra ->execute([$orden]);
$rowcompra = $sqlcompra ->fetch(PDO::FETCH_ASSOC);
$idcompra = $rowcompra['ID_compra'];
$fecha = new DateTime($rowcompra['fecha']);
$fecha = $fecha ->format('d/m/Y H:i');

$sqldetalle = $conexion->prepare("
    SELECT 
        vp.ID_venta_producto,
        vp.nombre,
        vp.precio,
        vp.cantidad,
        pv.ID_talla,
        pv.ID_color,
        t.nombre AS nombre_talla,
        c.nombre AS nombre_color,
        p.ruta_imagen
    FROM ventas_producto vp
    JOIN productos_variantes pv ON vp.ID_variante = pv.ID_producvar
    JOIN c_talla t ON pv.ID_talla = t.ID_talla
    JOIN c_colores c ON pv.ID_color = c.ID_colores
    JOIN producto p ON vp.ID_producto = p.ID_producto
    WHERE vp.ID_compra = ?
");
$sqldetalle -> execute([$idcompra]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Detalle De Compras</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	<style>
		table td {
			padding: 15px 10px !important;
			vertical-align: middle !important;
		}

		table th {
			text-align: center;
			vertical-align: middle;
		}

		table tr:not(:last-child) {
			border-bottom: 1px solid #dee2e6;
		}
	</style>

</head>
<body class="animsition">

	<?php include 'menu.php';?>

	


	<!-- breadcrumb -->
	<div class="container " style="margin-top: 100px;">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
				Inicio
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Historial De Compras
			</span>
		</div>
	</div>

    <main>
        <form class="bg0 p-t-75 p-b-85">
            <div class="container">
                <div class="row">
                    <div class= "col-12 col-md-4">
                        <div class = "card mb-3">
                            <div class="card-header">
                                <strong>Detalle de la compra</strong>
                            </div>
                            <div class ="card-body">
                                <p><strong>Fecha: </strong><?php echo $fecha;?></p>
                                <p><strong>Orden: </strong><?php echo $rowcompra['ID_transaccion'];?></p>
								<p><strong>Envio: </strong><?php echo MONEY .' '. number_format($rowcompra['costo_envio'],2,',','.');?></p>
                                <p><strong>Total: </strong><?php echo MONEY .' '. number_format($rowcompra['total'],2,',','.');?></p>
                                </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="table-responsive">
                            <table class = "table">
                                <thead>
                                    <tr>
                                        <th>Producto:</th>
                                        <th>Precio:</th>
                                        <th>Cantidad:</th>
                                        <th>Subtotal:</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
									<?php while($row = $sqldetalle ->fetch(PDO::FETCH_ASSOC)){
										$precio = $row['precio'];
										$cantidad = $row['cantidad'];
										$subtotal = $precio * $cantidad;
										$talle = $row['nombre_talla'];
										$color = $row['nombre_color'];
										$imagen = !empty($row['ruta_imagen']) ? $row['ruta_imagen'] : 'imagen-no-disponible.png';
									?>
									<tr>
										<td>
											<div style="display: flex; align-items: center; gap: 10px;">
												<img src="../<?php echo $imagen; ?>" alt="Producto" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
												<div>
													<span><?php echo $row['nombre']; ?></span><br>
													<small><b>Talle:</b> <?php echo $talle; ?> | <b>Color:</b> <?php echo $color; ?></small>
												</div>
											</div>
										</td>
										<td class="align-middle text-center"><?php echo MONEY .' '. number_format($precio,2,',','.');?></td>
										<td class="align-middle text-center"><?php echo $cantidad ?></td>
										<td class="align-middle text-center"><?php echo MONEY .' '. number_format($subtotal,2,',','.');?></td>
									</tr>
									<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
		

	

	<?php include 'footer.php' ; ?>


								
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




?>