<!-- correo_enviado.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Correo Enviado</title>
    <link rel="stylesheet" href="loginestilospruebas4.css"> <!-- Asegúrate que este archivo tenga tu CSS personalizado -->
</head>
<body>
    <main class="contenedor__todo">
        <div class="caja__trasera">
            <h3>¡Revisa tu correo!</h3>
            <p>Hemos enviado un enlace para restablecer tu contraseña a:</p>
            <p style="font-weight:bold;"><?php echo htmlspecialchars($_GET['email'] ?? ''); ?></p>
            <a href="login.php"><button>Volver al inicio de sesión</button></a>
        </div>
        <div class="contenedor__login-register">
            <img class="logo-local" src="../imagenes/logopsd.png" alt="Logo">
        </div>
    </main>
</body>
</html>
