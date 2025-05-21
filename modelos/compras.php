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
        $sql = "SELECT ID_transaccion,DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,compras.estado,total,medio_pago,CONCAT(nombres,' ',apellidos) AS cliente,telefono FROM compras INNER JOIN clientes_compras ON compras.ID_cliente = clientes_compras.ID_clientes_compras ORDER BY fecha $orden , ID_transaccion ASC   LIMIT $cantidad OFFSET $inicio";
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
            "SELECT 
                compras.ID_compra,
                compras.ID_transaccion,
                compras.fecha,
                compras.total,
                compras.calle,
                compras.numero,
                compras.codigo_postal,
                compras.email AS email_cliente,
                clientes_compras.telefono,
                CONCAT(clientes_compras.nombres, ' ', clientes_compras.apellidos) AS cliente 
            FROM compras 
            INNER JOIN clientes_compras 
            ON compras.ID_cliente = clientes_compras.ID_clientes_compras 
            WHERE compras.ID_transaccion = ? 
            LIMIT 1"
        );
        $sqlcompra->execute([$orden]);
        $rowcompra = $sqlcompra->fetch(PDO::FETCH_ASSOC);

        if (!$rowcompra) {
            return null;
        }

        // Formatear la fecha
        $fechaObj = new DateTime($rowcompra['fecha']);
        $rowcompra['fecha_formateada'] = $fechaObj->format('d/m/Y H:i');

        // Obtener los detalles de productos
        $idcompra = $rowcompra['ID_compra'];
        $sqldetalle = $this->pdo->prepare(
            "SELECT vp.ID_venta_producto,
                    vp.nombre,
                    vp.precio,
                    vp.cantidad 
            FROM ventas_producto vp
            WHERE vp.ID_compra = ?"
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
                CONCAT(nombres, ' ', apellidos) AS cliente,
                clientes_compras.telefono 
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

    public function obtenerNuevasComprasDesde($desde) {
        $sql = "SELECT 
                    ID_transaccion,
                    DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada,
                    fecha AS fecha_raw,
                    compras.total,
                    CONCAT(clientes_compras.nombres, ' ', clientes_compras.apellidos) AS cliente,
                    clientes_compras.telefono
                FROM compras 
                INNER JOIN clientes_compras ON compras.ID_cliente = clientes_compras.ID_clientes_compras
                WHERE fecha > ?
                ORDER BY fecha ASC";

        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([$desde]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUltimaFechaCompra() {
        $sql = "SELECT MAX(fecha) as ultima_fecha FROM compras";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['ultima_fecha'] ?? date('Y-m-d H:i:s');
    }
}
?>