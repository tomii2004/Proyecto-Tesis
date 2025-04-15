<?php
include 'funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';

$conexion = BasedeDatos::Conectar();

$user_id = $_GET['id'] ?? $_POST['user_id'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($user_id == '' || $token == '') {
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/index.php");
    exit;
}

$errors = [];

if (!verificarTokenRequest($user_id, $token, $conexion)) {
    echo "No se pudo verificar la información";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$user_id, $token, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (!passwordCorrecto($password, $conexion)) {
        $errors[] = "La contraseña no cumple con lo requerido";
    }

    if (count($errors) === 0) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        if (actualizarPassword($user_id, $pass_hash, $conexion)) {
            header("Location: password_modificada.php");
            exit;
        } else {
            $errors[] = "Error al modificar la contraseña. Intente nuevamente";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="loginestilospruebas4.css">
    
</head>
<body>

<main>
    <div class="contenedor__todo">
        <div class="caja__trasera">
            <div class="caja__trasera-login">
                <form action="reset_password.php" method="POST" class="row g-3" autocomplete="off">
                    <input type="hidden" name="user_id" id="user_id" value="<?= htmlspecialchars($user_id); ?>">
                    <input type="hidden" name="token" id="token" value="<?= htmlspecialchars($token); ?>">

                    <h2>Cambiar Contraseña</h2>

                    <label for="password">Ingrese su Nueva Contraseña</label>
                    <input class="form-control" type="password" placeholder="Nueva Contraseña" name="password" id="password" required>
                    <i class="custom-i" style="padding-top:15px;"><b>Requiere:</b> La contraseña debe tener entre 8 y 16 caracteres, al menos un dígito, una minúscula y una mayúscula.</i>

                    <label for="repassword">Confirme la Nueva Contraseña</label>
                    <input class="form-control" type="password" placeholder="Confirmar Contraseña" name="repassword" id="repassword" required>

                    <button type="submit">Continuar</button>
                    
                </form>
            </div>
        </div>
        <?php mostrarMensajesLogin($errors); ?>
        <div class="contenedor__login-register">
            <img class="logo-local" src="../imagenes/logopsd.png" alt="Logo">
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>
