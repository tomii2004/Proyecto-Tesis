<?php

class BasedeDatos {

    private static $conexion = null;

    public static function Conectar() {
        // Cargar variables de entorno desde .env
        self::cargarEnv(__DIR__ . '/../.env'); // Asegúrate de colocar el archivo .env en la raíz del proyecto

        try {
            $servidor = $_ENV['DB_HOST'];
            $usuariobd = $_ENV['DB_USER'];
            $clave = $_ENV['DB_PASSWORD'];
            $nombrebd = $_ENV['DB_NAME'];
            $charset = $_ENV['DB_CHARSET'];

            // Conectar a la base de datos con PDO
            self::$conexion = new PDO("mysql:host=$servidor;dbname=$nombrebd;charset=$charset", $usuariobd, $clave);
            self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return self::$conexion;
        } catch (PDOException $e) {
            return "Fallo: " . $e->getMessage();
        }
    }

    private static function cargarEnv($archivo) {
        if (file_exists($archivo)) {
            $lineas = file($archivo);

            foreach ($lineas as $linea) {
                // Ignorar líneas vacías y comentarios
                if (empty(trim($linea)) || $linea[0] == '#') {
                    continue;
                }

                list($clave, $valor) = explode('=', trim($linea), 2);
                $_ENV[$clave] = $valor;
            }
        } else {
            die("El archivo .env no existe.");
        }
    }
}
