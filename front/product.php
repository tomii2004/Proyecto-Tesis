<!DOCTYPE html>
<html lang="en">
<head>
	<title>Productos</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
<!--===============================================================================================-->
	
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
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/slick/slick.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/MagnificPopup/magnific-popup.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
	<style>
		.wrap-filter {
			max-height: 250px; /* Ajusta según la necesidad */
			overflow-y: auto;
		}
	</style>
	

</head>

<?php
include '../modelos/basededatos.php';

$conexion = BasedeDatos::Conectar();

include '../modelos/configproduct-detail.php';
?>
<body class="animsition">
	
	<?php include 'menu.php' ;?>

	
	<?php
	$idCategoria = $_GET['cat'] ?? null;
	$idColor = $_GET['color'] ?? null;
	$idTalla = $_GET['talla'] ?? null;
	$genero = $_GET['genero'] ?? null;
	
	$search = $_GET['search'] ?? null;

	$orden = $_GET['orden'] ?? '';
	$orders=[
		'asc'=> 'nombre ASC',
		'desc'=> 'nombre DESC',
		'precio_alto'=> 'precio DESC',
		'precio_bajo'=> 'precio ASC',
	];

	$order = $orders[$orden] ?? '';
	
	$query = "SELECT * FROM producto WHERE estado = 1 ";
	$params = [];
	
	if (!empty($idCategoria)) {
		$query .= " AND categoria = ? ";
		$params[] = $idCategoria;
	}
	
	if (!empty($idColor)) {
		$query .= " AND color = ? ";
		$params[] = $idColor;
	}
	
	if (!empty($idTalla)) {
		$query .= " AND talle = ? ";
		$params[] = $idTalla;
	}
	
	if (!empty($genero)) {
		$query .= " AND genero = ? ";
		$params[] = $genero;
	}

	if (!empty($search)) {
		$query .= " AND nombre LIKE ? ";
		$params[] = "%$search%";
	}

	if (!empty($order)) {
		$query .= " ORDER BY $order";
	}

	$consulta_productos = $conexion->prepare($query);
	$consulta_productos->execute($params);
	$resultado_productos = $consulta_productos->fetchAll(PDO::FETCH_ASSOC);
	
	$categorias = $conexion->query("SELECT c.ID_categoria, c.nombre, c.activo, COUNT(p.ID_producto) AS cantidad_productos FROM categoria c LEFT JOIN producto p ON c.ID_categoria = p.categoria WHERE c.activo = 1 GROUP BY c.ID_categoria, c.nombre, c.activo HAVING cantidad_productos > 0")->fetchAll(PDO::FETCH_ASSOC);
	$colores = $conexion->query("SELECT c.ID_colores,c.nombre,COUNT(p.ID_producto)AS cantidad_productos FROM c_colores c LEFT JOIN producto p ON c.ID_colores = p.color GROUP BY c.ID_colores,c.nombre HAVING cantidad_productos > 0")->fetchAll(PDO::FETCH_ASSOC);
	$tallas = $conexion->query("SELECT t.ID_talla,t.nombre,COUNT(p.ID_producto) AS cantidad_productos FROM c_talla t LEFT JOIN producto p ON t.ID_talla = p.talle GROUP BY t.ID_talla,t.nombre HAVING cantidad_productos > 0")->fetchAll(PDO::FETCH_ASSOC);
	?>
	
	<!-- Product -->
	<div class="bg0 m-t-23 p-b-140 " style="margin-top: 100px;">
		<div class="container">
			<div class="flex-w flex-sb-m ">
				<div class="flex-w flex-l-m filter-tope-group m-tb-10">
					<a href="product.php" class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 
					<?php if(empty($genero)) echo 'how-active1'; ?>">
						Todos los productos
					</a>

					<a href="product.php?cat=<?php echo $idCategoria; ?>&color=<?php echo $idColor; ?>&talla=<?php echo $idTalla; ?>&genero=1&orden=<?php echo $orden; ?>&search=<?php echo $search; ?>" 
						class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 
						<?php if($genero == 1) echo 'how-active1'; ?>">
						Hombres
					</a>

					<a href="product.php?cat=<?php echo $idCategoria; ?>&color=<?php echo $idColor; ?>&talla=<?php echo $idTalla; ?>&genero=2&orden=<?php echo $orden; ?>&search=<?php echo $search; ?>" 
						class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 
						<?php if($genero == 2) echo 'how-active1'; ?>">
						Mujeres
					</a>

					<a href="product.php?cat=<?php echo $idCategoria; ?>&color=<?php echo $idColor; ?>&talla=<?php echo $idTalla; ?>&genero=3&orden=<?php echo $orden; ?>&search=<?php echo $search; ?>" 
						class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 
						<?php if($genero == 3) echo 'how-active1'; ?>">
						Unisex
					</a>
				</div>

				<div class="flex-w flex-c-m m-tb-10">
					<div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
						<i class="icon-filter cl13 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
						<i class="icon-close-filter cl13 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						 Filtrar
					</div>

					<div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
						<i class="icon-search cl13 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
						<i class="icon-close-search cl13 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
						Buscar
					</div>
					
				</div>
				
				<!-- Search product -->
				<div class="dis-none panel-search w-full p-t-10 p-b-15">
					<form id="searchForm" method="GET" action="product.php" class="bor8 dis-flex p-l-15">
						<button type="submit" class="size-113 flex-c-m fs-16 cl13 hov-cl1 trans-04">
							<i class="zmdi zmdi-search"></i>
						</button>
						<input class="mtext-107 cl13 size-114 plh2 p-r-15" type="text" name="search" placeholder="Search">
					</form>    
				</div>

				<!-- Filter -->
				<div class="dis-none panel-filter w-full p-t-10">
					<div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm wrap-filter">
						<div class="filter-col1 p-r-15 p-b-27">
							<div class="mtext-102 cl13 p-b-15">
								Categorias
							</div>
							<ul>
								<li class="p-b-6">
									<a href="product.php" class="filter-link stext-106 trans-04 ">
										Todo
									</a>
								</li>
							<?php foreach($categorias as $categoria){ ?>
								<li class="p-b-6">
									<a href="product.php?cat=<?php echo $categoria['ID_categoria']; ?>&color=<?php echo $idColor; ?>&talla=<?php echo $idTalla; ?>&genero=<?php echo $genero; ?>&orden=<?php echo $orden; ?>" class="filter-link stext-106 trans-04 <?php if($idCategoria == $categoria['ID_categoria'])echo 'filter-link-active' ?>">
										<?php echo ucfirst($categoria['nombre'])?>
									</a>
								</li>
							<?php } ?>
							<ul>
						</div>

						<div class="filter-col2 p-r-15 p-b-27">
							<div class="mtext-102 cl13 p-b-15">
								Colores
							</div>
							<ul>
								<li class="p-b-6">
									<a href="product.php" class="filter-link stext-106 trans-04 ">
										Todo
									</a>
								</li>
							<?php 
							foreach($colores as $color){ ?>
								<li class="p-b-6">
									<a href="product.php?cat=<?php echo $idCategoria; ?>&color=<?php echo $color['ID_colores']; ?>&talla=<?php echo $idTalla; ?>&genero=<?php echo $genero; ?>&orden=<?php echo $orden; ?>" class="filter-link stext-106 trans-04 <?php if($idColor == $color['ID_colores'])echo 'filter-link-active' ?>">
										<?php echo ucfirst($color['nombre'])?>
									</a>
								</li>
							<?php } ?>
							<ul>
							
						</div>

						<div class="filter-col3 p-r-15 p-b-27">
							<div class="mtext-102 cl13 p-b-15">
								Talles
							</div>

							<ul>
								<li class="p-b-6">
									<a href="product.php" class="filter-link stext-106 trans-04 ">
										Todo
									</a>
								</li>
							<?php 
							foreach($tallas as $talla){ ?>
								<li class="p-b-6">
									<a href="product.php?cat=<?php echo $idCategoria; ?>&color=<?php echo $idColor; ?>&talla=<?php echo $talla['ID_talla']; ?>&genero=<?php echo $genero; ?>&orden=<?php echo $orden; ?>" class="filter-link stext-106 trans-04 <?php if($idTalla == $talla['ID_talla'])echo 'filter-link-active' ?>">
										<?php echo ucfirst($talla['nombre'])?>
									</a>
								</li>
							<?php } ?>
							<ul>
						</div>

						
					</div>
				</div>
			</div>

			<div class="d-flex justify-content-end mt-2" style="width: 100%; padding-left: 20px;padding-bottom: 20px">
				<div style="width: 212px;">
					<form action="product.php" id="ordenForm" method="GET">
						
						<input type="hidden" name="cat" id="cat" value="<?php echo $idCategoria; ?>">
						<input type="hidden" name="color" id="color" value="<?php echo $idColor; ?>">
						<input type="hidden" name="talla" id="talla" value="<?php echo $idTalla; ?>">
						<input type="hidden" name="genero" id="genero" value="<?php echo $genero; ?>">
						<!-- ver para los otros filtros para poder ordenar  -->

						<select name="orden" id="orden" class="form-select stext-106 cl6 size-105 bor4 pointer  trans-04 w-100" onchange="submitForm()">
							<option value="">Ordenar por</option>
							<option value="precio_alto" <?php echo ($orden === 'precio_alto')? 'selected': '';?>>Precios más altos</option>
							<option value="precio_bajo" <?php echo ($orden === 'precio_bajo')? 'selected': '';?>>Precios más bajos</option>
							<option value="asc" <?php echo ($orden === 'asc')? 'selected': '';?>>Nombre A-Z</option>
							<option value="desc" <?php echo ($orden === 'desc')? 'selected': '';?>>Nombre Z-A</option>
						</select>
					</form>
				</div>
			</div>
			

			<div class="row isotope-grid">
				<?php if(empty($resultado_productos)){
					echo "<p class='text-left '>No hay productos disponibles</p>";
				}else{
				foreach($resultado_productos as $row){?>
				<div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item 
				<?php 
					if ($row['genero'] == 1) echo 'men'; 
					if ($row['genero'] == 2) echo 'women'; 
					if ($row['genero'] == 3) echo 'unisex'; 
     			?>">
					<?php
					$imagen = '../'. $row['ruta_imagen'];
					?>
					<!-- Block2 -->
					<div class="block2">
						<div class="block2-pic hov-img0">
							<img src="<?php echo $imagen ; ?>" alt="IMG-PRODUCT">

							<a href="product-detail.php?ID_producto=<?php echo $row['ID_producto'];?>&token=<?php echo urlencode(hash_hmac('sha1',$row['ID_producto'],KEY_TOKEN));?>" class="block2-btn flex-c-m stext-103 cl13 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 ">
								Ver Producto
							</a>
						</div>

						<div class="block2-txt flex-w flex-t p-t-14">
							<div class="block2-txt-child1 flex-col-l ">
								<a href="product-detail.php?ID_producto=<?php echo $row['ID_producto'];?>&token=<?php echo urlencode(hash_hmac('sha1',$row['ID_producto'],KEY_TOKEN));?>" class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
									<?php echo $row['nombre']; ?>
								</a>

								<span class="stext-105 cl3">
									<?php echo MONEY; ?><?php echo number_format($row['precio'],2,'.',','); ?>
								</span>
							</div>

							<div class="block2-txt-child2 flex-r p-t-3">
								<a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2">
									<img class="icon-heart1 dis-block trans-04" src="images/icons/icon-heart-01.png" alt="ICON">
									<img class="icon-heart2 dis-block trans-04 ab-t-l" src="images/icons/icon-heart-02.png" alt="ICON">
								</a>
							</div>
						</div>
					</div>
				</div>
				<?php }?>
				<?php }?>
			</div>
			

			<!-- Load more
			<div class="flex-c-m flex-w w-full p-t-45">
				<a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
					Ver Mas
				</a>
			</div> -->
		</div>
	</div>
		

	<?php include 'footer.php';?>
<!--===============================================================================================-->	
	<script>
		function submitForm(){
			document.getElementById('ordenForm').submit();
		}
	</script>

	
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
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/slick/slick.min.js"></script>
	<script src="js/slick-custom.js"></script>
<!--===============================================================================================-->
	<script src="vendor/parallax100/parallax100.js"></script>
	<script>
        $('.parallax100').parallax100();
	</script>
<!--===============================================================================================-->
	<script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
	<script>
		$('.gallery-lb').each(function() { // the containers for all your galleries
			$(this).magnificPopup({
		        delegate: 'a', // the selector for gallery item
		        type: 'image',
		        gallery: {
		        	enabled:true
		        },
		        mainClass: 'mfp-fade'
		    });
		});
	</script>
<!--===============================================================================================-->
	<script src="vendor/isotope/isotope.pkgd.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/sweetalert/sweetalert.min.js"></script>
	<script>
		$('.js-addwish-b2, .js-addwish-detail').on('click', function(e){
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function(){
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function(){
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

		$('.js-addcart-detail').each(function(){
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function(){
				swal(nameProduct, "is added to cart !", "success");
			});
		});
	
	</script>
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