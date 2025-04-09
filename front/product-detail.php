<!DOCTYPE html>
<html lang="en">
<head>
	<title>Detalle De Producto</title>
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

	
</head>
<body class="animsition">
	<?php
	
	include '../modelos/basededatos.php';

	$conexion = BasedeDatos::Conectar();

	require '../modelos/configproduct-detail.php';
	include 'menu.php' ;
	?>


	<?php

	$id = isset($_GET['ID_producto']) ? $_GET['ID_producto'] : '';
	$token = isset($_GET['token']) ? $_GET['token'] : '';

	if($id == '' || $token == ''){
		echo "Error al procesar la peticion";
		exit;
	}else{
		
		$token_tmp = hash_hmac('sha1', $id , KEY_TOKEN);

		if($token == $token_tmp){
			$sql_producto = $conexion ->prepare("SELECT count(ID_producto) FROM producto where ID_producto = ? AND estado = 1 ;");
			$sql_producto ->execute([$id]);
			if($sql_producto ->fetchColumn() > 0){
				$sql_producto = $conexion ->prepare("SELECT producto.ID_producto,producto.nombre as 'producto_nombre',producto.precio,producto.stock,producto.ruta_imagen,producto.descripcion,producto.estado,producto.genero,producto.categoria,c_colores.nombre as color_nombre,c_talla.nombre as talla_nombre FROM producto INNER JOIN c_colores ON producto.color = c_colores.ID_colores INNER JOIN c_talla ON producto.talle = c_talla.ID_talla where ID_producto = ? AND estado = 1 LIMIT 1;");
				$sql_producto ->execute([$id]);
				$row = $sql_producto->fetch(PDO::FETCH_ASSOC);
				$nombre = $row['producto_nombre'];
				$precio = $row['precio'];
				$talle = $row['talla_nombre'];
				$color = $row['color_nombre'];
				$genero = $row['genero'];
				$desc = $row['descripcion'];
				$imagen = '../'. $row['ruta_imagen'];
				$imagenesadicionales = $conexion->prepare("SELECT ruta_imagen FROM productos_imagenes WHERE ID_producto = ?");
				$imagenesadicionales->execute([$id]);
				$imagenesadicionales = $imagenesadicionales->fetchAll(PDO::FETCH_ASSOC);

			}else{
				echo "Producto no encontrado";
    			exit;
			}

			$sqlcaracteristica = $conexion ->prepare("SELECT DISTINCT(det_prod_caracter.ID_caracteristica)AS idcat,caracteristicas.caracteristica FROM det_prod_caracter INNER JOIN caracteristicas on det_prod_caracter.ID_caracteristica = caracteristicas.ID_caracteristica WHERE det_prod_caracter.ID_producto = ?");
			$sqlcaracteristica ->execute([$id]);
			$sqlTallas =  $conexion ->prepare("SELECT DISTINCT t.ID_talla,t.nombre FROM productos_variantes AS pv INNER JOIN c_talla AS t ON pv.ID_talla = t.ID_talla WHERE pv.ID_producto = ?");
			$sqlTallas ->execute([$id]);
			$tallas = $sqlTallas->fetchAll(PDO::FETCH_ASSOC);

			$sqlColores =  $conexion ->prepare("SELECT DISTINCT c.ID_colores,c.nombre FROM productos_variantes AS pv INNER JOIN c_colores AS c ON pv.ID_color = c.ID_colores WHERE pv.ID_producto = ?  ");
			$sqlColores ->execute([$id]);
			$colores = $sqlColores->fetchAll(PDO::FETCH_ASSOC);

		}else{
			echo "Error al procesar la peticion";
			exit;
		}

	}

	?>
	<!-- breadcrumb -->
	<div class="container">
		<div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
			<a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
				Inicio
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<a href="product.php" class="stext-109 cl8 hov-cl1 trans-04">
				<?php if($genero == 1){
					echo "Hombre";
				}else if ($genero == 2){
					echo "Mujer";
				}else if($genero == 3){
					echo "Unisex";
				}?>
				<i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
			</a>

			<span class="stext-109 cl4">
				<?php echo $nombre ?>
			</span>
		</div>
	</div>

	<!-- Product Detail -->
	<section class="sec-product-detail bg0 p-t-40 p-b-60">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-lg-7 p-b-30">
					<div class="p-l-25 p-r-30 p-lr-0-lg">
						<div class="wrap-slick3 flex-sb flex-w">
							<div class="wrap-slick3-dots"></div>
							<div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

							<div class="slick3 gallery-lb">
								<!-- Imagen principal -->
								<div class="item-slick3" data-thumb="<?php echo $imagen ?>">
									<div class="wrap-pic-w pos-relative">
										<img src="<?php echo $imagen ?>" alt="IMG-PRODUCT">
										<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo $imagen ?>">
											<i class="fa fa-expand"></i>
										</a>
									</div>
								</div>

								<!-- Imágenes adicionales desde la BD -->
								<?php foreach ($imagenesadicionales as $img) { ?>
									<div class="item-slick3" data-thumb="<?php echo '../' . $img['ruta_imagen']; ?>">
										<div class="wrap-pic-w pos-relative">
											<img src="<?php echo '../' . $img['ruta_imagen']; ?>" alt="IMG-PRODUCT">
											<a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="<?php echo '../' . $img['ruta_imagen']; ?>">
												<i class="fa fa-expand"></i>
											</a>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
					
				<div class="col-md-6 col-lg-5 p-b-30">
					<div class="p-r-50 p-t-5 p-lr-0-lg">
						<h4 class="mtext-105 cl13 js-name-detail p-b-14">
							<?php echo $nombre ?>
						</h4>

						<span class="mtext-106 cl13">
							<?php echo MONEY; ?>  <?php echo number_format($precio,2,'.',',')?>
						</span>

						<p class="stext-102 cl3 p-t-23">
							<?php echo $desc ?>
						</p>

						
						<div class="p-t-33">
							<div class="flex-w flex-r-m p-b-10">
								<div class="size-203 flex-c-m respon6">
									Talle
								</div>

								<div class="size-204 respon6-next">
									<div class="rs1-select2 bor8 bg0">
										<?php
										if($tallas){ ?>
											<select class="form-select form-select-md " name="talles" id="talles" onchange="cargarColores()">
												<option value="" disabled selected>Selecccionar</option>
												<?php foreach($tallas as $talla){?>
													<option value="<?php echo $talla['ID_talla'];?>"><?php echo $talla['nombre'];?></option>
												<?php } ?>
											</select>
										<?php } ?>
									</div>
								</div>
							</div>

							<div class="flex-w flex-r-m p-b-10">
								<div class="size-203 flex-c-m respon6">
									Color
								</div>

								<div class="size-204 respon6-next">
									<div class="rs1-select2 bor8 bg0">
									<?php
										if($colores){ ?>
											<select class="form-select form-select-md " name="colores" id="colores">
												<option value="">Selecccionar</option>
												<?php foreach($colores as $color){?>
													<option value="<?php echo $color['ID_colores'];?>"><?php echo $color['nombre'];?></option>
												<?php } ?>
											</select>
										<?php } ?> 
									</div>
								</div>
							</div>

							<!-- Cantidad Disponible - Mejor Espaciado y Sin Apariencia de Input -->
							<div class="flex-w flex-r-m p-b-10">
								<div class="size-204 respon6-next d-flex align-items-center">
									<span class="me-1 text-muted small">Cantidad Disponible:</span>
									<input class="border-0 bg-transparent fw-bold p-0" 
										id="nuevo_stock" readonly style="width: auto; pointer-events: none;">
								</div>
							</div>
							
							<div class="flex-w flex-r-m p-b-10">
								<div class="size-204 flex-w flex-m respon6-next">
									
									<div class="wrap-num-product flex-w m-r-20 m-tb-10">
										 <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-minus"></i>
										</div>

										<input class="mtext-104 cl3 txt-center num-product" type="number" id="cantidad_<?php echo $id; ?>" name="cantidad" min="1" max="10" value="1" >

										<div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m">
											<i class="fs-16 zmdi zmdi-plus"></i>
										</div> 
									</div>
							
									<button class="flex-c-m stext-101 cl0 size-101 bg1 bor1 hov-btn1 p-lr-15 trans-04" onclick="addProducto(<?php echo $id; ?>,'<?php echo $token_tmp;?>')">
										Agregar al Carrito
									</button>
								</div>
							</div>	
						</div>

						<!--  -->
						<div class="flex-w flex-m p-l-100 p-t-40 respon7">
							<!-- <div class="flex-m bor9 p-r-10 m-r-11">
								<a href="#" class="fs-14 cl3 hov-cl1 trans-04 lh-10 p-lr-5 p-tb-2 js-addwish-detail tooltip100" data-tooltip="Add to Wishlist">
									<i class="zmdi zmdi-favorite"></i>
								</a>
							</div> -->

							<a href="https://www.instagram.com/arena.vt/" target="_blank" rel="noopener noreferrer" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
								<i class="fa fa-instagram"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

	</section>


	
	<?php include 'footer.php' ; ?>
	
<!--===============================================================================================-->
<script>
	function addProducto(id_producto, token) {
		let cantidad = document.getElementById('cantidad_' + id_producto).value;

		cantidad = parseInt(cantidad);
		if (isNaN(cantidad) || cantidad < 1) {
			swal({
				title: "Cantidad inválida",
				text: "Debes ingresar una cantidad válida.",
				icon: "warning",
				timer: 2000,
				buttons: false,
			});
			return;
		}
		
		let id_talla = document.getElementById('talles').value;
		let id_color = document.getElementById('colores').value;

		// Paso 1: Obtener ID de la variante (y su stock)
		let formDataVariante = new FormData();
		formDataVariante.append('id_producto', id_producto);
		formDataVariante.append('id_talla', id_talla);
		formDataVariante.append('id_color', id_color);
		if (!id_talla || !id_color) {
			swal({
				title: "Atención",
				text: "Debes seleccionar un talle y un color.",
				icon: "warning",
				timer: 2000,
				buttons: false,
			});
			return;
		}
		console.log(token)
		fetch('clases/getVariante.php', {
			method: 'POST',
			body: formDataVariante
		})
		 
		.then(response => response.json())
		.then(data => {
			console.log(data);
			if (data.success) {
				let id_variante = data.id_variante;
				let stock = parseInt(data.stock); // asumimos que getVariante.php devuelve stock también
				let cantidadDeseada = parseInt(cantidad);
	
				if (stock >= cantidadDeseada) {
					// Paso 2: Hacer el fetch al carrito si hay stock
					let url = 'clases/numerocarrito.php';
					let formData = new FormData();
					formData.append('id', id_producto);
					formData.append('cantidad', cantidad);
					formData.append('token', token);
					formData.append('id_variante', id_variante);
					formData.append('id_talla', id_talla);
					formData.append('id_color', id_color);

					fetch(url, {
						method: 'POST',
						body: formData,
						mode: 'cors'
					})
					.then(response => response.json())
					.then(data => {
						if (data.ok) {
							let elemento = document.getElementById("num_cart");
							elemento.setAttribute('data-notify', data.numero);

							actualizarCarritoFlotante();

							let nameProductSuccess = $(".js-name-detail").html();
							swal({
								title: nameProductSuccess,
								text: "¡Se agregó correctamente al carrito!",
								icon: "success",
								timer: 2000,
								buttons: false,
							});
						} else {
							// Error por backend (por ejemplo, falla en carrito.php)
							let nameProductError = $(".js-name-detail").html();
							swal({
								title: nameProductError,
								text: "¡No hay suficientes existencias!",
								icon: "error",
								timer: 2000,
								buttons: false,
							});
						}
					});
				} else {
					// Stock insuficiente
					let nameProductError = $(".js-name-detail").html();
					swal({
						title: nameProductError,
						text: "¡Stock insuficiente para la variante seleccionada!",
						icon: "error",
						timer: 2000,
						buttons: false,
					});
				}
			} else {
				// No se encontró variante
				swal({
					title: "Error",
					text: "Variante no encontrada. Verifica el talle y color.",
					icon: "error",
					timer: 2000,
					buttons: false,
				});
			}
		});
	}
</script>

<!--===============================================================================================-->	
	<script>
		const cbxTallas = document.getElementById('talles')
		cargarColores()

		const cbxColores = document.getElementById('colores')
		cbxColores.addEventListener('change',cargarVariante,false)

		function cargarColores(){
			let idTalla = document.getElementById('talles').value;

			// Si no se eligió ningún talle, no seguimos
			if (!idTalla) {
				document.getElementById('colores').innerHTML = '<option value="">Seleccionar luego</option>';
				document.getElementById('colores').disabled = true;
				document.getElementById('nuevo_stock').value = '';
				return;
			}

			const cbxColores = document.getElementById('colores');

			let url = 'clases/productosAjax.php';
			let formData = new FormData();
			formData.append('ID_producto','<?php echo $id?>');
			formData.append('ID_talla',idTalla);
			formData.append('action','buscarColoresPorTalla');

			fetch(url,{
				method: 'POST',
				body: formData,
				mode: 'cors'
			})
			.then(response => response.json())
			.then(data =>{
				if(data.colores){
					cbxColores.disabled = false;
					cbxColores.innerHTML = data.colores;
				}else{
					cbxColores.disabled = true;
					cbxColores.innerHTML = '<option value="">Sin colores disponibles</option>';
				}

				// Solo llamamos cargarVariante si hay un color seleccionado
				if (cbxColores.value) {
					cargarVariante();
				}
			});
		}


		function cargarVariante(){

			document.getElementById('nuevo_stock').value = '';
			
			let idTalla = 0
			if(document.getElementById('talles')){
				idTalla = document.getElementById('talles').value
			}

			let idColor = 0
			if(document.getElementById('colores')){
				idColor = document.getElementById('colores').value
			}
			//ajax con api fetch,obtenemos los datos
			let url = 'clases/productosAjax.php';
			let formData = new FormData();
			//enviamos los datos
			formData.append('ID_producto','<?php echo $id?>');
			if(idTalla !== 0 && idTalla !== ''){
				formData.append('ID_talla',idTalla);
			} 
			if(idColor !== 0 && idColor !== ''){
				formData.append('ID_colores',idColor);
			}
			
			formData.append('action','buscarIdVariante');
			
			//enviamos configuraciones a esa url mediante metodo post
			fetch(url,{
				method: 'POST',
				body: formData,
				mode: 'cors'
			})
			.then(response => response.json())
			.then(data =>{
				if(data.variantes != '' && data.variantes.stock !== undefined){
					// document.getElementById('nuevo_precio').value = data.variantes.precio
					document.getElementById('nuevo_stock').value = data.variantes.stock
				}else{
					document.getElementById('nuevo_precio').value = 'No encontro'
				}

			})
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