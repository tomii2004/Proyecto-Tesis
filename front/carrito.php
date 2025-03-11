<!DOCTYPE html>
<html lang="en">
<head>
	<title>Carrito de Compras</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
<!--===============================================================================================-->
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
	

	$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

	// print_r($_SESSION);
	
	
	$lista_carrito = array();
	if($productos != null){
		foreach($productos as $clave => $cantidad){ // la clave es el id del producto 
			$consulta_productos = $conexion -> prepare("SELECT * ,$cantidad AS cantidad FROM producto WHERE ID_producto = ? AND estado = 1; ");
			$consulta_productos ->execute([$clave]);
			$lista_carrito[] = $consulta_productos -> fetch(PDO::FETCH_ASSOC); // va a traer producto por producto 
		}
	}

	include 'menu.php' ;
	
	?>
	
	

	


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
									<th class="column-3">Precio</th>
									<th class="column-4">Cantidad</th>
									<th class="column-5">Subtotal</th>
									<th class="column-5"></th>
								</tr>
								<?php if($lista_carrito == null){
										echo '<tr><td colspan= "5" class= "txt-center"><b>Lista Vacia</b></td></tr>';
										$total = 0;
									}else{
										$total = 0;
										foreach($lista_carrito as $producto){
											$id = $producto['ID_producto'];
											$nombre = $producto['nombre'];
											$precio = $producto['precio'];
											$cantidad = $producto['cantidad'];
											$imagen = '../'. $producto['ruta_imagen'];
											$subtotal = $cantidad * $precio;
											$total += $subtotal;
										
								?>
								<tr class="table_row">
									
									<td class="column-1">
										<div class="how-itemcart1">
											<img src="<?php echo $imagen ?>" alt="IMG">
										</div>
									</td>
									<td class="column-2 js-name-detail"><?php echo $nombre; ?></td>
									<td class="column-3"><?php echo MONEY; ?><?php echo number_format($precio,2,'.',','); ?></td>
									<td class="column-4">
										<div class="quantity-selector">
											<div onclick="cambiarCantidad(-1, <?php echo $id; ?>)">
												<i class="decrement fs-16 zmdi zmdi-minus"></i>
											</div> 

											<input class="mtext-104 cl3 txt-center num-product" type="number" name="num-product1" max="10" min="1" value="<?php echo $cantidad;?>" id="cantidad_<?php echo $id;?>" onchange="actualizaCantidad(this.value,<?php echo $id;?>)">

											<div onclick="cambiarCantidad(1, <?php echo $id; ?>)">
												<i class="increment fs-16 zmdi zmdi-plus"></i>
											</div>
										</div>
									</td>
									<td class="column-5">
										<div id= "subtotal_<?php echo $id?>" name="subtotal[]">
											<?php echo MONEY; ?><?php echo number_format($subtotal,2,'.',','); ?>
										</div>
									</td>
									<td><a id= "eliminar" class="btn btn-warning btn-sm" data-bs-id="<?php echo $id; ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal">Eliminar</a></td>
								</tr>
								<?php }}?>
							</table>
						</div>

						<!-- <div class="flex-w flex-sb-m bor15 p-t-18 p-b-15 p-lr-40 p-lr-15-sm">
							<div class="flex-w flex-m m-r-20 m-tb-5">
								<input class="stext-104 cl13 plh4 size-117 bor13 p-lr-20 m-r-10 m-tb-5" type="text" name="coupon" placeholder="Cupon Code">
									
								<div class="flex-c-m stext-101 cl13 size-118 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-5">
									Aplicar Cupon
								</div>
							</div>

							 <div class="flex-c-m stext-101 cl13 size-119 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer m-tb-10">
								Modificar Carrito
							</div> 
						</div> -->
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
									<?php echo MONEY;?><?php if($total == 0){echo $total;}else{echo number_format($total,2,'.',',');}?>
								</span>
							</div>
						</div>

						<!-- <div class="flex-w flex-t bor12 p-t-15 p-b-30">
							<div class="size-208 w-full-ssm">
								<span class="stext-110 cl13">
									Envio:
								</span>
							</div>

							<div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
								<p class="stext-111 cl6 p-t-2">
									There are no shipping methods available. Please double check your address, or contact us if you need any help.
								</p>
								
								<div class="p-t-15">
									<span class="stext-112 cl8">
										Calcular Envio
									</span>

									<div class="rs1-select2 rs2-select2 bor8 bg0 m-b-12 m-t-9">
										<select class="js-select2" name="time">
											<option>Seleccionar Pais</option>
											<option>USA</option>
											<option>ARG</option>
										</select>
										<div class="dropDownSelect2"></div>
									</div>

									<div class="bor8 bg0 m-b-12">
										<input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="state" placeholder="Ciudad / Provincia">
									</div>

									<div class="bor8 bg0 m-b-22">
										<input class="stext-111 cl8 plh3 size-111 p-lr-15" type="text" name="postcode" placeholder="Codigo Postal">
									</div>
									
									<div class="flex-w">
										<div class="flex-c-m stext-101 cl13 size-115 bg8 bor13 hov-btn3 p-lr-15 trans-04 pointer">
											Modificar Total
										</div>
									</div>
										
								</div>
							</div>
						</div>

						<div class="flex-w flex-t p-t-27 p-b-33">
							<div class="size-208">
								<span class="mtext-101 cl13">
									Total con envio (falta hacer):
								</span>
							</div>

							<div class="size-209 p-t-1">
								<span class="mtext-110 cl13">
									$79.65
								</span>
							</div>
						</div> -->
						<?php if($lista_carrito != null){ ?>

							<?php if(isset($_SESSION['user_cliente'])) {?>
								<a href="pago.php" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
									Procesar Pago
								</a>
							<?php }else { ?>
								<a href="../paginalogin/login.php?pago" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer">
									Procesar Pago
								</a>

						<?php }}?>
					</div>
				</div>
			</div>
		</div>
	</form>
	

	<?php include 'footer.php' ; ?>
	<div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="eliminaModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="eliminaModalLabel"> Alerta</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					¿Desea eliminar el producto del carrito?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button id="btn-elimina" type="button" class="btn btn-danger" onclick= "eliminar()">Eliminar</button>
				</div>
			</div>
		</div>
	</div>
