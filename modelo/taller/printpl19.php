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

                      $q = $entityManager->createQuery("SELECT cc
                                                        FROM CargaCombustible cc
                                                        LEFT JOIN cc.unidad u
                                                        WHERE cc.fecha = :fecha
                                                        ORDER BY u.interno");    
                      $result = $q->setParameter('fecha', $fecha->format('Y-m-d'))->getResult();



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
                                   if (in_array($_SESSION['userid'], array('25', '3', '17')))
                                                $tabla.="<td align='center'><img class='delcga' id='".$carga->getId()."' src='../../eliminar.png' border='0' width='25' height='25'></td>";
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
                                                      $('.update').editable('/modelo/taller/printpl.php', {submit : 'Cambiar'});


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

?>

