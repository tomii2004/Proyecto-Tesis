<!DOCTYPE html>
<html lang="en">
<head>
	<title>Contacto</title>
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
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<?php 

require '../modelos/configproduct-detail.php';


?>
<body class="animsition">
	
	<?php include 'menu.php' ;?>

	


	<!-- Title page -->
	<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
		<h2 class="ltext-105 cl0 txt-center">
			Contacto
		</h2>
	</section>	

	<!--===============================================================================================-->	
	<?php

	// Incluir PHPMailer
	require '../PHPMailer/src/PHPMailer.php';
	require '../PHPMailer/src/SMTP.php';
	require '../PHPMailer/src/Exception.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$nombre = htmlspecialchars(trim($_POST['nombre']));
		$email = htmlspecialchars(trim($_POST['email']));
		$mensaje = htmlspecialchars(trim($_POST['mensaje']));

		if (empty($nombre) || empty($email) || empty($mensaje)) {
			$_SESSION['mensajeError'] = "Todos los campos son obligatorios.";
		} else {
			// Instanciar PHPMailer
			$mail = new PHPMailer(true); 

			try {
				// Configuración del servidor
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com'; 
				$mail->SMTPAuth = true; 
				$mail->Username = 'tomibmx1234@gmail.com'; 
				$mail->Password = 'upjm gsui dhlm xqij'; 
				$mail->SMTPSecure = 'tls'; 
				$mail->Port = 587; 

				// Destinatarios
				$mail->setFrom($email, $nombre);
				$mail->addAddress('tomibmx1234@gmail.com', 'Tomi');

				// Contenido del correo
				$mail->isHTML(true); 
				$mail->Subject = 'Consulta desde el formulario de contacto';
				$mail->Body    = 'Nombre: ' . $nombre . '<br>Email: ' . $email . '<br>Mensaje: ' . $mensaje;

				// Enviar el correo
				$mail->send();
				$_SESSION['mensajeExito'] = "Mensaje enviado con éxito.";
			} catch (Exception $e) {
				$_SESSION['mensajeError'] = "No se pudo enviar el mensaje. Mailer Error: {$mail->ErrorInfo}";
			}
		}

	}
		
	?>
	

	<!--===============================================================================================-->

	<!-- Content page -->
	<section class="bg0 p-t-104 p-b-116">
		<div class="container">
			<div class="flex-w flex-tr">
				<div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
					<form action="" method="post">
						<h4 class="mtext-105 cl13 txt-center p-b-30">
							Enviar Mensaje
						</h4>

						<div class="bor8 m-b-20 how-pos4-parent">
							<input class="stext-111 cl13 plh3 size-116 p-l-62 p-r-30" type="text" name="nombre" placeholder="Nombre" required>
							<img class="how-pos4 pointer-none" src="images/icons/gente-de-la-foto.png" alt="ICON">
						</div>
						

						<div class="bor8 m-b-20 how-pos4-parent">
							<input class="stext-111 cl13 plh3 size-116 p-l-62 p-r-30" type="text" name="email" placeholder="Correo Electronico" required>
							<img class="how-pos4 pointer-none" src="images/icons/icon-email.png" alt="ICON">
						</div>

						<div class="bor8 m-b-30">
							<textarea class="stext-111 cl13 plh3 size-120 p-lr-28 p-tb-25" name="mensaje" placeholder="En que lo puedo ayudar?" required></textarea>
						</div>

						<button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer" type= "submit">
							Enviar
						</button>
					</form>
					<?php
					if (isset($_SESSION['mensajeExito'])) {
						echo '<p style="color:green;font-weight: bold;margin: 10px 0;">' . $_SESSION['mensajeExito'] . '</p>';
						unset($_SESSION['mensajeExito']); //elimina
					}

					if (isset($_SESSION['mensajeError'])) {
						echo '<p style="color:red;font-weight: bold;margin: 10px 0;">' . $_SESSION['mensajeError'] . '</p>';
						unset($_SESSION['mensajeError']); 
					}
					?>
				</div>

				<div class="size-210 bor10 flex-w flex-col-m p-lr-93 p-tb-30 p-lr-15-lg w-full-md">
					<div class="flex-w w-full p-b-42">
						<span class="fs-18 cl5 txt-center size-211">
							<span class="lnr lnr-map-marker"></span>
						</span>

						<div class="size-212 p-t-2">
							<span class="mtext-110 cl13">
								Direccion
							</span>

							<p class="stext-115 cl6 size-213 p-t-18">
								Belgrano 1558, Venado Tuerto 2600
							</p>
						</div>
					</div>

					<div class="flex-w w-full p-b-42">
						<span class="fs-18 cl5 txt-center size-211">
							<span class="lnr lnr-phone-handset"></span>
						</span>

						<div class="size-212 p-t-2">
							<span class="mtext-110 cl13">
								Llamar
							</span>

							<p class="stext-115 cl1 size-213 p-t-18">
								+54 3462 333333
							</p>
						</div>
					</div>

					<div class="flex-w w-full">
						<span class="fs-18 cl5 txt-center size-211">
							<span class="lnr lnr-envelope"></span>
						</span>

						<div class="size-212 p-t-2">
							<span class="mtext-110 cl13">
								Soporte
							</span>

							<p class="stext-115 cl1 size-213 p-t-18">
								Contacto@example.com
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>	
	
	
	<!-- Map -->
	<div class="map">
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3317.824447572956!2d-61.974719988153666!3d-33.73935537316497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95c864a505b7c093%3A0xac42a1a49a538c2a!2sBelgrano%201558%2C%20S2600%20Venado%20Tuerto%2C%20Santa%20Fe!5e0!3m2!1ses-419!2sar!4v1727271031247!5m2!1ses-419!2sar" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
	</div>



	<?php include 'footer.php' ; ?>

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
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAKFWBqlKAGCeS1rMVoaNlwyayu0e0YRes"></script>
	<script src="js/map-custom.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>