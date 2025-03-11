<?php 
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';
require '../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\{PHPMailer,SMTP,Exception};

$mail = new PHPMailer(true); //para mostrar exceptiones

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF; //SMTP :: DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = MAIL_USER;                     //SMTP username
    $mail->Password   = MAIL_PASS;                             //SMTP password
    $mail->SMTPSecure = PHPMailer:: ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = MAIL_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('arenavt@gmail.com', 'Arena');
    $mail->addAddress('tomicasadei688@gmail.com', 'Tomi');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
   // Contenido del correo
   $mail->isHTML(true); 
   $mail->Subject = 'Detalle de su compra';
   $cuerpo = '<h4>Gracias por su compra </h4>';
   $cuerpo .='<p>El ID de su compra es <b>'. $id_transaccion. '</b></p>';
   $mail->Body    =  utf8_decode($cuerpo);
   $mail->AltBody = 'Le enviamos los detalles de su compra';

   $mail -> setLanguage('es','../../PHPMailer/language/phpmailer.lang-es.php');

   $mail->send();
   
} catch (Exception $e) {
    echo "Error al enviar el correo electronico de la compra: {$mail->ErrorInfo}";
    
}
?>