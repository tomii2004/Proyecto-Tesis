<?php



class Producto{
    private $pdo;

    private $ID_producto;
    private $nombre;
    private $precio;
    private $stock;
    private $ruta_imagen;
    private $talle;
    private $color;
    private $estado;
    private $descripcion;
    private $genero;
    private $categoria;

    // conexion a base de datos
    public function __CONSTRUCT(){
        $this ->pdo = BaseDeDatos::Conectar();
    }

    //para poder trabajar con esos atributos

    public function getID_producto(): ? int{ //es nulo o es entero
        return $this ->ID_producto;
    }

    public function setID_producto(int $id){
        $this -> ID_producto = $id;
    }

    public function getNombre(): ? string{
        return $this ->nombre;
    }

    public function setNombre(string $nombre){
        $this -> nombre= $nombre;
    }

    public function getPrecio(): ? float{ 
        return $this ->precio;
    }

    public function setPrecio(float $precio){
        $this -> precio = $precio;
    }
    
    public function getStock(): ? int{
        return $this ->stock;
    }

    public function setStock(int $stock){
        $this -> stock = $stock;
    }
    
    public function getRuta_imagen(): ? string{
        return $this ->ruta_imagen;
    }

    public function setRuta_imagen(?string $ruta_imagen){
        $this -> ruta_imagen = $ruta_imagen;
    }

    public function getTalle(): ? int{
        return $this ->talle;
    }

    public function setTalle(int $talle){
        $this -> talle = $talle;
    }

    public function getColor(): ? string{
        return $this ->color;
    }

    public function setColor(string $color){
        $this -> color = $color;
    }

    public function getDesc(): ? string{
        return $this ->descripcion;
    }

    public function setDesc(string $descripcion){
        $this -> descripcion = $descripcion;
    }
    
    public function getEstado(): ? int{
        return $this ->estado;
    }

    public function setEstado(int $estado){
        $this -> estado = $estado;
    }
    
    public function getGenero(): ? int{
        return $this ->genero;
    }

    public function setGenero(int $genero){
        $this -> genero = $genero;
    }

    public function getCategoria() : ? string{
        return $this ->categoria;
    }
    public function setCategoria(string $categoria){
        $this -> categoria = $categoria;
    }

    private $imagenesAdicionales = [];

    public function getImagenesAdicionales() {
        return $this->imagenesAdicionales;
    }

