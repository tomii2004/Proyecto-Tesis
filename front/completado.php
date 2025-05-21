<?php
include '../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();

require '../modelos/configproduct-detail.php';

$id_transaccion = isset($_GET['key']) ? $_GET['key'] : '0';
$error = '';
if($id_transaccion == ''|| $id_transaccion == 0){
    $error = 'Error al procesar la peticion';
}else{
    $sql_producto = $conexion ->prepare("SELECT count(ID_compra) FROM compras where ID_transaccion = ? AND estado = ? ;");
    $sql_producto ->execute([$id_transaccion,'approved']);
	$existe = $sql_producto ->fetchColumn();
    if($existe > 0){
        $sql_producto = $conexion ->prepare("SELECT ID_compra,fecha,email,total,costo_envio FROM compras where ID_transaccion = ? AND estado = ? LIMIT 1;");
        $sql_producto ->execute([$id_transaccion,'approved']);
        $row = $sql_producto->fetch(PDO::FETCH_ASSOC);
        
		if ($row) {
			$idcompra = $row['ID_compra'];
			$total = $row['total'];
			$costo_envio = $row['costo_envio'];
			$fecha = new DateTime($row['fecha']);
			$fecha_nueva = $fecha->format('d/m/Y H:i');
	
			$sqldetalles = $sqldetalles = $conexion->prepare("
			SELECT vp.nombre, vp.precio, vp.cantidad,
				   t.nombre AS talla, c.nombre AS color,p.ruta_imagen
			FROM ventas_producto vp
			INNER JOIN productos_variantes v ON vp.ID_variante = v.ID_producvar
			INNER JOIN c_talla t ON v.ID_talla = t.ID_talla
			INNER JOIN c_colores c ON v.ID_color = c.ID_colores
			INNER JOIN producto p ON vp.ID_producto = p.ID_producto
			WHERE vp.ID_compra = ?");
			
			$sqldetalles->execute([$idcompra]);
		} else {
			$error = 'Error al obtener detalles de la compra';
		}
    }else{
        $error = 'Error al comprobar la compra';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Detalles de Compra</title>
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
</head>
<body class="animsition">

	<?php include 'menu.php';?>

	


	<!-- breadcrumb -->
	<div class="container" style="margin-top: 100px;">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
				Inicio
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				Detalles de Compra
			</span>
		</div>
	</div>

    <main>
        <form class="bg0 p-t-75 p-b-85">
            <div class="container">
                <?php if(strlen($error)> 0){ ?>
                    <div class = "row">
                        <div class="col">
                            <h3><?php echo $error; ?></h3>
                        </div>
                    </div>
                    <?php } else { ?>
                        <div class="row">
							<div class="col">
								<div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
									<div class="m-l-25 m-r--38 m-lr-0-xl">
										<div class="wrap-table-shopping-cart">
											<table class="table-shopping-cart">
												<tr class="table_head">
													<th class="column-1">Imagen</th>
													<th class="column-2">Producto</th>
													<th class="column-4">Cantidad</th>
													<th class="column-5">Importe</th>
												</tr>
												<?php while($row_det = $sqldetalles->fetch(PDO::FETCH_ASSOC)) {
													$importe = $row_det['precio'] * $row_det['cantidad'];
													$imagen = !empty($row_det['ruta_imagen']) ? $row_det['ruta_imagen'] : 'imagen-no-disponible.png'; ?>
													<tr class="table_row">
														<td class="column-1"><img src="../<?php echo $imagen; ?>" alt="Producto" width="60" height="60" style="object-fit: cover; border-radius: 5px;"></td>
														<td class="column-2"><?php echo $row_det['nombre']; ?><br><small>Talle: <?php echo $row_det['talla']; ?> | Color: <?php echo $row_det['color']; ?></small></td>
														<td class="column-4"><?php echo $row_det['cantidad']; ?></td>
														<td class="column-5"><?php echo number_format($importe, 2, '.', ','); ?></td>
														
													</tr>
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

                        <div class="row">
                            <div class="col text-center">
                                <b> Folio de la compra: </b><?php echo $id_transaccion; ?><br>
                                <b> Fecha de la compra: </b><?php echo $fecha_nueva; ?><br>
								<b>Costo de envío: </b><?php echo MONEY . number_format($costo_envio, 2, '.', ','); ?><br>
                                <b> Total (Productos + Envio): </b><?php echo MONEY . number_format($total,2,'.',','); ?><br>
								<a href="index.php">Volver al Inicio</a>
                            </div>
							
                        </div>
                    <?php } ?> 
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