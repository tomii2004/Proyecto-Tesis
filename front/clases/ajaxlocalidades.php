<?php
require '../../modelos/basededatos.php';
$pdo = BasedeDatos::Conectar();
$id = intval($_GET['id_provincia'] ?? 0);
$stmt = $pdo->prepare("
  SELECT id AS id_localidad, localidad, costo_envio 
  FROM localidades 
  WHERE id_provincia = :prov 
  ORDER BY localidad
");
$stmt->execute([':prov' => $id]);
header('Content-Type: application/json');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
