<?php
  session_start();

 // include '../../modelsORM/src/lavadero/AccionUnidad.php';
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

if($accion == 'resumen'){
    try{
                 $tipo = find('TipoFluido', $_POST['tipo']);

                 $a = $entityManager->createQuery("SELECT sum(c.litros) as litros, c.ingreso as ingreso
                                                   FROM CargaCombustible c 
                                                   JOIN c.tipoFluido t                                            
                                                   WHERE c.fecha < :desde AND t = :tipo
                                                   GROUP BY c.ingreso");
                 $a->setParameter('tipo', $tipo);
                 $a->setParameter('desde', $_POST['desde']);            
                 $saldo = $a->getResult();


                 $cards = '<div class="card-group">';
                 $saldoInicial = 0;
                 foreach ($saldo as $s) {
                    if ($s['ingreso'])
                      $saldoInicial+=$s['litros'];
                    else
                      $saldoInicial-=$s['litros'];
                 }

                 $cards.="<div class='card mb-4'>
                          <div class='card-header text-white  red lighten-1 mb-3'>
                            Saldo Inicial
                          </div>
                          <div class='card-body text-right'>
                            $saldoInicial
                          </div>
                          </div>";

                 $a = $entityManager->createQuery("SELECT c, sum(c.litros) as litros, max(c.fecha) as ultCarga, (max(c.odometro) - min(c.odometro)) as km
                                                   FROM CargaCombustible c 
                                                   JOIN c.tipoFluido t                  
                                                   LEFT JOIN c.unidad u                            
                                                   WHERE c.fecha between :desde AND :hasta AND t = :tipo
                                                   GROUP BY u.id, c.ingreso
                                                   ORDER BY u.interno");
                 $a->setParameter('tipo', $tipo);
                 $a->setParameter('desde', $_POST['desde']);
                 $a->setParameter('hasta', $_POST['hasta']);         
                 //$a->setParameter('ingreso', false);         
                 $cargas = $a->getResult();
                 
        }catch (Exception $e){die($e->getMessage());}
                 $egresos = 0;
                 $ingresos = 0;
                 $body="";
                 foreach($cargas as $carga){
                  if ($carga[0]->getIngreso()){
                    $ingresos+=$carga['litros'];
                  }
                  else{
                     $last = "";
                     if ($carga['ultCarga']){
                        $last = DateTime::createFromFormat('Y-m-d', $carga['ultCarga']);
                        $last = $last->format('d/m/Y');
                     }
                     $egresos+=$carga['litros'];
                     $body.="<tr>
                                  <td>".$carga[0]->getUnidad()."</td>    
                                  <td>".$carga['litros']."</td>            
                                  <td>".$last."</td>                                      
                                  <td>".$carga['km']."</td>                                                                                           
                              </tr> ";
                    }
                 }
                 $cards.="<div class='card mb-4'>
                          <div class='card-header text-white red lighten-1 mb-3'>
                            Ingresos
                          </div>
                          <div class='card-body text-right'>
                            $ingresos
                          </div>
                          </div>";
                 $cards.="<div class='card mb-4'>
                            <div class='card-header text-white red lighten-1 mb-3'>
                              Egresos
                            </div>
                            <div class='card-body text-right'>
                              $egresos
                            </div>
                          </div>";
                 $cards.="<div class='card mb-4'>
                            <div class='card-header text-white red lighten-1 mb-3'>
                              Saldo Final
                            </div>
                            <div class='card-body text-right'>
                              ".($saldoInicial+$ingresos-$egresos)."
                            </div>
                          </div>
                          </div>
                          <hr>";                          
                 $tabla="$cards<table class='table table-striped table-bordered table-sm table-hover' id='dtBasicExample'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Litros</th>
                                       <th>Fecha Ult. Carga</th>                                       
                                       <th>KM S/ Tacografo</th>
                                       </tr>
                                </thead>
                                <tbody>".$body;
                 $tabla.="</tbody>
                          </table>
                          <script>
                                v34('#dtBasicExample').DataTable({
                                                                  'paging': false,
                                                                  'searching': false
                                                                });
                                v34('.dataTables_length').addClass('bs-select');
                          </script>";
                 print $tabla;
  }


  
?>

