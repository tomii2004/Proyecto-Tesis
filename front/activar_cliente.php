<?php
include '../paginalogin/funcionesregister.php';

require '../modelos/basededatos.php';
require '../modelos/configproduct-detail.php';
$conexion = BasedeDatos::Conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == ''){
    header("Location: index.php");
    exit;
}

echo validaToken($id,$token,$conexion);





?>