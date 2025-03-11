<?php
include 'funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';


$conexion = BasedeDatos::Conectar();

$user_id = $_GET['id']?? $_POST['user_id'] ?? ''; //primero busca por el get sino existe busca por el metodo post y si tampoco existe lo pone en vacio
$token = $_GET['token']?? $_POST['token'] ?? ''; 

if($user_id == '' || $token == ''){
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/index.php");
    exit;
}

$errors = [];

if(!verificarTokenRequest($user_id,$token,$conexion)){
    echo "No se pudo verificar la informacion";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if(esNulo([$user_id,$token,$password,$repassword])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(!validaPassword($password,$repassword)){
        $errors[] = "Las contraseñas no coinciden"; 
    }
    if(!passwordCorrecto($password,$conexion)){
        $errors[] = "La contraseña no cumple con lo requerido";
    }
    
    if(count($errors) == 0){
        $pass_hash = password_hash($password,PASSWORD_DEFAULT);
        if(actualizarPassword($user_id,$pass_hash,$conexion)){
            echo "Contraseña modificada Correctamente.<br><a href= 'login.php'>Iniciar sesion </a> ";
            exit;
        }else{
            $errors[] = "Error al modificar la contraseña intente nuevamente";
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
                        <form action="reset_password.php" method="POST" class="row g-3" autocomplete= "off">
                            <input type="hidden" name="user_id" id="user_id" value="<?= $user_id;?>">
                            <input type="hidden" name="token" id="token" value="<?= $token;?>">

                            <h2>Cambiar Contraseña</h2>
                            <label for="email">Ingrese su Nueva Contraseña</label>
                            <input class = "form-control" type="password" placeholder="Nueva Contraseña" name= "password" id="password" required>
                            <i class="custom-i"><b>Requiere:</b>La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.</i>
                            <label for="email">Confirme la Nueva Contraseña</label>
                            <input class = "form-control" type="password" placeholder="Confirmar Contraseña" name= "repassword" id="repassword" required>
                            <button type="submit">Continuar</button>                 
                            <a href="login.php">Iniciar Sesion</a>             
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

