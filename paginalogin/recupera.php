<?php
include 'funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';


$conexion = BasedeDatos::Conectar();


$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['correo-electronico']);

    if(esNulo([$email])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(!esEmail($email)){
        $errors[] = "La direccion de correo no es valida"; 
    }
    
    if(count($errors) == 0){
        if(emailExiste($email,$conexion)){
            $sql = $conexion ->prepare("SELECT usuarios.ID_usuario,clientes_compras.nombres FROM usuarios INNER JOIN clientes_compras ON usuarios.ID_cliente = clientes_compras.ID_clientes_compras WHERE clientes_compras.email LIKE ? LIMIT 1");
            $sql ->execute([$email]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['ID_usuario'];
            $nombres = $row['nombres'];

            $token = solicitarPassword($user_id,$conexion);

            if($token !== null){
                require '../front/clases/mailer.php';
                $mailer = new Mailer();

                $url = SITE_URL . '/paginalogin/reset_password.php?id='.$user_id .'&token='. $token;

                $string = "Recuperar Contraseña  - Arena VT";
                $asunto = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
                $cuerpo = "Estimado $nombres: <br> Si has solicitado el cambio de tu Contraseña da click en el siguiente link <a href='$url'>$url</a>";
                $cuerpo .= "<br>Si no hiciste esta solicitud puedes ignorar este correo.";

                if($mailer ->enviarEmail($email,$asunto,$cuerpo)){
                    echo "<p><b>Correo Enviado</b></p>"; //esto desp seria lindo hacerlo en una vista
                    echo "<p>Hemos enviado un correo electronio a la direccion $email para restablecer la contraseña</p>";
                    echo "<a href='login.php'>Volver al inicio de sesion</a>";
                    exit;
                }
    

            }
            
        }else{
            $errors[] =  "No existe una cuenta asociada a esta direccion de correo";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="loginestilos.css">
</head>
<body>

        <main>

            <div class="contenedor__todo">
                <div class="caja__trasera">
                    <div class="caja__trasera-login">
                        <form action="recupera.php" method="POST" class="row g-3" autocomplete= "off">
                            <h2>Recuperar Contraseña</h2>
                            <label for="email">Ingrese su Correo Electronico con el cual se registro</label>
                            <input class = "form-control" type="email" placeholder="Correo Electronico" name= "correo-electronico" id="correo-electronico" required>
                            <button type="submit">Continuar</button>                 
                        </form>
                    </div>
                </div>
                <?php mostrarMensajes($errors); ?>
            </div>

        </main>

        <script src="loginscript.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>
</html>

