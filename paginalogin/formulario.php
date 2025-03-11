

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
                        <button id="btn__registrarse">Regístrarse</button>
                    </div>
                </div>

                <div class="contenedor__login-register">
                    
                    <form action="login.php" method="POST" class="formulario__login">
                        <h2>Iniciar Sesión</h2>
                        <input type="text" placeholder="Usuario" name= "usuario" id="usuario" required>
                        <input type="password" placeholder="Contraseña" name= "password" id="password" required>
                        <a class="custom-a" href="recupera.php" >¿Olvidaste tu Contraseña?</a><br>
                        <button type="submit">Entrar</button>
                        <?php mostrarMensajes($errors); ?>
                    </form>

                    <form action="formulario.php" method="POST" class="formulario__register" autocomplete="off">
                        <h2>Regístrarse</h2>
                        
                        <input type="text" placeholder="Nombres *" name="nombres" id="nombres" value="<?php echo htmlspecialchars($_POST['nombres'] ?? ''); ?>" required >
                        <input type="text" placeholder="Apellidos *" name="apellidos"  id="apellidos" value="<?php echo htmlspecialchars($_POST['apellidos'] ?? ''); ?>" required>
                        <input type="email" placeholder="Correo Electronico *" name="correo-electronico" id="correo-electronico" value="<?php echo htmlspecialchars($_POST['correo-electronico'] ?? ''); ?>" required>
                        <span id="validaEmail" class= "text-danger"></span>
                        <input type="tel" placeholder="Telefono *" name="telefono" id="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>" required>
                        <input type="text" placeholder="Dni *" name="dni" id="dni" value="<?php echo htmlspecialchars($_POST['dni'] ?? ''); ?>" required>
                        <span id="validaDni" class= "text-danger"></span>
                        <input type="text" placeholder="Usuario *" name="usuario" id="usuario" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>" required>
                        <span id="validaUsuario" class= "text-danger"></span>
                        <input type="password" placeholder="Contraseña *" name="password" id="password" value="<?php echo htmlspecialchars($_POST['password'] ?? ''); ?>" required>
                        <i class="custom-i"><b>Requiere:</b>La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.</i>
                        <input type="password" placeholder="Repetir Contraseña *" name="repassword" id="repassword" value="<?php echo htmlspecialchars($_POST['repassword'] ?? ''); ?>" required>

                        <i class="custom-i"><b>Atencion:</b>Todos los campos son obligatorios</i>
                        
                        <button type="submit">Regístrarse</button>

                        <?php mostrarMensajes($errors); ?>

                    </form>
                </div>
            </div>

        </main>

        <script src="loginscript.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>
</html>

