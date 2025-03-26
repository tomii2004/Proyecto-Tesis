<?php
class Categorias{
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
    public function Listar() {
        try {
            $query = $this->pdo->prepare("SELECT ID_categoria,nombre as Nombre FROM categoria WHERE activo = 1");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function AgregarCategoria(){
        // Convertir nombre a minúsculas para evitar duplicados con diferente capitalización
        $nombre = strtolower(trim($_POST['nombre']));
        
        // Verificar si la categoria ya existe
        $sql = $this->pdo->prepare("SELECT COUNT(*) FROM categoria WHERE LOWER(nombre) = ?");
        $sql->execute([$nombre]);
        $existe = $sql->fetchColumn();
        
        if ($existe > 0) {
            return false; // Ya existe, no se agrega
        }
        
        $sql = $this -> pdo ->prepare("INSERT INTO categoria(nombre,activo)VALUES(?,1)");
        $sql -> execute([$nombre]);
        return true;
    }

    public function ModificarCategoria(){
        $id = $_GET['id'];
        $sql = $this -> pdo -> prepare("SELECT ID_categoria,nombre from categoria WHERE ID_categoria = ? LIMIT 1 ");
        $sql ->execute([$id]);
        $categoria = $sql ->fetch(PDO::FETCH_ASSOC);
        return $categoria;
    }

    public function TraerCategorias($inicio,$cantidad){
        $sql = "SELECT ID_categoria,nombre,activo FROM categoria LIMIT $cantidad OFFSET $inicio";
        $resultado = $this->pdo -> query($sql);
        $categorias = $resultado ->fetchAll(PDO::FETCH_ASSOC);
        // echo "<pre>";
        // print_r($categorias);
        // echo "</pre>";
        return $categorias;
    }
    public function ContarCategorias() {
        try {
            $consulta = $this->pdo->prepare("SELECT COUNT(*) as total FROM categoria");
            $consulta->execute();
            return $consulta->fetch(PDO::FETCH_OBJ)->total;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function ActualizarCategoriaModelo(){
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $sql = $this -> pdo ->prepare("UPDATE categoria SET nombre = ? WHERE ID_categoria = ?");
        $sql -> execute([$nombre,$id]);
    }

    public function CategoriaEnUso($id){
        $sql = $this->pdo->prepare("SELECT COUNT(*) FROM producto WHERE categoria = ?");
        $sql->execute([$id]);
        return $sql->fetchColumn() > 0; // Retorna true si está en uso
    }

    public function EliminarCategoriaModelo($id) {
        if($this->CategoriaEnUso($id)){
            return false;
        }
        $sql = $this->pdo->prepare("DELETE FROM categoria WHERE ID_categoria = ?");
        $sql->execute([$id]);
        return true;
    }


    public function buscarCategorias($termino) {
        $query = $this->pdo->prepare("
            SELECT ID_categoria,nombre,activo FROM categoria WHERE nombre LIKE :termino
        ");
        $query->execute([':termino' => "%$termino%"]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function DarBajaCategoria($id){
        if($this->CategoriaEnUso($id)){
            return false;
        }
        $sql = $this->pdo->prepare("UPDATE categoria SET activo = 0 WHERE ID_categoria = ?");
        $sql->execute([$id]);
        return true;
    }
    public function DarAltaCategoria($id){
        $sql = $this->pdo->prepare("UPDATE categoria SET activo = 1 WHERE ID_categoria = ?");
        return $sql->execute([$id]);
    }

}

?>