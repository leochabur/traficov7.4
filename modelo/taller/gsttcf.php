<?php
  session_start();
    set_time_limit(0);
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  
  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';

  include ('../../modelsORM/src/lavadero/TacografoUnidad.php');
  $accion= $_POST['accion'];
//

  if ($accion == 'setta')
  {
    try{
          $format = 'd/m/Y';
          $fecha = DateTime::createFromFormat($format, $_POST['fecha']);
          if($fecha === false) {
              print json_encode(array('ok'=>false, 'msge'=>'Fecha Invalida!!'));
              exit();
          }

          $tipoTaco = find('Tacografo', $_POST['tipo']);
          $coches = explode(',', $_POST['cch']);

          foreach ($coches as $coche) {
              $uda = find('Unidad', $coche);
              $taco = $entityManager->createQuery("SELECT ta FROM TacografoUnidad ta WHERE ta.unidad = :unidad")->setParameter('unidad', $uda)->getOneOrNullResult();

              if (!$taco){
                  
                  $taco = new TacografoUnidad();
                  $taco->setUnidad($uda);
                  $taco->setFechaCambio($fecha);
                  $taco->setTacografo($tipoTaco);        
                  $entityManager->persist($taco);
              }
              else{
                  $taco->setTacografo($tipoTaco);
                  $taco->setFechaCambio($fecha);
              }
        }



          $vto = $taco->getVencimiento()->format('d/m/Y');
          $entityManager->flush();
          print json_encode(array('ok'=>true, 'fc'=> $_POST['fecha'], 'vto'=> $vto, 'tpo'=>$tipoTaco->getDescripcion()));
    }
    catch (Exception $e){
                          print json_encode(array('ok'=>false, 'msge'=>'Error al procesar la accion'.$e->getMessage()));
    }
  }