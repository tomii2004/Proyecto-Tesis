<?php

class Compras{
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

    public function InformacionClientes($inicio, $cantidad,$orden = 'desc'){
        $sql = "SELECT ID_transaccion,DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,compras.estado,total,medio_pago,CONCAT(nombres,' ',apellidos) AS cliente FROM compras INNER JOIN clientes_compras ON compras.ID_cliente = clientes_compras.ID_clientes_compras ORDER BY fecha $orden , ID_transaccion ASC   LIMIT $cantidad OFFSET $inicio";
        $consulta = $this->pdo->prepare($sql);
        $consulta->execute();
        $compras = $consulta->fetchAll(PDO::FETCH_ASSOC);
        return $compras;
    }

    public function ContarClientes() {
        try {
            $consulta = $this->pdo->prepare("SELECT COUNT(*) as total FROM compras INNER JOIN clientes_compras ON compras.ID_cliente = clientes_compras.ID_clientes_compras");
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_OBJ)->total;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ObtenerDetallesCompra($orden) {
        $sqlcompra = $this->pdo->prepare(
            "SELECT compras.ID_compra, ID_transaccion, fecha, total, 
                    CONCAT(nombres, ' ', apellidos) AS cliente 
             FROM compras 
             INNER JOIN clientes_compras 
             ON compras.ID_cliente = clientes_compras.ID_clientes_compras 
             WHERE ID_transaccion = ? 
             LIMIT 1"
        );
        $sqlcompra->execute([$orden]);
        $rowcompra = $sqlcompra->fetch(PDO::FETCH_ASSOC);
    
        if (!$rowcompra) {
            return null;
        }
    
        $idcompra = $rowcompra['ID_compra'];
        $fecha = new DateTime($rowcompra['fecha']);
        $rowcompra['fecha_formateada'] = $fecha->format('d/m/Y H:i');
    
        $sqldetalle = $this->pdo->prepare(
            "SELECT ID_venta_producto, nombre, precio, cantidad 
             FROM ventas_producto 
             WHERE ID_compra = ?"
        );
        $sqldetalle->execute([$idcompra]);
        $rowcompra['detalles'] = $sqldetalle->fetchAll(PDO::FETCH_ASSOC);
    
        return $rowcompra;
    }

    public function buscarCompras($termino) {
        $query = $this->pdo->prepare("
            SELECT 
                ID_transaccion,
                DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,
                compras.estado,
                total,
                medio_pago,
                CONCAT(nombres, ' ', apellidos) AS cliente
            FROM compras
            INNER JOIN clientes_compras 
                ON compras.ID_cliente = clientes_compras.ID_clientes_compras
            WHERE 
                CONCAT(nombres, ' ', apellidos) LIKE :termino
                OR DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') LIKE :termino
                OR ID_transaccion LIKE :termino
                OR total LIKE :termino
            ORDER BY fecha
        ");
        $query->execute([':termino' => "%$termino%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function EnviarDatosPdf(){
        $fechaIni = $_POST['fecha_ini'];
        $fechaFin = $_POST['fecha_fin'];

        $sql = "SELECT DATE_FORMAT(compras.fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,compras.estado,compras.total,compras.medio_pago,CONCAT(nombres,' ',apellidos) AS cliente FROM compras INNER JOIN clientes_compras ON compras.ID_cliente = clientes_compras.ID_clientes_compras WHERE DATE(compras.fecha)BETWEEN ? AND ? ORDER BY fecha ASC";
        $resultado = $this->pdo->prepare($sql);
        $resultado->execute([$fechaIni,$fechaFin]);

        // Devolver el resultado y las fechas
        return [
            'resultado' => $resultado,
            'fecha_ini' => $fechaIni,
            'fecha_fin' => $fechaFin
        ];

    }


}
?>