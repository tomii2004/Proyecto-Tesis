<?php
session_start();

include 'modelos/compras.php';

class ComprasControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Compras();
    }

    public function Inicio(){
        $this->modelo->VerificarAdmin();
        // Parámetros de paginación
        $items_por_pagina = 10; // Cambia este valor según tu necesidad
        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
        $inicio = ($pagina_actual - 1) * $items_por_pagina;

        $orden = isset($_GET['orden']) && in_array($_GET['orden'], ['asc', 'desc']) ? $_GET['orden'] : 'desc';
        // Obtener los compras paginados
        $compras = $this->modelo->InformacionClientes($inicio, $items_por_pagina,$orden);

        // Obtener el total de compras para calcular páginas
        $total_clientes = $this->modelo->ContarClientes();
        $total_paginas = ceil($total_clientes / $items_por_pagina);
        
        require_once "vistas/encabezado.php";
        require_once "vistas/compras/compras.php";
        require_once "vistas/pie.php";
    }
    
    public function PeticionDetalleCompra() {
        $orden = $_POST['orden'] ?? null;
    
        if ($orden == null) {
            echo json_encode(['error' => 'No se recibió un ID válido']);
            return;
        }
    
        $datosCompra = $this->modelo->ObtenerDetallesCompra($orden);
    
        if (!$datosCompra) {
            echo json_encode(['error' => 'Compra no encontrada']);
            return;
        }
    
        $html = '<p><strong>Fecha: </strong>' . $datosCompra['fecha_formateada'] . '</p>';
        $html .= '<p><strong>Orden: </strong>' . $datosCompra['ID_transaccion'] . '</p>';
        $html .= '<p><strong>Total: </strong>' . number_format($datosCompra['total'], 2, '.', ',') . '</p>';
        $html .= '<p><strong>Cliente: </strong>' . $datosCompra['cliente'] . '</p>';
        $html .= '<h5>Detalles:</h5>';
        $html .= '<ul style="list-style:none;">';
        foreach ($datosCompra['detalles'] as $detalle) {
            $html .= '<li>' . $detalle['nombre'] . ' (x' . $detalle['cantidad'] . ') - $' . 
                      number_format($detalle['precio'], 2, '.', ',') . '</li>';
        }
        $html .= '</ul>';
    
        echo json_encode(['html' => $html],JSON_UNESCAPED_UNICODE);
    }
    
    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarCompras($termino);
            echo json_encode($resultados);
        }
    }


    public function GenerarReporte(){
        $this->modelo->VerificarAdmin();

        require_once "vistas/encabezado.php";
        require_once "vistas/compras/reportecompras.php";
        require_once "vistas/pie.php";
    }

    public function DescargarPdf(){
        require __DIR__ . '/../fpdf/plantilla_reporte_compras.php';

            // Obtener los datos desde el modelo
        $datos = $this->modelo->EnviarDatosPdf();
        $resultado = $datos['resultado'];  // Los resultados de la consulta
        $fechaIni = $datos['fecha_ini'];   // La fecha de inicio
        $fechaFin = $datos['fecha_fin'];   // La fecha de fin

        $datos = [
            'fechaIni' => $fechaIni,
            'fechaFin' => $fechaFin
        ];

        $pdf = new PDF('P','mm','A4',$datos);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',10);

        while($row = $resultado -> fetch(PDO::FETCH_ASSOC)){
            $pdf->Cell(30,6,$row['fecha_formateada'],1,0);
            $pdf->Cell(30,6,$row['estado'],1,0);
            $pdf->Cell(60,6,mb_convert_encoding($row['cliente'],'ISO-8859-1','UTF-8'),1,0);
            $pdf->Cell(30,6,$row['total'],1,0);
            $pdf->Cell(30,6,$row['medio_pago'],1,1);
        }
        $pdf->Output('D','Reporte_Compras_' . date('Y-m-d') . '.pdf');

    }






}

?>