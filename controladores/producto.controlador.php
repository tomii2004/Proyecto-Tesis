<?php
session_start();
require_once "modelos/producto.php"; //funciona como un import
require_once "modelos/categorias.php"; // Importar el modelo de categorías


class ProductoControlador{

    private $modelo;

    public function __CONSTRUCT(){
        $this-> modelo = new Producto();
    }

    public function Inicio(){
        $this->modelo->VerificarAdmin();
        
        // Parámetros de paginación
        $items_por_pagina = 10; // Cambia este valor según tu necesidad
        $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
        $inicio = ($pagina_actual - 1) * $items_por_pagina;

        // Obtener los productos paginados
        $productos = $this->modelo->ListarPaginado($inicio, $items_por_pagina);

        // Obtener el total de productos para calcular páginas
        $total_productos = $this->modelo->ContarProductos();
        $total_paginas = ceil($total_productos / $items_por_pagina);
        
        require_once "vistas/encabezado.php";
        require_once "vistas/productos/index.php";
        require_once "vistas/pie.php";
    }

    public function FormCrear(){
        $this->modelo->VerificarAdmin();
        $titulo= "Registrar";
        $p = new Producto();

        
        $categoriaModelo = new Categorias();
        $categorias = $categoriaModelo->Listar();

        $tallas = $this->modelo->ObtenerTallas();
        $colores = $this->modelo->ObtenerColores();

        
        
        if(isset($_GET["id"])){
            $p = $this->modelo->Obtener($_GET["id"]);
            $titulo="Modificar";

            $selected_color = $p->getColor(); 
            $selected_talla = $p->getTalle(); 
            $variantes = $this->modelo->MostrarVariaciones($p)?? [];
        }else{
            // Asegúrate de que el estado inicial del producto sea activo (1) cuando es un nuevo producto
            $p->setEstado(1);  // Setea el estado como activo por defecto
            $variantes = [];
            $selected_talla = null;
            $selected_color = null;
        }

        require_once "vistas/encabezado.php";
        require_once "vistas/productos/form.php";
        require_once "vistas/pie.php";
    }

    public function Guardar(){
        $errores = [];

        // Verificar los campos requeridos
        if (empty($_POST["Nombre"])) {
            $errores["Nombre"] = "El nombre es obligatorio.";
        }
        if (empty($_POST["Precio"]) || $_POST["Precio"] <= 0) {
            $errores["Precio"] = "El precio debe ser mayor a 0.";
        }
        if (empty($_POST["Stock"]) || $_POST["Stock"] < 0) {
            $errores["Stock"] = "El stock debe ser mayor o igual a 0.";
        }
        if (empty($_POST["Talle"])) {
            $errores["Talle"] = "Debes seleccionar un talle.";
        }
        if (empty($_POST["Color"])) {
            $errores["Color"] = "Debes seleccionar un color.";
        }
        if (empty($_POST["Genero"])) {
            $errores["Genero"] = "El género es obligatorio.";
        }
        if (empty($_POST["Categoria"])) {
            $errores["Categoria"] = "Debes seleccionar una categoría.";
        }

        // Si hay errores, redireccionar con mensaje de error
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            header("Location: ?c=producto&a=FormCrear");
            exit;
        }
        
        $p = new Producto();
        $p->setID_producto(intval($_POST["ID_producto"]));
        $p->setNombre($_POST["Nombre"]);
        $p->setPrecio($_POST["Precio"]);
        $p->setStock($_POST["Stock"]);
        $p->setTalle($_POST["Talle"]);
        $p->setColor($_POST["Color"]);
        $p->setGenero($_POST["Genero"]);
        $p->setCategoria($_POST["Categoria"]);
        $p->setDesc($_POST["Descripcion"]);

        if(empty($_POST['ID_producto'])) { //si esta vacio es porque se esta cargando un producto y el estado se setea en 1
            $p->setEstado(1);  // Estado "activo"
        } else {
            $p->setEstado($_POST['Estado'] ?? 1);  // si no esta definida o es null se pone en uno (?? ese operador se llama fusion null)
        }

        $ruta_imagen = null;
        // Manejo de la imagen principal
        if (isset($_FILES['Imagen']) && $_FILES['Imagen']['error'] == 0) {
            $nombre_imagen = time() . '_' . $_FILES['Imagen']['name']; // Generar un nombre único con el tiempo actual
            $ruta_imagen = 'imagenes/imagenesropa/' . $nombre_imagen; // Carpeta de destino

            // Mover la imagen subida a la carpeta de destino
            if (move_uploaded_file($_FILES['Imagen']['tmp_name'], $ruta_imagen)) {
                $p->setRuta_imagen($ruta_imagen); // Guardar la ruta en la base de datos
            }
        } else {
            // Si no se subió una nueva imagen, mantener la ruta actual
            if ($p->getID_producto() > 0){ // Solo si es una actualización de un producto existente
                $productoExistente = $this->modelo->Obtener($p->getID_producto()); //obtiene los datos de ese id
                $p->setRuta_imagen($productoExistente->getRuta_imagen()); //mantiene la imagen
            }

        }

        

        //es un if en linea escrito en una sola linea se le llama
        $p-> getID_producto() > 0 ? //condicion
        $this->modelo->Actualizar($p): //si sale por el si hace esto
        $this->modelo->Insertar($p); // si sale por el no hace esto 

        header("Location: ?c=producto");
 
    }

    public function CambiarEstado() {
        $id = $_GET['id']; // Obtener el ID del producto desde la URL
        $nuevo_estado = $_GET['estado']; // Obtener el nuevo estado desde la URL
    
        $this->modelo->ActualizarEstado($id, $nuevo_estado); // Llamar a un método del modelo para actualizar el estado
    
        header('Location: ?c=producto'); // Redireccionar al listado de productos
    }

    public function ModificarStock() {
        $id = intval($_GET['id']); // convierte el valor a entero para asegurarse que no sea otro tipo de dato
        $accion = $_GET['accion']; // Acción a realizar (incrementar o decrementar)
        $stock = $_GET['stock'];
    
        if ($accion === 'incrementar') {
            $this->modelo->IncrementarStock($id);
        } else{
            $this->modelo->DecrementarStock($id);
            if($stock == 0){
                $this->modelo->ActualizarEstado($id,$nuevo_estado);
            }
        }
        header('Location: ?c=producto'); // Redirigir de vuelta al listado de productos para actualizar
    }

    public function ModificarStockAjax() {
        $id = intval($_POST['id']);
        $accion = $_POST['accion'];
        $stock = intval($_POST['stock']);

        if ($accion === 'incrementar') {
            $this->modelo->IncrementarStock($id);
            $stock++;
        } else {
            $this->modelo->DecrementarStock($id);
            $stock = max(0, $stock - 1);
        }

        // Verificar y actualizar estado
        $nuevo_estado = ($stock > 0) ? 1 : 0;
        $this->modelo->ActualizarEstado($id, $nuevo_estado);

        echo json_encode([
            'success' => true,
            'nuevo_stock' => $stock,
            'nuevo_estado' => $nuevo_estado ? 'Activo' : 'Inactivo',
            'nuevo_estado_valor' => $nuevo_estado,
            'nuevo_boton' => $nuevo_estado ? 'X' : '✓'
        ]);
    }
    public function BuscarAjax() {
        if (isset($_GET['termino'])) {
            $termino = htmlspecialchars($_GET['termino']);
            $resultados = $this->modelo->buscarProductos($termino);
            echo json_encode($resultados);
        }
    }

}