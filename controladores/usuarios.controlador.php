<?php
session_start();

include 'modelos/usuarios.php';


class UsuariosControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Usuarios();
    }

    public function Inicio(){
        $this->modelo->VerificarAdmin();
        // Parámetros de paginación
        $items_por_pagina = 10; 
        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
        $inicio = ($pagina_actual - 1) * $items_por_pagina;

        // Obtener los usuarios paginados
        $usuarios = $this->modelo->InformacionUsuarios($inicio, $items_por_pagina);

        // Obtener el total de usuarios para calcular páginas
        $total_clientes = $this->modelo->ContarUsuarios();
        $total_paginas = ceil($total_clientes / $items_por_pagina);
        
        require_once "vistas/encabezado.php";
        require_once "vistas/usuarios/usuarios.php";
        require_once "vistas/pie.php";
    }

    public function FormEditarPassword(){
        $this->modelo->VerificarAdmin();
        list($errors, $usuario) = $this->modelo->CambiarPasswordUsuariosModelo();
        
        require_once "vistas/encabezado.php";
        require_once "vistas/usuarios/cambiarusuarios.php";
        $this->modelo->mostrarMensajes($errors);
        require_once "vistas/pie.php";
    }

    public function PeticionBajaUsuario(){
        $id = $_POST['id']?? null;

        $datosUsuario= $this->modelo->DarBajaUsuario($id);

        if (!$datosUsuario) {
            echo json_encode(['error' => 'No se pudo dar de baja al usuario']);
            return;
        }

        echo json_encode([
            'success' => true,
            'datosUsuario' => "El usuario con ID $id ha sido dado de baja correctamente."
        ], JSON_UNESCAPED_UNICODE);

    }
    public function PeticionAltaUsuario(){
        $id = $_POST['id']?? null;

        $datosUsuario= $this->modelo->DarAltaUsuario($id);

        if (!$datosUsuario) {
            echo json_encode(['error' => 'No se pudo dar de alta al usuario']);
            return;
        }

        echo json_encode([
            'success' => true,
            'datosUsuario' => "El usuario con ID $id ha sido dado de alta correctamente."
        ], JSON_UNESCAPED_UNICODE);
    }

    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarUsuario($termino);
            echo json_encode($resultados);
        }
    }





}
?>