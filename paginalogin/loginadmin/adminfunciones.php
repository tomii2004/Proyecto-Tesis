
<?php
    function validaPassword($password,$repassword){
        if(strcmp($password,$repassword) === 0){ // hace una comparacion a nivel binario entre 2 string,por ej para diferencia si una esta en mayus y otra minus te va marcar que son dif
            return true;
        } 
        return false;
    }
    
    function passwordCorrecto($password,$conexion){
        $necesario = '/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/';
        if(preg_match($necesario,$password)){
            return true;
        }
        return false;
    }
    
    function esNulo(array $parametros)
    {
        foreach($parametros as $parametro){
            if(strlen(trim($parametro)) < 1 ){ // esto es para saber si el campo viene vacio porque si es menor que uno es 0
                return true;
            }
        }
        return false;
    }

    function usuarioExiste($usuario, $conexion)
    {
        $sql = $conexion -> prepare("SELECT ID_usuario FROM usuarios WHERE usuario LIKE ? LIMIT 1");
        $sql->execute([$usuario]);
        if($sql ->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    function mostrarMensajes(array $errors){
        if(count($errors) > 0){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><ul>';
            foreach($errors as $error){
                echo '<li>'. $error . '</li>';
            }
            echo '</ul>';
            echo ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        }
    }

    function login($usuario,$password,$conexion){
        $sql = $conexion -> prepare("SELECT ID_admin,usuario,password,nombre FROM admin WHERE usuario like ? AND activo = 1 limit 1");
        $sql ->execute([$usuario]);
        if($row = $sql ->fetch(PDO::FETCH_ASSOC)){
            if(password_verify($password,$row['password'])){
                $_SESSION['user_id'] = $row['ID_admin'];
                $_SESSION['user_name'] = $row['nombre'];
                $_SESSION['user_type'] = 'admin';
                header('Location: ../../?c=inicio');
                exit;
            }   
            
        }
        return 'El usuario y/o contraseña son incorrectos';
    }






?>