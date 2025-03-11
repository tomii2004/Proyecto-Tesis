<?php
require_once "modelos/estadisticas.php";

$estadisticas = new Estadisticas();

// Verificar si ya se ha guardado el resumen de esta semana
$hoy = date('Y-m-d');
$lunes = date('Y-m-d', strtotime('monday last  week', strtotime($hoy)));
$domingo = date('Y-m-d', strtotime('sunday last  week', strtotime($hoy)));

// Aquí se podría agregar una validación para evitar duplicados
$sql = "SELECT COUNT(*) as total FROM resumen_semanal WHERE semana_inicio = :lunes AND semana_fin = :domingo";
$stmt = $estadisticas->pdo->prepare($sql);
$stmt->bindParam(':lunes', $lunes, PDO::PARAM_STR);
$stmt->bindParam(':domingo', $domingo, PDO::PARAM_STR);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if ($resultado['total'] == 0) {
    // Obtener las estadísticas necesarias
    $totalVentas = $estadisticas->EstadisticasVentasSemana();
    $productosVendidos = json_encode($estadisticas->EstadisticasProdVendido());

    // Guardar el resumen semanal
    $estadisticas->GuardarResumenSemanal($totalVentas, $productosVendidos);
    echo "Resumen semanal guardado con éxito.";
} else {
    echo "El resumen semanal ya ha sido guardado.";
}
