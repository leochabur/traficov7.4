<?php
  session_start();
  error_reporting(0);
  include '../../modelsORM/src/lavadero/AccionUnidad.php';
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  

  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
  include_once '../../modelsORM/controller.php';
  include_once '../../modelsORM/src/TiempoAccionTipoUnidad.php';
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
  elseif($accion == 'tpos'){
      //$tipoV = tipoV($_POST['tipos'], $_SESSION['structure']);
 
      $tiposL = $entityManager->createQuery("SELECT t FROM TipoAccionUnidad t WHERE t.comenta = :comenta")
                              ->setParameter('comenta', false)
                              ->getResult();

      $tiempos = $entityManager->createQuery("SELECT t 
                                             FROM TiempoAccionTipoUnidad t 
                                             JOIN t.tipo tpo
                                             WHERE tpo.id = :tipo AND tpo.estructura = :str")
                               ->setParameter('tipo', $_POST['tipos'])
                               ->setParameter('str', $_SESSION['structure'])
                               ->getResult();
      $arrayTiempos = array();
      foreach ($tiempos as $t) {
          $arrayTiempos[$t->getTipoAccion()->getId()] = $t->getTiempo();
      }
      $tabla = "  <br>
                  <table class='table'>
                  <thead>
                    <tr>
                        <th>Tipo Accion</th>
                        <th>Tiempo Estimado</th>
                        <th></th>
                    </tr>
                  </thead>
                  <tbody>";
      $i = 1;
      foreach ($tiposL as $t) {
          $tpo = $arrayTiempos[$t->getId()];
          $tabla.="<tr>
                      <td>
                        $t
                      </td>
                      <td>
                        <form class='form' data-id='$i'>
                          <input type='text' name='tiempo' class='hora' value='".($tpo?$tpo->format('H:i'):'00:00')."'>
                          <input type='submit'  class='btn' value='Guardar'>
                          <input type='hidden' name='tipoAccion' value='".$t->getId()."'>
                          <input type='hidden' name='tipoUnidad' value='".$_POST['tipos']."'>
                          <input type='hidden' name='accion' value='setTime'>
                        </form>
                      </td>
                      <td class='$i'>
                      </td>
                   </tr>";
          $i++;
      }
      $tabla.="</tbody></table>
               <script>
                            $('.btn').button();
                            $('.hora').mask('99:99');
                            $('.form').submit(function(event){
                                                                event.preventDefault();
                                                                var form = $(this);
                                                                $.post('/modelo/taller/gestionlavados.php', 
                                                                      $(this).serialize(),
                                                                      function(data){
                                                                                    var response = $.parseJSON(data);
                                                                                    if (!response.status){
                                                                                        alert(response.msge);
                                                                                    }
                                                                                    else{
                                                                                          $('.'+form.data('id')).html('<i class=\"fas fa-check\"></i>');
                                                                                    }
                                                                        });
                              });
               </script>";
      print $tabla;

  }
  elseif($accion == 'setTime'){
    $response = Array();
    try{
          $tipoAccion = find('TipoAccionUnidad', $_POST['tipoAccion']);
          $tipoV = tipoV($_POST['tipoUnidad'], $_SESSION['structure']);

          $tiempo = $entityManager->createQuery("SELECT t 
                                                 FROM TiempoAccionTipoUnidad t 
                                                 JOIN t.tipo tpo
                                                 JOIN  t.tipoAccion ta
                                                 WHERE tpo.id = :tipo AND tpo.estructura = :str AND ta = :accion")
                                  ->setParameter('tipo', $_POST['tipoUnidad'])
                                  ->setParameter('str', $_SESSION['structure'])
                                  ->setParameter('accion', $tipoAccion)
                                  ->getOneOrNullResult();
          if (!$tiempo){
              $tiempo = new TiempoAccionTipoUnidad();
              $tiempo->setTipo($tipoV);
              $tiempo->setTipoAccion($tipoAccion);
              $entityManager->persist($tiempo);
          }
          $tiempo->setTiempo(DateTime::createFromFormat('H:i', $_POST['tiempo']));
          $entityManager->flush();
          $response['status'] = true;
          print json_encode($response);
    }catch (Exception $e){
                          $response['msge'] = $e->getMessage();
                          $response['status'] = false;
                          print json_encode($response);
                        }
  }
  
?>

