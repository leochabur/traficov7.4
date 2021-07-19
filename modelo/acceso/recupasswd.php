<?php
include("../enviomail/class.phpmailer.php");
include("../enviomail/class.smtp.php");
include("../../controlador/bdadmin.php");

function passRnd(){
    $length = 10;
    $rangeMin = pow(36, $length-1);
    $rangeMax = pow(36, $length)-1;
    $base10Rand = mt_rand($rangeMin, $rangeMax);
    return base_convert($base10Rand, 30, 36);
}

$conn = conexcion();
$sql = "SELECT * FROM usuarios u where (user ='$_POST[usr]') and (activo)";
$result = mysql_query($sql, $conn);
if (mysql_num_rows($result)){
    $user = mysql_fetch_array($result);
    $direccion = $user['mail'];
    mysql_free_result($result);
    $passRnd = $user['password'];//;passRnd();
    //$sql = "UPDATE usuarios SET password = '$passRnd', passwordProvisoria = '$passRnd' WHERE (user ='$_POST[usr]')";

  //  mysql_query($sql, $conn)or die(mysql_error($conn));
  //  if (!mysql_errno($conn)){
         $correo_emisor="soportetrafico@masterbus.net";
         $contrasena="soptra2011";
         $mail = new PHPMailer();
         $mail->IsSMTP(); // Envia el correo via SMTP
         $mail->SMTPAuth = true; // Enciende la autenticacion SMTP
         $mail->Username = $correo_emisor; // Usuario SMTP
         $mail->Password = $contrasena; // Contrasena SMTP
         $mail->From = "soportetrafico@masterbus.net";
         $mail->FromName = "Master Bus - Auto";//$nombre_emisor; //Nombre del que envia el correo
         $mail->AddAddress($direccion,"Usuario");
         $mail->WordWrap = 50; // Word wrap
         $mail->IsHTML(true); // Enviar como HTML
         $mail->Subject = "Recuperacion de Contraseña"; //Asunto
         $mail->Body = "Tu contraseña de acceso es:   $passRnd";
         @$mail->Send();
   // }
}
mysql_close($conn);
?>
