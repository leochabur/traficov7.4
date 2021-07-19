<?php
set_time_limit(0);

require_once './vendor/autoload.php';

// Create the Transport
$transport = (new Swift_SmtpTransport('mail.airepampa.com.ar', 25))
  ->setUsername('avisos@airepampa.com.ar')
  ->setPassword('leo181979')
;

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('envio de un correot'))
  ->setFrom(['avisos@airepampa.com.ar' => 'John Doe'])
  ->setTo(['msenabre93@gmail.com', 'msenabre93@gmail.com' => 'A name'])
  ->setBody('Cuerpo del correo')
  ;

// Send the message
$result = $mailer->send($message);


 ?>