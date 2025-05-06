<?php
session_start();

include 'modelos/categorias.php';

class CategoriasControlador{
    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Categorias();
    }

    public function Inicio(){
        $this->modelo->VerificarAdmin();
        
        // Parámetros de paginación
        $items_por_pagina = 10; // Cambia este valor según tu necesidad
        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
        $inicio = ($pagina_actual - 1) * $items_por_pagina;

        $categorias = $this->modelo->TraerCategorias($inicio, $items_por_pagina);

        // Obtener el total de compras para calcular páginas
        $total_categorias = $this->modelo->ContarCategorias();
        $total_paginas = ceil($total_categorias / $items_por_pagina);

        require_once "vistas/encabezado.php";
        require_once "vistas/categorias/categorias.php";
        require_once "vistas/pie.php";
    }

    public function FormNuevo(){
        $categoria = $this->modelo->VerificarAdmin();
        
        require_once "vistas/encabezado.php";
        require_once "vistas/categorias/nuevo.php";
        require_once "vistas/pie.php";
    }

    public function FormEditar(){
        $categoria = $this->modelo->VerificarAdmin();
        $categoria = $this->modelo->ModificarCategoria();
        require_once "vistas/encabezado.php";
        require_once "vistas/categorias/editar.php";
        require_once "vistas/pie.php";
    }

    public function AñadirCategoria(){
        $categoria = $this->modelo->AgregarCategoria();
        if ($categoria) {
            header("Location: ?c=categorias&alerta=success");
        } else {
            header("Location: ?c=categorias&alerta=error");
        }
        exit();
    }

    public function ActualizarCategoria(){

        $categoria = $this->modelo->ActualizarCategoriaModelo();
        header('Location: ?c=categorias');
    }

    public function EliminarCategoria() {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $resultados = $this->modelo->EliminarCategoriaModelo($id); // Llama al modelo para eliminar la categoría
            
            if (!$resultados) {
                header("Location: ?c=categorias&alerta=uso"); // Mostrar mensaje de error
                exit;
            }

            header('Location: ?c=categorias'); // Redirige al listado
            exit;
        } else {
            echo "Error: No se recibió un ID válido.";
        }
    }
    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarCategorias($termino);
            echo json_encode($resultados);
        }
    }
    public function PeticionBajaCategoria(){
        $id = $_POST['id']?? null;

        if ($this->modelo->CategoriaEnUso($id)) {
            echo json_encode(['error' => 'No se puede desactivar la categoría porque está en uso.']);
            return;
        }

        $datosCategoria= $this->modelo->DarBajaCategoria($id);

        if (!$datosCategoria) {
            echo json_encode(['error' => 'No se pudo desactivar la categoria']);
            return;
        }

        echo json_encode([
            'success' => true,
            'datosCategoria' => "La categoria con ID $id ha sido desactivada correctamente."
        ], JSON_UNESCAPED_UNICODE);

    }
    public function PeticionAltaCategoria(){
        $id = $_POST['id']?? null;

        $datosCategoria= $this->modelo->DarAltaCategoria($id);

        if (!$datosCategoria) {
            echo json_encode(['error' => 'No se pudo dar activar la categoria']);
            return;
        }

        echo json_encode([
            'success' => true,
            'datosCategoria' => "La categoria con ID $id ha sido activada correctamente."
        ], JSON_UNESCAPED_UNICODE);
    }
}


?>