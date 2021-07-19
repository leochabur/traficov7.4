<?php
  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include ('../modelsORM/manager.php');
  include_once ('../modelsORM/call.php');
  include_once ('../modelsORM/src/ClaseRealizada.php');
  include ('../modelsORM/src/ClaseAulaVirtual.php');
  include ('../modelsORM/src/RespuestaPreguntaRealizada.php');

  $accion = $_POST['accion'];

  if ($accion == 'setView')
  {
    try
    {
      $clase = find('ClaseAulaVirtual', $_POST['cls']);
      $empleado = find('Empleado', $_SESSION['id_chofer']);
      $claseRealizada = new ClaseRealizada();
      $claseRealizada->setClase($clase);
      $claseRealizada->setEmpleado($empleado);
      $claseRealizada->setFecha(new DateTime());
      $entityManager->persist($claseRealizada);
      $entityManager->flush();
      print json_encode(array( 'status' => true));
    }
    catch (Exception $e){print json_encode(array( 'status' => false, 'message' => $e->getMessage()));}
  }
  elseif ($accion == 'procev')
  {
    try
    {
      $clase = find('ClaseAulaVirtual', $_POST['cls']);
      if (count($_POST) < (count($clase->getPreguntas())+2)) // no contesto todas las preguntas
      {
        print json_encode(array( 'status' => false, 'message' => "Se deben contestar todas las preguntas!"));
        return;
      }

      $empleado = find('Empleado', $_SESSION['id_chofer']);
      $claseRealizada = new ClaseRealizada();
      $claseRealizada->setClase($clase);
      $claseRealizada->setEmpleado($empleado);
      $claseRealizada->setFecha(new DateTime());
      
      foreach ($_POST as $key => $rta)
      {
          $q = explode("-", $key);
          if ($q[0] == 'q') //esta procesando una pregunta
          {
              $r = explode("-", $rta);
              if ($r[0] == 'r') //esta procesando una respuesta
              {

                  $respuesta = find('RespuestaPregunta', $r[1]); //es la respuesta que eligio el ñato
                  if ($respuesta)
                  {
                      $rtaPregunta = new RespuestaPreguntaRealizada();
                      $rtaPregunta->setFecha(new DateTime());
                      $rtaPregunta->setRespuesta($respuesta);
                      $rtaPregunta->setClaseRealizada($claseRealizada);
                      $claseRealizada->addRespuesta($rtaPregunta);

                  }

              }

          }
      }
      $entityManager->persist($claseRealizada);
      $entityManager->flush();
      print json_encode(array( 'status' => true));
      return;
    }
    catch (Exception $e){print json_encode(array( 'status' => false, 'message' => $e->getMessage()));}


  }
?>

