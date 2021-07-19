<?php
  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include ('../modelsORM/manager.php');
  include_once ('../modelsORM/call.php');
  include_once ('../modelsORM/src/ClaseRealizada.php');
  include ('../modelsORM/src/ClaseAulaVirtual.php');
  
  $accion = $_POST['accion'];

  if ($accion == 'setView')
  {
    try{
    $clase = find('ClaseAulaVirtual', $_POST['cls']);
    $empleado = find('Empleado', $_SESSION['id_chofer']);
    $claseRealizada = new ClaseRealizada();
    $claseRealizada->setClase($clase);
    $claseRealizada->setEmpleado($empleado);
    $claseRealizada->setFecha(new DateTime());
    $entityManager->persist($claseRealizada);
    $entityManager->flush();
    print json_encode(array( 'status' => 1));
    }
    catch (Exception $e){print json_encode(array( 'status' => 0, 'message' => $e->getMessage()));}
  }
?>

