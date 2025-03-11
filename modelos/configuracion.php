<?php
require 'front/clases/cifrado.php';

class Configuracion{
    private $pdo;
    
    public function __CONSTRUCT(){
        $this-> pdo = BaseDeDatos::Conectar();
    }

    public function Tomarvalues(){
        try{
            $sql = "SELECT nombre,valor FROM configuracion";
            $resultado = $this-> pdo ->query($sql);
            $datos = $resultado ->fetchAll(PDO::FETCH_ASSOC);

            $config = [];
            foreach($datos as $dato){
                $config[$dato['nombre']] = $dato['valor'];
            }
            

            return $config;
        }catch(Exception $e){
            die($e ->getMessage());
        }
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
    
    public function ActualizarConfiguracion() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $smtp = $_POST['smtp'];
                $puerto = $_POST['puerto'];
                $email = $_POST['correo-electronico'];
                $password = cifrar($_POST['password']);

                error_log("Datos recibidos: SMTP: $smtp, Puerto: $puerto, Email: $email, Password: $password");
    
                $this->pdo->beginTransaction();
    
                $sql = $this->pdo->prepare("UPDATE configuracion SET valor = ? WHERE nombre = ?");
                $sql->execute([$smtp, 'correo_smtp']);
                $sql->execute([$puerto, 'correo_puerto']);
                $sql->execute([$email, 'correo_email']);
                $sql->execute([$password, 'correo_password']);
    
                $this->pdo->commit();
                return true;
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log("Error al actualizar configuración: " . $e->getMessage());
                return false;
            }
        }
        return false;
    }


}

?>