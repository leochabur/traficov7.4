<?php
  session_start();
    
//
    set_time_limit(0);
//error_reporting(1);
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  
//$script = $_SERVER['PHP_SELF'];
  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
  include ('../../modelsORM/src/CargaCombustible.php');
  use Doctrine\ORM\Tools\Pagination\Paginator;
  $accion= $_POST['accion'];
//

  if ($accion == 'load'){

                 $a = $entityManager->createQuery("SELECT u.id, u.interno, u.dominio, u.nuevoDominio, MAX(c.fecha) as fecha, c.id as carga FROM Unidad u LEFT JOIN CargaCombustible c WITH c.unidad = u WHERE u.activo = :activo AND u.estructura = :str GROUP BY u ORDER BY u.interno");
                 $a->setParameter('activo', true);
				 $a->setParameter('str', $_SESSION['structure']);
                 $coches = $a->getResult();
                // die(var_dump($coches));/*
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
                            $class= 'amarillo';
                        }
                        else{
                          $class='verde';
                        }
                    }
                    $tabla.="<tr class='$class' data-etc='holis'>
                                  <td class='$class'>".($coche['dominio']?$coche['dominio']:$coche['nuevoDominio'])."</td>
                                  <td class='$class'>$coche[interno]</td>
                                  <td class='$class' align='center'>
                                      <form data-class='$class'>
                                      <input type='text' placeholder='Tacografo' size='5' name='odometro'/>
                                      <input type='text' placeholder='Litros' size='5' name='litros'/>
                                      <input type='hidden' name='interno' value='$coche[id]'/>
                                      <input type='hidden' name='accion' value='sve'/>
                                      <a href='#' class='addcga' data-id='".$coche['id']."'><i class='fas fa-gas-pump fa-2x'></i></a></td>
                                      </form>
                                  <td class='$class' align='center'><a href='#' class='viewcga' data-id='".$coche['id']."' text='Registrar'><i class='fas fa-search fa-2x'></i></a></td>
                              </tr> ";
                 }
                 $tabla.="</tbody></table>
                 <script>
                        $('.addcga').button().click(function(event){
                                                                    event.preventDefault();
                                                                    var form = $(this).parent();
                                                                    if (confirm('Guardar carga combustible?'))
                                                                      $.post('/modelo/taller/gstcgl.php',
                                                                            form.serialize(),
                                                                            function(data){
                                                                                           var response =  $.parseJSON(data);
                                                                                           if(!response.status)
                                                                                              alert(response.message);
                                                                                            else{
                                                                                              form[0].reset();
                                                                                              var tr = form.parent().parent();
                                                                                              var classe = form.data('class');
                                                                                              tr.find('td').each(function(){
                                                                                                  if (classe)
                                                                                                    $(this).removeClass(classe);
                                                                                                  $(this).addClass('verde');
                                                                                              })
                                                                                              
                                                                                            }
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

        if (!is_numeric($_POST['odometro'])){
              print json_encode(array('status' => false, 'message'=>'Los datos del odometro son incorrectos!!'));
              exit();
        }
        if (!is_numeric($_POST['litros'])){
              print json_encode(array('status' => false, 'message'=>'Los litros ingresados son incorrectos!!'));
              exit();
        }

        $fecha = new DatetIme();
        $carga = new CargaCombustible();

        $coche = find('Unidad', $_POST['interno']);

        $usuario = find('Usuario', $_SESSION['userid']);

        $carga->setUnidad($coche);
        $carga->setFecha($fecha);
        $carga->setUsuario($usuario);
        $carga->setOdometro($_POST['odometro']);
        $carga->setLitros($_POST['litros']);        
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
                                           <th>Tacografo</th>
                                           <th>Cant. Litros</th>
                                       </tr>
                                </thead>
                                <tbody>";
      foreach($cargas as $carga){
                     $tabla.="<tr class='$class'>
                                  <td class='$class'>".($carga->getUnidad()->getDominio()?$carga->getUnidad()->getDominio():$carga->getUnidad()->getNuevoDominio())."</td>
                                  <td class='$class'>".$carga->getUnidad()->getInterno()."</td>
                                  <td class='$class' align='center'>".$carga->getFecha()->format('d/m/Y')."</td>
                                  <td class='$class' align='center'>".$carga->getOdometro()."</td>
                                  <td class='$class' align='center'>".$carga->getLitros()."</td>
                              </tr> ";
      }
      $tabla.="</tbody></table>";
      print $tabla;
  }
  elseif($accion == 'setTque'){
                 $a = $entityManager->createQuery("SELECT u FROM Unidad u WHERE u.activo = :activo AND u.estructura = :str ORDER BY u.interno");
                 $a->setParameter('activo', true);
				 $a->setParameter('str', $_SESSION['structure']);
                 $coches = $a->getResult();
                 $tabla="<table class='table table-zebra' align='left'>
                                <thead>
                                       <tr>
                                           <th>Interno</th> 
										   <th>Dominio</th>
                                           <th>Capacidad Tanque</th> 
										   <th>Consumo c/ 100 km</th>
										   <th>Guardar</th>
                                       </tr>
                                </thead>
                                <tbody>";
                 foreach($coches as $coche){
                    $tabla.="<tr>
									<td>".$coche->getInterno()." </td>
									<td>".strtoupper($coche->getDominio()?$coche->getDominio():$coche->getNuevoDominio())." </td>
									<td><input type='text' id='cap-".$coche->getId()."' size='4' value='".$coche->getCapacidadTanque()."' class='ui-widget ui-widget-content  ui-corner-all'></td>
									<td><input type='text' id='con-".$coche->getId()."' size='4' value='".($coche->getConsumo()*100)."' class='ui-widget ui-widget-content  ui-corner-all'></td>
									<td align='center'><input type='button' value='Guardar' class='tque' data-id='".$coche->getId()."'></td>
                             </tr> ";
				 }
				 $tabla.="</tbody>
						  </table>
						  <script>
									$('.tque').button().click(function(){
																			var btn = $(this);
																			var parent = btn.parent();
																			var id =  btn.data('id');
																			var cap = $('#cap-'+id).val();
																			var con = $('#con-'+id).val();
																			parent.html(\"<img src='../../vista/loading.gif' width='20' height='20' border='0'>\");
																			$.post('$script',
																				   {accion:'svet', int:id, ca:cap, co:con},
																				   function(data){
																									var result = $.parseJSON(data);
																									if (result.status){
																										parent.html(\"<img src='../../vista/si.png' width='17' height='17' border='0'>\");
																									}
																									else{
																										parent.html(\"<img src='../../vista/no.png' width='17' height='17' border='0'>\");
																									}
																					});
																		});
						  </script>";
				print $tabla;
  }
  elseif($accion == 'svet'){
	  try
		  {
	      $unidad = find('Unidad', $_POST['int']);
		  $unidad->setCapacidadTanque($_POST['ca']);
		  $unidad->setConsumo(($_POST['co']/100));
		  $entityManager->flush();
		  print json_encode(array('status' => true));
		} 
		catch (Exception $e) {
								print json_encode(array('status' => false)); 
							}				
  }
  elseif($accion == 'state'){
     try{
         if (isset($_SESSION['LASTCOUNT'])){
            if ($_SESSION['LASTCOUNT'] < $_SESSION['COUNTORIG']){
               $_SESSION['PAGE'] = 0;
            }
         }

        $estructura = find('Estructura', $_SESSION['structure']); 
        $cargas = $entityManager->createQuery("SELECT c
                                          FROM EstadoTanque c 
                                          JOIN c.unidad u 
                                          WHERE u.activo = :activo AND u.estructura = :estructura
                                          ORDER BY u.interno")
                                ->setParameter('activo', true)
                                ->setParameter('estructura', $estructura)                                
                                ->setFirstResult($_SESSION['PAGE'])
                                ->setMaxResults($_SESSION['COUNTORIG']);
        $paginator = new Paginator($cargas, $fetchJoinCollection = true);
        
        $_SESSION['PAGE']+= $_SESSION['COUNTORIG'];
        
        $tablaTotal = "<table border='0'>
                              <tr>";

        $i = 0;
        $j = 0;
        foreach($paginator as $carga){
                if ($i == 0)
                {  $tablaTotal.="<td valign='top'>";
                   $tablaTotal.= encabezadoTabla();
                }
                $tablaTotal.= getTd($carga);
                $i++;
                if ($i == $_SESSION["COUNTPORPAGE"])
                {
                   $tablaTotal.="</tbody></table>";
                   $tablaTotal.="</td>";
                   $i = 0;
                }
                $j++;
        }
        $_SESSION['LASTCOUNT'] = $j;
        print $tablaTotal;
    }catch (Exception $e) {print $e->getMessage();};
  }

  function encabezadoTabla(){
                  $tabla="<table class='ui-widget ui-widget-content ui-corner-all table-zebra'>
                                <thead class='ui-widget-header'>
                                       <tr>
                                           <th>Dominio</th>
                                           <th>Interno</th>
                                           <th>Km Disponibles</th>

                                       </tr>
                                </thead>
                                <tbody>";
         return $tabla;
  }
  
  function getTd($carga)
  {        if ($carga->getDisponible() <= 0)
              $disp = "";
           else
               $disp = $carga->getDisponible();
                     $tabla.="<tr class='$class'>
                                  <td class='$class'>".($carga->getUnidad()->getDominio()?$carga->getUnidad()->getDominio():$carga->getUnidad()->getNuevoDominio())."</td>
                                  <td class='$class'><b>".$carga->getUnidad()->getInterno()."</b></td>
                                  <td class='$class' align='center'>".$disp."</td>
                              </tr> ";
                     return $tabla;
  }

  ?>
