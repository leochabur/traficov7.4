<?php
include("../enviomail/class.phpmailer.php");
include("../enviomail/class.smtp.php");


function enviarMailFromUser($dirs, $cuerpo, $subject, $usuario, $clave)
{
        $array = explode(',', $dirs);

        $correo_emisor="soportetrafico@masterbus.net";
        $contrasena="soptra2011";
        $mail = new PHPMailer();
        $mail->IsSMTP(); // Envia el correo via SMTP
        $mail->SMTPDEbug = 4;
        $mail->SMTPAuth = true; // Enciende la autenticacion SMTP
        $mail->Username = $usuario; //$correo_emisor; // Usuario SMTP
        $mail->Password = $clave; //$contrasena; // Contrasena SMTP
        $mail->From = $usuario; //"soportetrafico@masterbus.net";
        $mail->FromName = "Sistema Master Bus - Auto";//$nombre_emisor; //Nombre del que envia el correo
        foreach ($array as $valor){
                $mail->AddAddress($valor,"Usuario");
        }
        $mail->WordWrap = 50; // Word wrap
        $mail->IsHTML(true); // Enviar como HTML
        $mail->Subject = $subject; //Asunto
        $mail->Body = $cuerpo;
        return $mail->Send();
}


function enviarMail($dirs, $cuerpo, $subject)
{
        $array = explode(',', $dirs);

        $correo_emisor="soportetrafico@masterbus.net";
        $contrasena="soptra2011";
        $mail = new PHPMailer();
        $mail->IsSMTP(); // Envia el correo via SMTP
        $mail->SMTPAuth = true; // Enciende la autenticacion SMTP
        $mail->Username = $correo_emisor; // Usuario SMTP
        $mail->Password = $contrasena; // Contrasena SMTP
        $mail->From = "soportetrafico@masterbus.net";
        $mail->FromName = "Sistema Master Bus - Auto";//$nombre_emisor; //Nombre del que envia el correo
        foreach ($array as $valor){
                $mail->AddAddress($valor,"Usuario");
        }
        $mail->WordWrap = 50; // Word wrap
        $mail->IsHTML(true); // Enviar como HTML
        $mail->Subject = $subject; //Asunto
        $mail->Body = $cuerpo;
        @$mail->Send();
}


function enviarMailAdjunto($dirs, $cuerpo, $subject, $file){
         $array = explode(',', $dirs);

         $correo_emisor="soportetrafico@masterbus.net";
         $contrasena="soptra2011";
         $mail = new PHPMailer();
         $mail->IsSMTP(); // Envia el correo via SMTP
         $mail->SMTPAuth = true; // Enciende la autenticacion SMTP
         $mail->Username = $correo_emisor; // Usuario SMTP
         $mail->Password = $contrasena; // Contrasena SMTP
         $mail->From = "soportetrafico@masterbus.net";
         $mail->FromName = "Sistema Master Bus - Auto";//$nombre_emisor; //Nombre del que envia el correo
         foreach ($array as $valor){
                 $mail->AddAddress($valor,"Usuario");
         }
         $mail->WordWrap = 50; // Word wrap
         $mail->IsHTML(true); // Enviar como HTML
         $mail->Subject = $subject; //Asunto
         $mail->Body = $cuerpo;


        $mail->AddAttachment( $file, 'file.xls' );
         @$mail->Send();
}





?>
