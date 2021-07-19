<?php
  session_start();

  include '../../modelsORM/src/lavadero/AccionUnidad.php';
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  

  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
 // use Symfony\Component\Validator\Validation;
  //include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
//

  if ($accion == 'addacc'){
     $response = array();    
     $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fec']);

     $response[error] = false;


     $emples = $_POST[e];
     $data = explode('-', $_POST[d]);
    // die (print_r($emples));

     $unidad = find('Unidad', $data[0]);
  //  if (isset($entityManager))
      // die('esta seteada');
    //  $data = $entityManager->find('Unidad', $data[0]);
    // die ("busco tipo");
     $tAccion = find('TipoAccionUnidad', $data[1]);

     $accion = new AccionUnidad();
     $accion->setUnidad($unidad);
     $accion->setFecha($fecha);
     $accion->setAccion($tAccion);
     if (!is_array($emples)){
        $response[error] = true;
        $response[mje] = 'No ha seleccionado ningun emleado para la tarea!!!!';
        print json_encode($response);
        exit();
     }
     foreach($emples as $emple){
         $empleado = find('Empleado', $emple);
         $accion->addResponsable($empleado);
     }
     $entityManager->persist($accion);
     $entityManager->flush();
     print json_encode($response);
  }
  elseif ($accion == 'addcmt'){
     $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fec']);
     $response = array();
     $response[error] = false;
     if (!$_POST[c]){
        $response[error] = true;
        $response[mje] = 'El campo comentario se encuentra vacio!!!!';
        print json_encode($response);
        exit();
     }
     $data = explode('-', $_POST[d]);
     $unidad = find('Unidad', $data[0]);
     $tAccion = find('TipoAccionUnidad', $data[1]);
     $accion = new AccionUnidad();
     $accion->setUnidad($unidad);
     $accion->setFecha($fecha);
     $accion->setAccion($tAccion);
     $accion->setObservaciones($_POST[c]);
     $entityManager->persist($accion);
     $entityManager->flush();
     print json_encode($response);
  }
  elseif($accion == 'lstacc'){
    try{
                 //$unidad = find('Unidad', $_POST[uda]);
                 $a = $entityManager->createQuery("SELECT a 
                                                   FROM AccionUnidad a 
                                                   LEFT JOIN a.unidad u
                                                   LEFT JOIN u.tipoUnidad tu                                                   
                                                   WHERE u.id = :unidad and a.fecha = :fecha 
                                                   ORDER BY a.accion");
                 $a->setParameter('unidad', $_POST['uda']);
                 $a->setParameter('fecha', (new \DateTime('now'))->format('Y-m-d'));
               
                // print $a->getSQL();
             //    exit();
                 $acciones = $a->getResult();
                 }catch (Exception $e){die($e->getMessage());}
                 $tabla="<table class='table table-zebra'>
                                <thead>
                                       <tr>
                                           <th colspan='4'>Interno $unidad</th>
                                       </tr>
                                       <tr>
                                       <th>Fecha</th>
                                       <th>Accion</th>
                                       <th>Responsables</th>
                                       <th>Observaciones</th>
                                       </tr>
                                </thead>
                                <tbody>";
                 foreach($acciones as $accion){
                     $resp = "";
                     foreach($accion->getResponsables() as $responsable){
                         $resp.=$responsable.", ";
                     }
                     $tabla.="<tr>
                                  <td>".$accion->getFecha()->format('d-m-Y')."</td>
                                  <td>".$accion->getAccion()."</td>
                                  <td>$resp</td>
                                  <td>".$accion->getObservaciones()."</td>
                              </tr> ";
                 }
                 $tabla.="</tbody></table>";
                 print $tabla;
  }


  
?>

