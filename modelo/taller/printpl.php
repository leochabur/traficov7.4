<?php
  session_start();
  set_time_limit(0);
    error_reporting(E_ALL & ~E_NOTICE); 
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
  
  
  if (isset($_POST['accion']))  
  {
        $accion = $_POST['accion'];
        if($accion == 'viewload'){
						

            
                      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
					  $tipoFluido = $entityManager->createQuery("SELECT t FROM TipoFluido t WHERE t.id = :id")->setParameter('id', $_POST['producto'])->getOneOrNullResult();
                      $q = $entityManager->createQuery("SELECT cc
                                                        FROM CargaCombustible cc
                                                        LEFT JOIN cc.unidad u
                                                        WHERE cc.fecha = :fecha AND cc.tipoFluido = :tipo
                                                        ORDER BY u.interno");    
                      $result = $q->setParameter('fecha', $fecha->format('Y-m-d'))->setParameter('tipo', $tipoFluido)->getResult();



                       $tabla="<table class='table table-zebra' id='anomalias'>
                                      <thead>
                                             <tr>
                                                 <th>Fecha</th>
                                                 <th>Interno</th>
                                                 <th>Accion</th>
                                                 <th>Producto</th>                                                 
                                                 <th>Odometro</th>
                                                 <th>Litros</th>
                                                 <th>Hora Carga</th>
                                                 <th>Usuario</th>
                                                 <th></th>
                                             </tr>
                                      </thead>
                                      <tbody>";
                       $i=0;
                       $script = "";
                       foreach ($result as $carga) {
                              try{
                                   $tabla.="<tr>
                                                <td>".$carga->getFecha()->format('d/m/Y')."</td>
                                                <td align='right'>".$carga->getUnidad()."</td>
                                                <td>".$carga->getTipoFluido()->getTipo()."</td>                                                  
                                                <td>".$carga->getAccion()."</td>
                                                <td><div id='odometro-".$carga->getId()."' class='update'>".$carga->getOdometro()."</div></td>
                                                <td><div id='litros-".$carga->getId()."' class='update'>".$carga->getLitros()."</div></td>
                                                <td>".$carga->getFechaAlta()->format('H:i')."</td>
                                                <td>".$carga->getUsuario()->getUsername()."</td>";
                                   if (in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132', '139'))){
                                                $tabla.="<td align='center'><img class='delcga' id='".$carga->getId()."' src='../../eliminar.png' border='0' width='25' height='25'></td>";
                                                $script = "$('.update').editable('/modelo/taller/printpl.php', {submit : 'Cambiar'});";
                                   }
                                   else
                                                $tabla.='<td></td>';
                                   $tabla.="</tr>";
                                  }
                                  catch (Exception $e){
                                                      die($e->getMessage());
                                                      }
                        }
                                           
                       $tabla.="</tbody>
                                </thead>
                                 <script>

                                                      $('.delcga').click(function(){
                                                                                    if (confirm('Seguro eliminar el movimiento?')){
                                                                                          var id = $(this).attr('id');
                                                                                          $.post('/modelo/taller/printpl.php', 
                                                                                                 {accion:'baja', carga:id}, 
                                                                                                 function(data){
                                                                                                              var result = $.parseJSON(data);
                                                                                                              if (result.status){
                                                                                                                $('#view').trigger('click');
                                                                                                              }
                                                                                                              else{
                                                                                                                alert(result.msge);
                                                                                                              }
                                                                                                  });
                                                                                    }
                                                                                    });
                                                      $script
                                                      


                                 </script>";
                       print $tabla;
                      
        }
        elseif($accion == 'baja'){
             try{
                 $carga = $entityManager->find('CargaCombustible', $_POST['carga']);
                 $entityManager->remove($carga);
                 $entityManager->flush();
                 print json_encode(array('status' => true));
             } catch (Exception $e) {
                     print json_encode(array('status' => false, 'msge' => $e->getMessage()));
             }
        }
        elseif($accion == 'viewtag'){            
            $dql = "SELECT cc
                    FROM CargaCombustible cc
                    JOIN cc.unidad u";
            $where = "WHERE cc.ingreso = :ingreso ";
            if ($_POST['desde']){
                $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
                $where.= "AND cc.fecha >= :desde";
            }
            if ($_POST['hasta']){
              $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
              if($where){
                  $where.=" AND cc.fecha <= :hasta";
              }
              else{
                  $where =" WHERE cc.fecha <= :hasta";
              }
            }
            $motivo = null;
            if ($_POST['uso']){
              if ($where){
                $where.= " AND cc.usoTagMaestro = :uso";
              }
              else{
                  $where = " WHERE cc.usoTagMaestro = :uso";
              }
              if ($_POST['uso'] == '1'){
                if ($_POST['motivo']){
                  $where.= " AND cc.motivotagmaestro = :motivo";
                  $motivo = find('MotivoTAGMaestro', $_POST['motivo']);
                }
              }
            }

            if ($_POST['coches']){
              $unidad = find('Unidad', $_POST['coches']);
              if ($where){
                $where.= " AND u = :unidad";
              }
              else{
                $where.= " WHERE u = :unidad";
              }                              
            }
            try 
            {
                  $dql ="$dql $where ORDER BY cc.fecha, u.interno";
                  $a = $entityManager->createQuery($dql); 
                  if ($_POST['desde'])
                    $a->setParameter('desde', $desde->format('Y-m-d'));
                  if ($_POST['hasta'])
                    $a->setParameter('hasta', $hasta->format('Y-m-d'));
                  if ($_POST['coches'])
                    $a->setParameter('unidad', $unidad);
                  if ($_POST['uso'])
                    $a->setParameter('uso', $_POST['uso']);
                  if ($motivo)
                    $a->setParameter('motivo', $motivo);
                  $a->setParameter('ingreso', false);
                  $cargas = $a->getResult();
            }
            catch (Exception $e){print $e->getMessage();}

                       $tabla="<table class='table table-zebra' id='anomalias'>
                                      <thead>
                                             <tr>
                                                 <th>Fecha Carga</th>
                                                 <th>Interno</th>
                                                 <th>Ult. Lectura</th>
                                                 <th>Lect. Actual</th>
                                                 <th>Km</th>
                                                 <th>Litros</th>                                                 
                                                 <th>Uso TAG Maestro</th>
                                                 <th>Motivo </th>
                                                 <th>Observaciones</th>
                                                 <th>Fecha registro</th>
                                                 <th>Usuario registro</th>
                                             </tr>
                                      </thead>
                                      <tbody>";
                       $i=0;
                       $script = "";
                       foreach ($cargas as $carga) {
                              try{
                                   $last = getLasCarga($carga, $entityManager);
                                   $tabla.="<tr>
                                                <td>".$carga->getFecha()->format('d/m/Y')."</td>
                                                <td align='right'>".$carga->getUnidad()."</td>    
                                                <td>".($last?$last->getOdometro():'')."</td>                                          
                                                <td>".$carga->getOdometro()."</td>
                                                <td>".($last?($carga->getOdometro()-$last->getOdometro()):'')."</td>     
                                                <td>".$carga->getLitros()."</td>
                                                <td>".($carga->getUsoTagMaestro()?'SI':'NO')."</td>
                                                <td>".$carga->getMotivotagmaestro()."</td>
                                                <td>".$carga->getDescripcionMotivo()."</td>
                                                <td>".$carga->getFechaAlta()->format('d/m/Y H:i:s')."</td>
                                                <td>".$carga->getUsuario()->getUsername()."</td>
                                            </tr>";
                                  }
                                  catch (Exception $e){
                                                      die($e->getMessage());
                                                      }
                        }
                                           
                       $tabla.="</tbody>
                                </tabla>";
                       print $tabla;
                      
        }
  }
  else
  {
     $data  = explode("-",$_POST['id']);
     $reg = $data[0];
     $id    = $data[1]; // id del registro    
     $value = $_POST['value']; // valor por el cual reemplazar
     try{
         $carga = $entityManager->find('CargaCombustible', $id);
         if ($reg=='odometro'){
            $carga->setOdometro($value);
         }
         else{
            $carga->setLitros($value);
         }         
         $entityManager->flush();
         print $value;
     } catch (Exception $e) {
             print "ERROR";
     }    
  }

  function getLasCarga($carga, $em){
    $dql = "SELECT cc
            FROM CargaCombustible cc
            JOIN cc.unidad u
            WHERE u = :unidad AND cc.id < :carga
            ORDER BY cc.id DESC";
    $a = $em->createQuery($dql)
            ->setParameter('unidad', $carga->getUnidad())
            ->setParameter('carga', $carga->getId())
            ->setMaxResults(1)
            ->getOneOrNullResult();
    return $a;
  }

?>

