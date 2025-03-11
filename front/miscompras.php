<?php
include '../paginalogin/funcionesregister.php';
include '../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();

require '../modelos/configproduct-detail.php';

$token = generaToken();
$_SESSION['token'] = $token;

// print_r($_SESSION);
$idCliente = $_SESSION['user_cliente'];

$sql = $conexion ->prepare("SELECT ID_transaccion,fecha,estado,total,medio_pago FROM compras WHERE ID_cliente = ? ORDER BY fecha DESC");
$sql ->execute([$idCliente]);

// Verifica si hay compras
if ($sql->rowCount() == 0) {
    $noCompras = true;
} else {
    $noCompras = false;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Historial De Compras</title>
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
                <h3>Mis Compras</h3>
				<hr>
				<?php if ($noCompras): ?>
					<p>No tienes compras registradas.</p>
				<?php else: ?>
					<?php while($row = $sql ->fetch(PDO::FETCH_ASSOC)){ ?>

					<div class="card mb-3 border-success ">
						<div class="card-header">
							<?php echo $row['fecha'] ;?>
						</div>
						<div class="card-body">
							<h5 class="card-title">Folio: <?php echo $row['ID_transaccion'] ;?></h5>
							<p class="card-text">Total: <?php echo number_format($row['total'],2,'.',',' );?></p>
							<a href="compra_detalle.php?orden=<?php echo $row['ID_transaccion'];?>&token=<?php echo $token;?>" class="btn btn-primary">Ver compra</a>
						</div>
					</div>
				<?php } ?>
				<?php endif; ?>
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