<?php
include 'funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';


$conexion = BasedeDatos::Conectar();

$proceso = isset($_GET['pago'])? 'pago' : 'login';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $proceso = $_POST['proceso']?? 'login';
  

    if(esNulo([$usuario,$password])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(count($errors) == 0){
        $errors[] = login($usuario,$password,$conexion,$proceso);
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
                        <h3>¿Ya tienes una cuenta?</h3>
                        <p>Inicia sesión para entrar en la página</p>
                        <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                    </div>
                    <div class="caja__trasera-register">
                        <h3>¿Aún no tienes una cuenta?</h3>
                        <p>Regístrate para que puedas iniciar sesión</p>
                        <a href="register.php"><button id="btn__registrarse">Regístrarse </button></a>
                    </div>
                </div>

                <div class="contenedor__login-register">
                    
                    <form action="login.php" method="POST" class="formulario__login">
                        <input type="hidden" name="proceso" value="<?php echo $proceso; ?>">
                        <h2>Iniciar Sesión</h2>
                        <input type="text" placeholder="Usuario" name= "usuario" id="usuario" required>
                        <input type="password" placeholder="Contraseña" name= "password" id="password" required>
                        <a class="custom-a" href="recupera.php" >¿Olvidaste tu Contraseña?</a><br>
                        <button type="submit">Entrar</button><br><br>
                        <div class= "col-12">
                            ¿No tienes cuenta?<a class= "custom-a" href="register.php">Registrate aqui </a>
                        </div>
                        
                        <?php mostrarMensajes($errors); ?>
                    </form>

                    
                </div>
            </div>

        </main>

        <script src="loginscript.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>
</html>


