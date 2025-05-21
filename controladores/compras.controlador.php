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
        //después de cargar $compras, si $compras no está vacío, obtenés el valor de fecha de la última compra (la primera si estás ordenando desc).
        $ultima_fecha = $this->modelo->obtenerUltimaFechaCompra();
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
            echo json_encode(['error' => 'No se recibió un ID de transacción válido']);
            return;
        }

        $datosCompra = $this->modelo->ObtenerDetallesCompra($orden);
        if (!$datosCompra) {
            echo json_encode(['error' => 'Compra no encontrada']);
            return;
        }

        // Armamos el HTML que se mostrará en el modal
        $html = '<p><strong>Fecha:</strong> ' . $datosCompra['fecha_formateada'] . '</p>';
        $html .= '<p><strong>Folio (Transacción):</strong> ' . htmlspecialchars($datosCompra['ID_transaccion']) . '</p>';
        $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($datosCompra['cliente']) . '</p>';
        $html .= '<p><strong>Telefono:</strong> ' . htmlspecialchars($datosCompra['telefono']) . '</p>';
        $html .= '<p><strong>Email cliente:</strong> ' . htmlspecialchars($datosCompra['email_cliente']) . '</p>';
        $html .= '<p><strong>Dirección de envío:</strong><br>'
            . 'Calle: ' . htmlspecialchars($datosCompra['calle']) . '<br>'
            . 'Número: ' . htmlspecialchars($datosCompra['numero']) . '<br>'
            . 'Código Postal: ' . htmlspecialchars($datosCompra['codigo_postal']) . '</p>';
        $html .= '<p><strong>Total:</strong> $' . number_format($datosCompra['total'], 2, '.', ',') . '</p>';
        $html .= '<h5>Productos comprados:</h5>';
        $html .= '<ul style="list-style:none; padding-left: 0;">';
        foreach ($datosCompra['detalles'] as $detalle) {
            $html .= '<li>'
                . htmlspecialchars($detalle['nombre']) 
                . ' (x' . intval($detalle['cantidad']) . ') - $' 
                . number_format($detalle['precio'], 2, '.', ',')
                . '</li>';
        }
        $html .= '</ul>';

        // Agregamos un botón "Enviar Correo" que abra el cliente de correo del admin
        // usando un mailto: al email del cliente. Si quieres enviar desde el servidor en lugar
        // de usar mailto:, puedes invocar un nuevo método vía AJAX.
        $emailCliente = urlencode($datosCompra['email_cliente']);
        $asunto       = urlencode("Consulta sobre tu compra #{$datosCompra['ID_transaccion']}");
        $cuerpoMail   = urlencode("Hola " . $datosCompra['cliente'] . ",%0A%0ATu compra con folio {$datosCompra['ID_transaccion']} " .
                                "fue verificada. Cualquier consulta, estamos a tu disposición.%0A%0ASaludos.");
        $mailtoLink   = "mailto:{$emailCliente}?subject={$asunto}&body={$cuerpoMail}";

        $html .= '<hr>';
        $html .= '<button onclick="enviarMailCompra(\'' . $orden . '\')" '
            . 'class="btn btn-sm btn-success">'
            . '<i class="fa fa-envelope"></i> Enviar Correo al Cliente'
            . '</button>';

        echo json_encode(['html' => $html], JSON_UNESCAPED_UNICODE);
    }

    
    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarCompras($termino);
            echo json_encode($resultados,JSON_UNESCAPED_UNICODE);
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

    public function EnviarCorreo() {
        $orden = $_POST['orden'] ?? null;
        if ($orden == null) {
            echo json_encode(['error' => 'No se recibió un folio válido']);
            return;
        }

        // Llamamos a un método del modelo que devuelva email, cliente y quizás detalles mínimos
        $compra = $this->modelo->ObtenerDetallesCompra($orden);
        if (!$compra) {
            echo json_encode(['error' => 'No se encontró la compra']);
            return;
        }

        // Aquí usarías tu clase de mailer (PHPMailer, por ej.) para enviar el correo
        require_once 'front/clases/mailer.php';
        $mailer = new Mailer();

        $destinatario = $compra['email_cliente'];
        $asunto  = "Información sobre tu compra #{$compra['ID_transaccion']}";
        $cuerpo  = "<h3>Hola {$compra['cliente']},</h3>";
        $cuerpo .= "<p>Tu compra con folio <b>{$compra['ID_transaccion']}</b> " .
                "se encuentra en estado <b>APROBADA</b> y los productos serán enviados a la siguiente dirección dentro de 5 a 7 dias habiles.</p>";
        $cuerpo .= "<p>Direccion: {$compra['calle']}, Número: {$compra['numero']}, " .
                "CP: {$compra['codigo_postal']}.</p>";
        $cuerpo .= "<p>El total de la compra(Productos + Envio) fue <b>$" . number_format($compra['total'], 2, '.', ',') . "</b>.</p>";
        $cuerpo .= "<br><p>¡Gracias por elegirnos!</p>";

        $envioOk = $mailer->enviarEmail($destinatario, $asunto, $cuerpo);
        if ($envioOk) {
            echo json_encode(['success' => 'Correo enviado correctamente']);
        } else {
            echo json_encode(['error' => 'Hubo un problema al enviar el correo']);
        }
    }

    public function ObtenerNuevasComprasDesde() {
        $fecha = $_GET['desde'] ?? null;

        if ($fecha) {
            if (strpos($fecha, '/') !== false) {
                $fechaObj = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);
                if (!$fechaObj) {
                    $fechaObj = DateTime::createFromFormat('d/m/Y H:i', $fecha);
                }
                if ($fechaObj) {
                    $fecha = $fechaObj->format('Y-m-d H:i:s');
                } else {
                    // Fecha inválida, setear null o fecha por defecto
                    $fecha = null;
                }
            }
        }

        $resultado = $this->modelo->obtenerNuevasComprasDesde($fecha);

        header('Content-Type: application/json');
        echo json_encode($resultado);
    }



}

?>