<!--===============================================================================================-->
    <script>
		function cambiarCantidad(cantidad, id) {
			let inputCantidad = document.getElementById("cantidad_" + id);
			let cantidadActual = parseInt(inputCantidad.value);

			// Aumentar o disminuir según el cantidad
			let nuevaCantidad = cantidadActual + cantidad;

			// Asegurarse de que no sea menor que 1
			if (nuevaCantidad < 1) {
				nuevaCantidad = 1;
			}

			// Actualizar el valor del input
			inputCantidad.value = nuevaCantidad;

			// Llamar a la función para actualizar el subtotal
			actualizaCantidad(nuevaCantidad, id);
		}
	</script>							


<!--===============================================================================================-->
	<script> 
		let eliminaModal = document.getElementById('eliminaModal')
		eliminaModal.addEventListener('show.bs.modal',function(event){//el evento es cuando se muestre
			let button = event.relatedTarget //trae los datos del vinculo que creamos para eliminar
			let id = button.getAttribute('data-bs-id') //obtenemos el id que estamos pasando desde el boton
			let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina') //buscamos el boton al que le vamos a pasar el id,accedemos a la clase y luego al id
			buttonElimina.value = id
		})

		function actualizaCantidad(cantidad,id){
			if (cantidad < 1) {
				alert("La cantidad debe ser al menos 1.");
				return
			}
			let url = 'clases/actualizarcarrito.php';
			let formData = new FormData();
			//enviamos los datos
			formData.append('id',id);
			formData.append('action','agregar');
			formData.append('cantidad',cantidad);
			
			//enviamos configuraciones a esa url mediante metodo post
			fetch(url,{
				method: 'POST',
				body: formData,
				mode: 'cors'
			})
			.then(response => response.json())
			.then(data =>{
				if(data.ok){
					let divsubtotal = document.getElementById("subtotal_" + id)
					divsubtotal.innerHTML = data.sub

					let total = 0.00
					let list = document.getElementsByName('subtotal[]')

					for(let i = 0; i < list.length; i++){
						total += parseFloat(list[i].innerHTML.replace(/[<?php echo MONEY ?>,]/g,'')) //esto para eliminar el signo pesos y las comas
					}

					total = new Intl.NumberFormat('en-US',{
						minimumFractionDigits: 2
					}).format(total)
					document.getElementById('total').innerHTML = '<?php echo MONEY;?>' + total
				}else {
					let inputCantidad = document.getElementById("cantidad_" + id)
					inputCantidad.value = data.cantidadAnterior
					
            		//Animacion de insuficiente stock
					let nameProductError = $(".js-name-detail").html();
            		swal({
						title: nameProductError, 
						text: "¡No hay suficientes existencias!", 
						icon: "error",
						timer: 2000,  // El mensaje se cierra automáticamente después de 3 segundos (3000 milisegundos)
						buttons: false,  // No muestra botones (opcional)
					});

					
        		}
			})
			.catch(error => console.error("Error en la solicitud:", error));
		}


		function eliminar(){
			let botonElimina = document.getElementById("btn-elimina")
			let id = botonElimina.value

			let url = 'clases/actualizarcarrito.php';
			let formData = new FormData();
			//enviamos los datos
			formData.append('id',id);
			formData.append('action','eliminar');
			
			
			//enviamos configuraciones a esa url mediante metodo post
			fetch(url,{
				method: 'POST',
				body: formData,
				mode: 'cors'
			})
			.then(response => response.json())
			.then(data =>{
				if(data.ok){
					location.reload() //para actualizar la ventana
				}else {
            		alert("Error al actualizar el carrito.");
        		}
			})
			.catch(error => console.error("Error en la solicitud:", error));
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
<!--===============================================================================================-->
	<script src="vendor/sweetalert/sweetalert.min.js"></script>

</body>

</html>