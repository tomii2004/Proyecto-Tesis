<?php
use PHPMailer\PHPMailer\{PHPMailer,SMTP,Exception};
//clase y funcion generica
class Mailer{
    function enviarEmail($email,$asunto,$cuerpo){ 
        require_once __DIR__.'/../../modelos/configproduct-detail.php';
        require __DIR__.'/../../PHPMailer/src/PHPMailer.php';
        require __DIR__.'/../../PHPMailer/src/SMTP.php';
        require __DIR__.'/../../PHPMailer/src/Exception.php';
        
        $mail = new PHPMailer(true); //para mostrar exceptiones

        try {
            //Server settings
            
            $mail->SMTPDebug = SMTP::DEBUG_OFF; //SMTP :: DEBUG_OFF;                      //Enable verbose debug output
            
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = MAIL_USER;                     //SMTP username
            $mail->Password   = MAIL_PASS;                             //SMTP password
            $mail->SMTPSecure = 'ssl';           //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('arenavt@gmail.com', 'Arena');
            $mail->addAddress($email);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
            // Contenido del correo
            $mail->isHTML(true); 
            $mail->Subject = $asunto;
            
            $mail->Body    =  mb_convert_encoding($cuerpo, 'ISO-8859-1', 'UTF-8');
          
            $mail -> setLanguage('es','../../PHPMailer/language/phpmailer.lang-es.php');

            if($mail->send()){
                return true;
            }else{
                return false;
            };
        
        } catch (Exception $e) {
            echo "Error al enviar el correo electronico de la compra: {$mail->ErrorInfo}";
            return false;
        }
    }
}