    public function setImagenesAdicionales($imagenes) {
        $this->imagenesAdicionales = $imagenes;
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

    public function Cantidad(){
        try{
            $consulta = $this -> pdo ->prepare("SELECT SUM(stock) as Cantidad FROM producto;");
            $consulta ->execute();
            return $consulta -> fetch(PDO:: FETCH_OBJ); // para acceder al resultado el cual es uno solo 
        }catch(Exception $e){
            die($e ->getMessage());
        }
    }

    public function Listar() {
        try {
            $consulta = $this->pdo->prepare("SELECT p.*, 
            c.nombre AS categoria_nombre,col.nombre AS colores_nombre,tal.nombre AS tallas_nombre FROM producto p LEFT JOIN categoria c ON p.categoria = c.ID_categoria LEFT JOIN c_colores col ON p.color = col.ID_colores LEFT JOIN c_talla tal ON p.talle = tal.ID_talla;");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_OBJ); // Trae todos los resultados
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarPaginado($inicio, $cantidad) {
        try {
            $consulta = $this->pdo->prepare("SELECT p.*, 
                c.nombre AS categoria_nombre, col.nombre AS colores_nombre,tal.nombre AS tallas_nombre 
                FROM producto p 
                LEFT JOIN categoria c ON p.categoria = c.ID_categoria 
                LEFT JOIN c_colores col ON p.color = col.ID_colores 
                LEFT JOIN c_talla tal ON p.talle = tal.ID_talla
                LIMIT ? OFFSET ?");
            $consulta->bindParam(1, $cantidad, PDO::PARAM_INT);
            $consulta->bindParam(2, $inicio, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function ContarProductos() {
        try {
            $consulta = $this->pdo->prepare("SELECT COUNT(*) as total FROM producto");
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_OBJ)->total;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function Obtener($id){
        try{
            $consulta = $this -> pdo ->prepare("SELECT * FROM producto where ID_producto=?;");
            $consulta ->execute(array($id));
            $r = $consulta -> fetch(PDO:: FETCH_OBJ); // trae un solo registro
            $p = new Producto();
            $p->setID_producto($r->ID_producto);
            $p->setNombre($r->nombre);
            $p->setPrecio($r->precio);
            $p->setStock($r->stock);
            $p->setTalle($r->talle);
            $p->setColor($r->color);
            $p->setEstado($r->estado);
            $p->setRuta_imagen($r->ruta_imagen);
            $p->setGenero($r->genero);
            $p->setCategoria($r->categoria);
            $p->setDesc($r->descripcion);
            

            return $p;
        }catch(Exception $e){
            die($e ->getMessage());
        }
    }

    public function Insertar(Producto $p){
        try{
            $consulta="INSERT INTO producto(nombre,precio,stock,talle,color,estado,genero,categoria,descripcion,ruta_imagen) VALUES(?,?,?,?,?,?,?,?,?,?);";
            $this->pdo->prepare($consulta)
                    ->execute(array(
                        $p->getNombre(),
                        $p->getPrecio(),
                        $p->getStock(),
                        $p->getTalle(),
                        $p->getColor(),
                        $p->getEstado(),
                        $p->getGenero(),
                        $p->getCategoria(),
                        $p->getDesc(),
                        $p->getRuta_imagen()
                        
            ));
             // Obtener el ID del producto recién insertado
            $lastInsertId = $this->pdo->lastInsertId();
            $p->setID_producto($lastInsertId);  // Asignamos el ID generado al objeto $p TOMO EL ID PARA PODER GUARDAR LAS VARIACIONES
            
            $this->GuardarVariaciones($p);

            // Guardar imágenes adicionales
            $this->GuardarImagenesAdicionales($p);

        }catch(Exception $e){
            die($e ->getMessage());
        }

        
    }

    public function Actualizar(Producto $p){
        try{
            $consulta="UPDATE producto SET 
            nombre = ?,
            precio = ?,
            stock = ?,
            talle = ?,
            color = ?,
            estado = ?,
            genero = ?,
            categoria = ?,
            descripcion = ?,
            ruta_imagen = ?
            WHERE ID_producto = ?;";
            $this->pdo->prepare($consulta)
                    ->execute(array(
                        $p->getNombre(),
                        $p->getPrecio(),
                        $p->getStock(),
                        $p->getTalle(),
                        $p->getColor(),
                        $p->getEstado(),
                        $p->getGenero(),
                        $p->getCategoria(),
                        $p->getDesc(),
                        $p->getRuta_imagen(),
                        $p->getID_producto()
            ));
            $this->GuardarVariaciones($p);
            
            //Insertar imágenes adicionales si existen
            if (!empty($_FILES['Otras_imagenes']['name'][0])) {
                $this->GuardarImagenesAdicionales($p);
            }
        }catch(Exception $e){
            die($e ->getMessage());
        }

        
    }

    public function ActualizarEstado($id, $estado) {
        try {
            $consulta = "UPDATE producto SET estado = ? WHERE ID_producto = ?";
            $this->pdo->prepare($consulta)
                ->execute(array($estado, $id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function IncrementarStock($id) {
        try {
            $consulta = "UPDATE producto SET stock = stock + 1 WHERE ID_producto = ?";
            $this->pdo->prepare($consulta)->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function DecrementarStock($id) {
        try {
            $consulta = "UPDATE producto SET stock = GREATEST(stock - 1, 0) WHERE ID_producto = ?"; //se una el greatest para asegurar que no baje de 0
            $this->pdo->prepare($consulta)->execute(array($id));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    

    public function ObtenerTallas() {
        try {
            $consulta = $this->pdo->prepare("SELECT ID_talla, nombre FROM c_talla");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error al obtener las tallas: " . $e->getMessage());
        }
    }

    // Método para obtener colores
    public function ObtenerColores() {
        try {
            $consulta = $this->pdo->prepare("SELECT ID_colores, nombre FROM c_colores");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error al obtener los colores: " . $e->getMessage());
        }
    }

    public function GuardarVariaciones(Producto $p){
        $idVariante = $_POST['id_variante'] ?? [];
        $talla = $_POST['talla'] ?? [];
        $color = $_POST['color'] ?? [];
        $precioVariante = $_POST['precio_variante'] ?? [];
        $stockVariante = $_POST['stock_variante'] ?? [];

        if(count($talla)== count($color) && count($talla)== count($precioVariante) && count($talla)== count($stockVariante)){
            $sql = "INSERT INTO productos_variantes(ID_producto,ID_talla,ID_color,precio,stock)VALUES(?,?,?,?,?)";
            $stm = $this->pdo->prepare($sql);

            $sqlUpdate = "UPDATE productos_variantes SET ID_talla = ? ,ID_color = ?,precio = ?,stock = ? WHERE ID_producvar = ?";
            $stmUpdate = $this->pdo->prepare($sqlUpdate);

            for($i = 0;$i< count($talla);$i++){
                $idTalla = (int)$talla[$i];
                $idColor = (int)$color[$i];
                $precio = is_numeric($precioVariante[$i]) ? $precioVariante[$i] : 0;
                $stock = is_numeric($stockVariante[$i]) ? $stockVariante[$i] : 0;

                if(isset($idVariante[$i])&& !empty($idVariante[$i])){
                    $stmUpdate->execute([$idTalla,$idColor,$precio,$stock,$idVariante[$i]]);
                }else {
                    $stm->execute([$p->getID_producto(),$idTalla,$idColor,$precio,$stock]);
                }

            }
        }
    }

    public function MostrarVariaciones(Producto $p){
        try {
            $consulta = $this->pdo->prepare("SELECT ID_producvar,ID_talla,ID_color,precio,stock FROM productos_variantes WHERE ID_producto = ? ");
            $consulta->execute([$p->getID_producto()]);
            return $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die("Error al obtener las variaciones: " . $e->getMessage());
        }
    }
    public function buscarProductos($termino) {
        $query = $this->pdo->prepare("
            SELECT p.ID_producto, p.nombre, p.precio, p.stock, p.talle, c.nombre AS colores_nombre, cat.nombre AS categoria_nombre, p.estado,  p.genero, p.descripcion, p.ruta_imagen,tal.nombre AS tallas_nombre 
        FROM producto p
        LEFT JOIN c_colores c ON p.color = c.ID_colores
        LEFT JOIN categoria cat ON p.categoria = cat.ID_categoria
        LEFT JOIN c_talla tal ON p.talle = tal.ID_talla
        WHERE p.nombre LIKE :termino
        ");
        $query->execute([':termino' => "%$termino%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GuardarImagenesAdicionales(Producto $p) {
        if (!empty($_FILES['Otras_imagenes']['name'][0])) { 
            $ruta_base = 'imagenes/imagenesropa/';
            
            foreach ($_FILES['Otras_imagenes']['tmp_name'] as $key => $tmp_name) {
                $nombre_archivo = uniqid() . "_" . $_FILES['Otras_imagenes']['name'][$key];
                $ruta_completa = $ruta_base . $nombre_archivo;
    
                if (move_uploaded_file($tmp_name, $ruta_completa)) {
                    $consulta = "INSERT INTO productos_imagenes (ID_producto, ruta_imagen) VALUES (?, ?)";
                    $this->pdo->prepare($consulta)->execute([$p->getID_producto(), $ruta_completa]);
                }
            }
        }
    }
    
}