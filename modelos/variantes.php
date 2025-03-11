<?php
class Variantes{
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
    public function ListarTalla() {
        try {
            $query = $this->pdo->prepare("SELECT ID_talla,nombre as Nombre FROM c_talla");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function ListarColor() {
        try {
            $query = $this->pdo->prepare("SELECT ID_colores,nombre as Nombre FROM c_colores");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function AgregarTalla($nombre){
        // Convertir nombre a minúsculas para evitar duplicados con diferente capitalización
        $nombre = strtolower(trim($nombre));
    
        // Verificar si la talla ya existe
        $sql = $this->pdo->prepare("SELECT COUNT(*) FROM c_talla WHERE LOWER(nombre) = ?");
        $sql->execute([$nombre]);
        $existe = $sql->fetchColumn();
    
        if ($existe > 0) {
            return false; // Ya existe, no se agrega
        }
    
        $sql = $this->pdo->prepare("INSERT INTO c_talla(nombre) VALUES(?)");
        $sql->execute([$nombre]);
        return true;
    }
    
    public function AgregarColor($nombre){
        // Convertir nombre a minúsculas para evitar duplicados con diferente capitalización
        $nombre = strtolower(trim($nombre));
    
        // Verificar si el color ya existe
        $sql = $this->pdo->prepare("SELECT COUNT(*) FROM c_colores WHERE LOWER(nombre) = ?");
        $sql->execute([$nombre]);
        $existe = $sql->fetchColumn();
    
        if ($existe > 0) {
            return false; // Ya existe, no se agrega
        }
    
        $sql = $this->pdo->prepare("INSERT INTO c_colores(nombre) VALUES(?)");
        $sql->execute([$nombre]);
        return true;
    }

    public function ModificarTalla($id){
        $sql = $this -> pdo -> prepare("SELECT ID_talla,nombre from c_talla WHERE ID_talla = ? LIMIT 1 ");
        $sql ->execute([$id]);
        $categoria = $sql ->fetch(PDO::FETCH_ASSOC);
        return $categoria;
    }

    public function ModificarColor($id){
        $sql = $this -> pdo -> prepare("SELECT ID_colores,nombre from c_colores WHERE ID_colores = ? LIMIT 1 ");
        $sql ->execute([$id]);
        $categoria = $sql ->fetch(PDO::FETCH_ASSOC);
        return $categoria;
    }

    public function TraerTalla(){
        $sql = "SELECT ID_talla,nombre FROM c_talla";
        $resultado = $this->pdo -> query($sql);
        $categorias = $resultado ->fetchAll(PDO::FETCH_ASSOC);
        // echo "<pre>";
        // print_r($categorias);
        // echo "</pre>";
        return $categorias;
    }
    public function TraerColor(){
        $sql = "SELECT ID_colores,nombre FROM c_colores";
        $resultado = $this->pdo -> query($sql);
        $categorias = $resultado ->fetchAll(PDO::FETCH_ASSOC);
        // echo "<pre>";
        // print_r($categorias);
        // echo "</pre>";
        return $categorias;
    }
    
    public function ActualizarTallaModelo($id,$nombre){
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $sql = $this -> pdo ->prepare("UPDATE c_talla SET nombre = ? WHERE ID_talla = ?");
        $sql -> execute([$nombre,$id]);
    }
    public function ActualizarColorModelo($id,$nombre){
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $sql = $this -> pdo ->prepare("UPDATE c_colores SET nombre = ? WHERE ID_colores = ?");
        $sql -> execute([$nombre,$id]);
    }

    public function EliminarTallaModelo($id) {
        $sql = $this->pdo->prepare("DELETE FROM c_talla WHERE ID_talla = ?");
        $sql->execute([$id]);
    }
    public function EliminarColorModelo($id) {
        $sql = $this->pdo->prepare("DELETE FROM c_colores WHERE ID_colores = ?");
        $sql->execute([$id]);
    }






}

?>