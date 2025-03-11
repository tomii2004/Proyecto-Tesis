
<?php 
include 'funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';


$conexion = BasedeDatos::Conectar();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['correo-electronico']);
    $telefono = trim($_POST['telefono']);
    $dni = trim($_POST['dni']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if(esNulo([$nombres,$apellidos,$email,$telefono,$dni,$usuario,$password,$repassword])){
        $errors[] = "Debe llenar todos los campos";
    }

    if(!esEmail($email)){
        $errors[] = "La direccion de correo no es valida"; 
    }

    if(!validaPassword($password,$repassword)){
        $errors[] = "Las contraseñas no coinciden"; 
    }

    if(usuarioExiste($usuario,$conexion)){
        $errors[] = "El nombre de usuario $usuario ya existe"; 
    }
    if(emailExiste($email,$conexion)){
        $errors[] = "El correo electronico $email ya existe"; 
    }

    if(!dniCorrecto($dni,$conexion)){
        $errors[] = "El dni esta mal ingresado";
    }

    if(dniExiste($dni,$conexion)){
        $errors[] = "El dni ya esta registrado";
    }

    if(!passwordCorrecto($password,$conexion)){
        $errors[] = "La contraseña no cumple con lo requerido";
    }


    if(count($errors) == 0){
        $id = registraCliente([$nombres,$apellidos,$email,$telefono,$dni], $conexion);

        if($id > 0){

            require '../front/clases/mailer.php';
            $mailer = new Mailer();
            $token = generaToken();
           
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $idUsuario = registraUsuario([$usuario,$password_hash,$token,$id],$conexion);
            if($idUsuario > 0){
                
                $url = SITE_URL . '/front/activar_cliente.php?id='.$idUsuario .'&token='. $token;
                $asunto = "Activar cuenta - Arena VT";
                $cuerpo = "Estimado $nombres: <br> Para continuar con el proceso de Registro es indispensable dar click en el siguiente link <a href='$url'>Activar cuenta</a>";
    
                if($mailer ->enviarEmail($email,$asunto,$cuerpo)){
                    echo "Para terminar el proceso de registro siga las instrucciones que le hemos enviado a la direccion de correo electronico $email <a href='login.php'>Volver Al Inicio</a>"; //esto desp seria lindo hacerlo en una vista
                    exit;
                }
            }else{
                $errors[]= "Error al registrar Usuario";
            }
        }else{
            $errors[]= "Error al registrar Cliente";
        }
    }
    
  

}
?>
<script>
    var registroConErrores = <?php echo count($errors) > 0 ? 'true' : 'false'; ?>;
</script>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    
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
                        <a href="login.php"><button id="btn__iniciar-sesion">Iniciar Sesión</button></a>
                    </div>
                    <div class="caja__trasera-register">
                        <h3>¿Aún no tienes una cuenta?</h3>
                        <p>Regístrate para que puedas iniciar sesión</p>
                        <button id="btn__registrarse">Regístrarse</button>
                    </div>
                </div>

                <div class="contenedor__login-register">
                    
                    
                    <form action="register.php" method="POST" class="formulario__register" autocomplete="off">
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

        <script> 
            let txtUsuario = document.getElementById('usuario')
            txtUsuario.addEventListener("blur",function(){
                existeUsuario(txtUsuario.value)
            },false)

            let txtEmail = document.getElementById('correo-electronico')
            txtEmail.addEventListener("blur",function(){
                existeEmail(txtEmail.value)
            },false)

            let txtDni = document.getElementById('dni')
            txtDni.addEventListener("blur",function(){
                existeDni(txtDni.value)
            },false)

            let txtPassword = document.getElementById('password')
            txtPassword.addEventListener("blur",function(){
                existeDni(txtPassword.value)
            },false)

            function existeUsuario(usuario){
                let url = "registerAjax.php" // donde se encuentra la peticion
                let formData = new FormData()
                formData.append("action","existeUsuario") // para saber a que peticion estoy solicitando
                formData.append("usuario",usuario)

                fetch(url, { // esto sirve para hacer peticiones
                    method: 'POST',
                    body: formData
                }).then(response => response.json()) //indicamos como nos tiene que volver la respuesta de la peticion
                .then(data => { //data trae la respuesta de la peticion y la podemos procesar
                    if(data.ok){
                        document.getElementById('usuario').value = ''
                        document.getElementById('validaUsuario').innerHTML = 'Usuario no disponible'
                    }else{
                        document.getElementById('validaUsuario').innerHTML = ''

                    }
                })
            }
            function existeEmail(email){
                let url = "registerAjax.php" // donde se encuentra la peticion
                let formData = new FormData()
                formData.append("action","existeEmail") // para saber a que peticion estoy solicitando
                formData.append("email",email)

                fetch(url, { // esto sirve para hacer peticiones
                    method: 'POST',
                    body: formData
                }).then(response => response.json()) //indicamos como nos tiene que volver la respuesta de la peticion
                .then(data => { //data trae la respuesta de la peticion y la podemos procesar
                    if(data.ok){
                        document.getElementById('correo-electronico').value = ''
                        document.getElementById('validaEmail').innerHTML = 'Email ya registrado'
                    }else{
                        document.getElementById('validaEmail').innerHTML = ''

                    }
                })
            }
            function existeDni(dni){
                let url = "registerAjax.php" // donde se encuentra la peticion
                let formData = new FormData()
                formData.append("action","existeDni") // para saber a que peticion estoy solicitando
                formData.append("dni",dni)

                fetch(url, { // esto sirve para hacer peticiones
                    method: 'POST',
                    body: formData
                }).then(response => response.json()) //indicamos como nos tiene que volver la respuesta de la peticion
                .then(data => { //data trae la respuesta de la peticion y la podemos procesar
                    if(data.ok){
                        document.getElementById('dni').value = ''
                        document.getElementById('validaDni').innerHTML = 'Dni ya registrado'
                    }else{
                        document.getElementById('validaDni').innerHTML = ''

                    }
                })
            }

        </script>



</body>
</html>

