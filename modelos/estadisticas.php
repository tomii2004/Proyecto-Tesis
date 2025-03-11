<?php 
class Estadisticas{
    private $pdo;
    
    public function __CONSTRUCT(){
        $this-> pdo = BaseDeDatos::Conectar();
    }

    public function getPdo() {
        return $this->pdo;
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

    public function EstadisticasVentasSemana(){
        $hoy = date('Y-m-d');
        $lunes = date('Y-m-d',strtotime('monday this week',strtotime($hoy)));
        $domingo = date('Y-m-d',strtotime('sunday this week',strtotime($hoy)));
        $fechaInicial = new DateTime($lunes);
        $fechaFinal = new DateTime($domingo);

        $diasVentas = [];
        for($i = $fechaInicial; $i <= $fechaFinal;$i->modify('+1 day')){
            $diasVentas[] = self::totalDia($this->pdo,$i->format('Y-m-d'));
        }
        $diasVentas = implode(',',$diasVentas);
        return $diasVentas;
    }


    public function EstadisticasProdVendido(){
        // Calcular fechas de la semana actual
        $hoy = date('Y-m-d');
        $lunes = date('Y-m-d', strtotime('monday this week', strtotime($hoy)));
        $domingo = date('Y-m-d', strtotime('sunday this week', strtotime($hoy)));
        $fechaInicial = new DateTime($lunes);
        $fechaFinal = new DateTime($domingo);
    
        // Obtener los productos más vendidos
        $listaProductos = self::ProductosMasVendidos($this->pdo, $fechaInicial, $fechaFinal);
        
        // Consolidar productos con el mismo nombre
        $productosAgrupados = [];
        foreach ($listaProductos as $producto) {
            $nombre = $producto['nombre'];
            if (isset($productosAgrupados[$nombre])) {
                $productosAgrupados[$nombre]['cantidad'] += $producto['cantidad'];
            } else {
                $productosAgrupados[$nombre] = $producto;
            }
        }
    
        // Reindexar el array y preparar los datos para la vista
        $productosAgrupados = array_values($productosAgrupados); // Reindexar
        $nombreProductos = [];
        $cantidadProductos = [];
    
        foreach ($productosAgrupados as $producto) {
            $nombreProductos[] = $producto['nombre'];
            $cantidadProductos[] = $producto['cantidad'];
        }
    
        // Convertir los nombres y cantidades a JSON y formato adecuado
        $nombreProductos = json_encode($nombreProductos, JSON_UNESCAPED_UNICODE);
        $cantidadProductos = implode(',', $cantidadProductos);
    
        return [$nombreProductos, $cantidadProductos];
    }


    public function GuardarResumenSemanal($totalVentas, $productosVendidos) {
        $hoy = date('Y-m-d');
        $lunes = date('Y-m-d', strtotime('monday this week', strtotime($hoy)));
        $domingo = date('Y-m-d', strtotime('sunday this week', strtotime($hoy)));
    
        $sql = "INSERT INTO resumen_semanal (semana_inicio, semana_fin, total_ventas, productos_vendidos)
                VALUES (:lunes, :domingo, :total_ventas, :productos_vendidos)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':lunes', $lunes, PDO::PARAM_STR);
        $stmt->bindParam(':domingo', $domingo, PDO::PARAM_STR);
        $stmt->bindParam(':total_ventas', $totalVentas, PDO::PARAM_STR);
        $stmt->bindParam(':productos_vendidos', $productosVendidos, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function ObtenerHistorialSemanal() {
        $sql = "SELECT DATE_FORMAT(semana_inicio, '%d/%m/%Y') as inicio,
                       DATE_FORMAT(semana_fin, '%d/%m/%Y') as fin,
                       total_ventas,
                       productos_vendidos
                FROM resumen_semanal
                ORDER BY semana_inicio DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarHistorial($termino) {
        $query = $this->pdo->prepare("
            SELECT DATE_FORMAT(semana_inicio, '%d/%m/%Y') as inicio,
                       DATE_FORMAT(semana_fin, '%d/%m/%Y') as fin,
                       total_ventas,
                       productos_vendidos
                FROM resumen_semanal
                WHERE DATE_FORMAT(semana_inicio, '%d/%m/%Y') LIKE :termino OR DATE_FORMAT(semana_fin, '%d/%m/%Y') LIKE :termino
                ORDER BY semana_inicio DESC
        ");
        $query->execute([':termino' => "%$termino%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

   


    public static function ProductosMasVendidos($conexion,$fechaInicial,$fechaFinal){
        // Convertir DateTime a formato Y-m-d
        $fechaInicial = $fechaInicial->format('Y-m-d');
        $fechaFinal = $fechaFinal->format('Y-m-d');

        $sql = "SELECT SUM(vp.cantidad) as cantidad,vp.nombre 
            FROM ventas_producto as vp INNER JOIN compras AS c on vp.ID_compra = c.ID_compra WHERE DATE(c.fecha)BETWEEN :fechainicial AND :fechafinal
            GROUP BY vp.ID_producto,vp.nombre ORDER BY SUM(vp.cantidad)DESC LIMIT 5";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':fechainicial', $fechaInicial, PDO::PARAM_STR);
        $stmt->bindParam(':fechafinal', $fechaFinal, PDO::PARAM_STR);
        $stmt->execute();
        

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
 
    }


    public static function totalDia($conexion,$fecha){
        $sql = "SELECT IFNULL(SUM(total), 0) as total 
            FROM compras 
            WHERE DATE(fecha) = :fecha AND estado = 'COMPLETED'";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];
    }

    public function EliminarHistorial() {
        $sql = "DELETE FROM resumen_semanal";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    ///// ESTA PARTE ES PARA PODER EL ADMIN CAMBIAR SU CONTRASEÑA ////

    public function CambiarPasswordAdminModelo(){
        $user_id = $_GET['id']?? $_POST['id'] ?? ''; //primero busca por el get sino existe busca por el metodo post y si tampoco existe lo pone en vacio
        if($user_id == '' || $user_id != $_SESSION['user_id']){
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
                if(self::ActualizarPasswordAdminModelo($user_id,$pass_hash,$this->pdo)){
                    $errors[] = "Contraseña modificada";
                }else{
                    $errors[] = "Error al modificar la contraseña intente nuevamente";
                }
            }
            
        }

        $sql = $this->pdo->prepare("SELECT ID_admin,usuario FROM admin WHERE ID_admin = ?");
        $sql ->execute([$user_id]);
        $usuario = $sql -> fetch(PDO::FETCH_ASSOC);
        return [$errors,$usuario];
    }


    




    public function ActualizarPasswordAdminModelo($user_id, $password, $conexion) {
        $sql = $conexion->prepare("UPDATE admin SET password = ? WHERE ID_admin = ?");
        return $sql->execute([$password, $user_id]);
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