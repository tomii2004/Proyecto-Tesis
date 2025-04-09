<?php


// $path = dirname(__FILE__);


// require_once $path . '/../front/clases/cifrado.php';
// require_once $path . '/basededatos.php';
// $conexion = BaseDeDatos::Conectar();

// $sql = "SELECT nombre,valor FROM configuracion";
// $resultado = $conexion ->query($sql);
// $datosConfig = $resultado ->fetchAll(PDO::FETCH_ASSOC);

// $config = [];
// foreach($datosConfig as $datoConfig){
//     $config[$datoConfig['nombre']] = $datoConfig['valor'];
// }

//Configuracion del sistema
define("SITE_URL","http://localhost/proyecto-final-tesis");
define("KEY_TOKEN", "TC-20-02-04"); //una constante
define("MONEY","$");

//Configuracion para Paypal
define("CLIENTE_ID","ARoAjaFuYO9Alev6wces5IYHIJEcwaBhu_F7EQrTukbzeGy-tYoJVc1f4faFz3KUjXvHKOYRqCIjstMM");
define("CURRENCY","USD");

//Configuracion para MercadoPago
define("TOKEN_MP","APP_USR-5466260019979275-112811-2f015d5c0ca22e9c2b8d89c4648922e5-2121822673");
define("PUBLIC_KEY_MP","APP_USR-27393609-e5a0-498a-81f6-051105d93085");

// define("MAIL_HOST",$config['correo_smto']);
// define("MAIL_USER",$config['correo_email']);
// define("MAIL_PASS",descifrar($config['correo_password']));
// define("MAIL_PORT",$config['correo_puerto']);

// Datos para envio de correo electronico
define("MAIL_HOST","smtp.gmail.com");
define("MAIL_USER","tomibmx1234@gmail.com");
define("MAIL_PASS","upjm gsui dhlm xqij");
define("MAIL_PORT",465);

// define("MAIL_HOST","smtp.gmail.com");
// define("MAIL_USER","tomicasadei688@gmail.com");
// define("MAIL_PASS","cfsi xval woxh ttnp");
// define("MAIL_PORT",465);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$num_cart = 0;
if (isset($_SESSION['carrito']['variantes'])) {
    foreach ($_SESSION['carrito']['variantes'] as $variante) {
        if (is_array($variante) && isset($variante['cantidad'])) {
            $num_cart += (int)$variante['cantidad'];
        } elseif (is_numeric($variante)) {
            $num_cart += (int)$variante;
        }
    }
}


?>