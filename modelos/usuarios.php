<?php
class Usuarios{
    private $pdo;
    
    public function __CONSTRUCT(){
        $this-> pdo = BaseDeDatos::Conectar();
    }

    public function VerificarAdmin(){
        if(!isset($_SESSION['user_type'])){
            header('Location: paginalogin/loginadmin/loginadmin.php');
            exit;
        }
        if($_SESSION['user_type'] != 'admin'){
            header('Location: front/index.php');
            exit;
        }
    }

    public function InformacionUsuarios($inicio,$cantidad){
        $sql = "SELECT usuarios.ID_usuario,CONCAT (clientes_compras.nombres,' ',clientes_compras.apellidos)AS cliente,usuarios.usuario,usuarios.activacion,
        CASE
        WHEN usuarios.activacion = 1 THEN 'Activo'
        WHEN usuarios.activacion = 0 THEN 'No activado'
        ELSE 'Deshabilitado'
        END AS estatus FROM usuarios INNER JOIN clientes_compras ON usuarios.ID_cliente = clientes_compras.ID_clientes_compras LIMIT $cantidad OFFSET $inicio ";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute();
        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    }

    public function ContarUsuarios() {
        try {
            $consulta = $this->pdo->prepare("SELECT COUNT(*) as total FROM usuarios");
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_OBJ)->total;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CambiarPasswordUsuariosModelo(){
        $user_id = $_GET['user_id']?? $_POST['user_id'] ?? ''; //primero busca por el get sino existe busca por el metodo post y si tampoco existe lo pone en vacio
        if($user_id == '' ){
            header("Location: " . dirname($_SERVER['PHP_SELF']) . "/../front/index.php");
            exit;
        }
        
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $password = trim($_POST['password']);
            $repassword = trim($_POST['repassword']);
        
            if(self::esNulo([$user_id,$password,$repassword])){
                $errors[] = "Debe llenar todos los campos";
            }
        
            if(!self::validaPassword($password,$repassword)){
                $errors[] = "Las contraseñas no coinciden"; 
            }
            if(!self::passwordCorrecto($password,$this->pdo)){
                $errors[] = "La contraseña no cumple con lo requerido";
            }
            
            if(empty($errors)){
                $pass_hash = password_hash($password,PASSWORD_DEFAULT);
                if(self::ActualizarPasswordUsuarioModelo($user_id,$pass_hash,$this->pdo)){
                    $errors[] = "Contraseña modificada";
                }else{
                    $errors[] = "Error al modificar la contraseña intente nuevamente";
                }
            }
            
        }

        $sql = $this->pdo->prepare("SELECT ID_usuario,usuario FROM usuarios WHERE ID_usuario = ?");
        $sql ->execute([$user_id]);
        $usuario = $sql -> fetch(PDO::FETCH_ASSOC);
        return [$errors,$usuario];
    }
    public function ActualizarPasswordUsuarioModelo($user_id, $password, $conexion) {
        $sql = $conexion->prepare("UPDATE usuarios SET password = ?, token_password = '', password_request = 0 WHERE ID_usuario = ?");
        return $sql->execute([$password, $user_id]);
    }

    public function DarBajaUsuario($id){
        $sql = $this->pdo->prepare("UPDATE usuarios SET activacion = 2 WHERE ID_usuario = ?");
        return $sql->execute([$id]);
    }
    public function DarAltaUsuario($id){
        $sql = $this->pdo->prepare("UPDATE usuarios SET activacion = 1 WHERE ID_usuario = ?");
        return $sql->execute([$id]);
    }


    public function buscarUsuario($termino) {
        $query = $this->pdo->prepare("
            SELECT usuarios.ID_usuario,CONCAT (clientes_compras.nombres,' ',clientes_compras.apellidos)AS cliente,usuarios.usuario,usuarios.activacion,
        CASE
        WHEN usuarios.activacion = 1 THEN 'Activo'
        WHEN usuarios.activacion = 0 THEN 'No activado'
        ELSE 'Deshabilitado'
        END AS estatus FROM usuarios INNER JOIN clientes_compras ON usuarios.ID_cliente = clientes_compras.ID_clientes_compras WHERE CONCAT (clientes_compras.nombres,' ',clientes_compras.apellidos) LIKE :termino;
        ");
        $query->execute([':termino' => "%$termino%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function validaPassword($password, $repassword) {
        return strcmp($password, $repassword) === 0; // Comparación binaria
    }

    public static function esNulo(array $parametros) {
        foreach ($parametros as $parametro) {
            if (strlen(trim($parametro)) < 1) { // Campo vacío
                return true;
            }
        }
        return false;
    }
    public static function passwordCorrecto($password,$conexion){
        $necesario = '/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/';
        if(preg_match($necesario,$password)){
            return true;
        }
        return false;
    }

    public static function mostrarMensajes(array $errors) {
        if (count($errors) > 0) {
            echo '<div class="alert alert-warning fade show" role="alert"><ul>';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul>';
            echo '<button type="button" class="btn-close" onclick="this.parentElement.style.display=\'none\';" aria-label="Close">×</button>';
            echo '</div>';
        }
    }

}



?>