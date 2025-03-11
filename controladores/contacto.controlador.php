<?php
session_start();

// Incluir PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactoControlador {
    
    public function Inicio() {
        $this-> VerificarAdmin();
        require_once "vistas/encabezado.php";
        require_once "vistas/contacto/formulariocontacto.php";
        require_once "vistas/pie.php";
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

    public function enviarFormulario() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $email = htmlspecialchars(trim($_POST['email']));
            $mensaje = htmlspecialchars(trim($_POST['mensaje']));

            if (empty($nombre) || empty($email) || empty($mensaje)) {
                $_SESSION['mensajeError'] = "Todos los campos son obligatorios.";
            } else {
                // Instanciar PHPMailer
                $mail = new PHPMailer(true); 

                try {
                    // Configuración del servidor
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; 
                    $mail->SMTPAuth = true; 
                    $mail->Username = 'tomicasadei688@gmail.com'; 
                    $mail->Password = 'dpsh kuyk wjhc kdnk'; 
                    $mail->SMTPSecure = 'tls'; 
                    $mail->Port = 587; 

                    // Destinatarios
                    $mail->setFrom($email, $nombre);
                    $mail->addAddress('tomicasadei688@gmail.com', 'Tomi');

                    // Contenido del correo
                    $mail->isHTML(true); 
                    $mail->Subject = 'Consulta desde el formulario de contacto';
                    $mail->Body    = 'Nombre: ' . $nombre . '<br>Email: ' . $email . '<br>Mensaje: ' . $mensaje;

                    // Enviar el correo
                    $mail->send();
                    $_SESSION['mensajeExito'] = "Mensaje enviado con éxito.";
                } catch (Exception $e) {
                    $_SESSION['mensajeError'] = "No se pudo enviar el mensaje. Mailer Error: {$mail->ErrorInfo}";
                }
            }

            header("Location: ?c=contacto");
            exit();
        }
    }
}
?>
