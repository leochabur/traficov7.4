<?php
  session_start();

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  

  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
 // use Symfony\Component\Validator\Validation;
  include ('../../modelsORM/src/CargaCombustible.php');
  $accion= $_POST['accion'];
//

  if ($accion == 'load'){
                 $a = $entityManager->createQuery("SELECT u.id, u.interno, u.dominio, u.nuevoDominio, MAX(c.fecha) as fecha, c.id as carga FROM Unidad u LEFT JOIN CargaCombustible c WITH c.unidad = u WHERE u.activo = :activo GROUP BY u ORDER BY u.interno");
                 $a->setParameter('activo', true);

                 $coches = $a->getResult();
               //  die(var_dump($coches));
                 $tabla="<table class='table table-zebra'>
                                <thead>
                                       <tr>
                                           <th>Dominio</th> 
                                           <th>Interno</th>
                                           <th>Registrar Carga</th>
                                           <th>Ver Ultimas Cargas</th>
                                       </tr>
                                </thead>
                                <tbody>";
                  $hoy = new DatetIme();

                 foreach($coches as $coche){
                    $class='';
                    if ($coche['carga']){
                        $fecha = DatetIme::createFromFormat('Y-m-d', $coche['fecha']);

                        $interval = $hoy->diff($fecha)->format('%d');
                        if ($fecha < $hoy){
                          if ($interval == 1)
                            $class= 'amarillo';
                          elseif ($interval > 1)
                            $class='rojo';
                        }
                        else{
                          $class='verde';
                        }
                        //die($interval);

                        
                        $dias.=" / ".$interval;
                    }
                     $tabla.="<tr class='$class'>
                                  <td class='$class'>".($coche['dominio']?$coche['dominio']:$coche['nuevoDominio'])."</td>
                                  <td class='$class'>$coche[interno]</td>
                                  <td class='$class' align='center'><a href='#' class='addcga' data-id='".$coche['id']."'><i class='fas fa-gas-pump fa-2x'>$coche[1]</i></a></td>
                                  <td class='$class' align='center'><a href='#' class='viewcga' data-id='".$coche['id']."'><i class='fas fa-search fa-2x'></i></a></td>
                              </tr> ";
                 }
                 $tabla.="</tbody></table>
                 <script>
                        $('.addcga').button().click(function(event){
                                                                    event.preventDefault();
                                                                    if (confirm('Guardar carga combustible?'))
                                                                      $.post('/modelo/taller/gstcgl.php',
                                                                            {accion:'sve', fcha: $('#fecha').val(), tno: $('#turnos').val(), cche: $(this).data('id')},
                                                                            function(data){
                                                                                           var response =  $.parseJSON(data);
                                                                                           if(!response.status)
                                                                                              alert(response.message);
                                                                              });
                                                                    
                          });
                        $('.viewcga').button().click(function(event){
                                                                    event.preventDefault();
                                                                    var unit = $(this).data('id');
                                                                    $('#detap').remove();
                                                                    var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                             title: 'Ultimas Cargas Registradas',
                                                                                             width:400,
                                                                                             height:350,
                                                                                             modal:true,
                                                                                             autoOpen: false
                                                                                    });
                                                                    dialog.load('/modelo/taller/gstcgl.php',
                                                                                            {accion: 'loadcgas', cche: unit},
                                                                                            function (){ 
                                                                                            });
                                                                    dialog.dialog('open');
                          });                          
                 </script>";
                 print $tabla;    
  }
  elseif($accion == 'sve'){
    try{
        if (!$_POST['fcha']){
              print json_encode(array('status' => false, 'message'=>'Fecha Invalida!!'));
                exit();
        }
        $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fcha']);
        $carga = new CargaCombustible();
        $turno = find('Turno', $_POST['tno']);
        $coche = find('Unidad', $_POST['cche']);
        $carga->setTurno($turno);
        $carga->setUnidad($coche);
        $carga->setFecha($fecha);
        $entityManager->persist($carga);
        $entityManager->flush();
        print json_encode(array('status' => true));
    } catch (Exception $e) {
        print json_encode(array('status' => false, 'message'=>'Se han producido errores alrealizar la accion!! '.$e->getMessage()));
    }
  }
  elseif($accion == 'loadcgas'){
      $unidad = find('Unidad', $_POST['cche']);
      $a = $entityManager->createQuery("SELECT c FROM CargaCombustible c WHERE c.unidad = :unidad ORDER BY c.fecha DESC"); 
      $a->setParameter('unidad', $unidad);
      $a->setMaxResults(10);
      $cargas = $a->getResult();

      $tabla="<table class='table table-zebra'>
                                <thead>
                                       <tr>
                                           <th>Dominio</th> 
                                           <th>Interno</th>
                                           <th>Fecha Carga</th>
                                           <th>Turno</th>
                                       </tr>
                                </thead>
                                <tbody>";
      foreach($cargas as $carga){
                     $tabla.="<tr class='$class'>
                                  <td class='$class'>".($carga->getUnidad()->getDominio()?$carga->getUnidad()->getDominio():$carga->getUnidad()->getNuevoDominio())."</td>
                                  <td class='$class'>".$carga->getUnidad()->getInterno()."</td>
                                  <td class='$class' align='center'>".$carga->getFecha()->format('d/m/Y')."</td>
                                  <td class='$class' align='center'>".$carga->getTurno()."</td>
                              </tr> ";
      }
      $tabla.="</tbody></table>";
      print $tabla;
  }

  ?>