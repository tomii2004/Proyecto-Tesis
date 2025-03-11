
<?php
    
    function esNulo(array $parametros)
    {
        foreach($parametros as $parametro){
            if(strlen(trim($parametro)) < 1 ){ // esto es para saber si el campo viene vacio porque si es menor que uno es 0
                return true;
            }
        }
        return false;
    }

    function esEmail($email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        return false;

    }


    function validaPassword($password,$repassword){
        if(strcmp($password,$repassword) === 0){ // hace una comparacion a nivel binario entre 2 string,por ej para diferencia si una esta en mayus y otra minus te va marcar que son dif
            return true;
        } 
        return false;
    }

    function generaToken()
    {
        return md5(uniqid(mt_rand(), false));
    }

    function registraCliente(array $datos, $conexion)
    {
        $sql = $conexion -> prepare("INSERT INTO clientes_compras(nombres,apellidos,email,telefono,dni,estado,fecha_alta)VALUES(?,?,?,?,?,1,now())");
        if($sql->execute($datos)){
            return $conexion -> lastInsertId();
        }
        return 0;
    }

    function registraUsuario(array $datos, $conexion){
        $sql = $conexion -> prepare("INSERT INTO usuarios(usuario,password,token,ID_cliente)values(?,?,?,?)");
        if($sql ->execute($datos)){
            return $conexion -> lastInsertId();
        }
        return 0;
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
    function emailExiste($email, $conexion)
    {
        $sql = $conexion -> prepare("SELECT ID_clientes_compras FROM clientes_compras WHERE email LIKE ? LIMIT 1");
        $sql->execute([$email]);
        if($sql ->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    function dniCorrecto($dni,$conexion){
        $expresion = '/^[\d]{1,3}\.?[\d]{3}\.?[\d]{3}$/';
        if(preg_match($expresion,$dni)){
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

    function dniExiste($dni,$conexion){
        $sql = $conexion -> prepare("SELECT ID_clientes_compras FROM clientes_compras WHERE dni LIKE ? LIMIT 1");
        $sql->execute([$dni]);
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

    function validaToken($id, $token,$conexion)
    {
        $msg = "";
        $sql = $conexion -> prepare("SELECT ID_usuario FROM usuarios WHERE ID_usuario = ? AND token LIKE ? LIMIT 1");
        $sql->execute([$id,$token]);
        if($sql ->fetchColumn() > 0){
            if(activarUsuario($id,$conexion)){
                $msg = "Cuenta Activada";
            }else{
                $msg = "Error al activar cuenta";
            }
        }else{
            $msg = "No existe el registro del cliente";
        }
        return $msg;
    }

    function activarUsuario($id,$conexion){
        $sql = $conexion -> prepare("UPDATE usuarios SET activacion = 1,token = '' WHERE ID_usuario = ? ");
        return $sql -> execute([$id]);

    }

    function login($usuario,$password,$conexion,$proceso){
        $sql = $conexion -> prepare("SELECT ID_usuario,usuario,password,ID_cliente FROM usuarios WHERE usuario like ? limit 1");
        $sql ->execute([$usuario]);
        if($row = $sql ->fetch(PDO::FETCH_ASSOC)){
            if(esActivo($usuario,$conexion)){
                if(password_verify($password,$row['password'])){
                    $_SESSION['user_id'] = $row['ID_usuario'];
                    $_SESSION['user_name'] = $row['usuario'];
                    $_SESSION['user_cliente'] = $row['ID_cliente'];
                    if($row['ID_cliente'] == 1 ){
                        $_SESSION['user_type'] = 'admin';
                    }else{
                        $_SESSION['user_type'] = 'user';
                    }
                    
                    if($proceso == 'pago'){
                        header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/carrito.php");
                    }else{
                        header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/index.php");
                    }
                    exit;
                }   
            }else{
                return 'El usuario no ha sido activado.';
            }
        }
        return 'El usuario y/o contraseña son incorrectos';
    }

    function esActivo($usuario,$conexion){
        $sql = $conexion -> prepare("SELECT activacion FROM usuarios WHERE usuario like ? limit 1");
        $sql ->execute([$usuario]);
        $row = $sql ->fetch(PDO::FETCH_ASSOC);
        if($row['activacion'] == 1){
            return true;
        }
        return false;
    }

    function solicitarPassword($user_id,$conexion){
        $token = generaToken();
        $sql = $conexion -> prepare("UPDATE usuarios SET token_password = ?,password_request = 1 WHERE ID_usuario = ?");
        if($sql->execute([$token,$user_id])){
            return $token;
        }
        return null;
    }

    function verificarTokenRequest($user_id,$token,$conexion)
    {
        $sql = $conexion ->prepare("SELECT ID_usuario FROM usuarios WHERE ID_usuario = ? AND token_password LIKE ? AND password_request = 1 LIMIT 1");
        $sql ->execute([$user_id,$token]);
        if($sql->fetchColumn() > 0 ){
            return true;
        }
        return false;

    }

    function actualizarPassword($user_id,$password,$conexion){
        $sql = $conexion -> prepare("UPDATE usuarios SET password = ?,token_password= '',password_request = 0 WHERE ID_usuario = ?");
        if($sql ->execute([$password,$user_id])){
            return true;
        }
        return false;
    }

    function validaCompra($id_transaccion,$id_cliente,$conexion){
        $sql = $conexion ->prepare("SELECT ID_transaccion FROM compras WHERE ID_cliente  = ?;");
        $sql ->execute([$id_cliente]);
        
    }





?>