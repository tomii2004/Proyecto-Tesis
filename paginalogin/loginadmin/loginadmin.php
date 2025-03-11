<?php
session_start();
require '../../modelos/basededatos.php';
require 'adminfunciones.php';
$conexion = BasedeDatos::Conectar();

/*$password = password_hash('admin',PASSWORD_DEFAULT);
$sql = "INSERT INTO admin(usuario,password,nombre,email,activo,fecha_alta) values ('admin','$password','Administrador','tomicasadei688@gmail.com','1',NOW())";
$conexion ->query($sql); */
$errors = []; 
if(!empty($_POST)){
  $usuario = trim($_POST['usuario']);
  $password = trim($_POST['password']);
  if(esNulo([$usuario,$password])){
    $errors[] = "Debe llenar todos los campos";
  }

  if(count($errors) == 0){
    $errors[] = login($usuario,$password,$conexion);
  }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>
<section class="vh-100" style="background-color: #673AB7;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col col-xl-10">
        <div class="card" style="border-radius: 1rem;">
          <div class="row g-0">
            <div class="col-md-6 col-lg-5 d-none d-md-block">
              <img src="../../imagenes/fondolocal.png"
                alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem; height: 100%" />
            </div>
            <div class="col-md-6 col-lg-7 d-flex align-items-center">
              <div class="card-body p-4 p-lg-5 text-black">

                <form action="loginadmin.php" method= "POST" autocomplete = "off">

                  <div class="d-flex align-items-center mb-3 pb-1">
                    
                    <span class="h1 fw-bold mb-0">• Arena Vt •</span>
                  </div>

                  <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Ingrese a su cuenta</h5>
                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="text" id="usuario" name="usuario" class="form-control form-control-lg" required autofocus />
                    <label class="form-label" for="usuario">Usuario</label>
                  </div>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                    <label class="form-label" for="password">Contraseña</label>
                  </div>
                  <?php mostrarMensajes($errors); ?>       
                  <div class="pt-1 mb-4">
                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit">Iniciar Sesion</button>
                  </div>

                  <!-- <a class="small text-muted" href="#!">Recuperar Contraseña</a> -->
                  
                  
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>