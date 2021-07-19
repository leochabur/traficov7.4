<?php
  session_start();
    
//
    set_time_limit(0);
error_reporting(0);
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  
  $script = $_SERVER['PHP_SELF'];
  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/call.php';
  include ('../../modelsORM/src/CargaCombustible.php');
  use Doctrine\ORM\Tools\Pagination\Paginator;
  $accion= $_POST['accion'];
//

  if ($accion == 'load'){

    try{
                 $motivos = call('MotivoTAGMaestro', 'findAll');
                 $options = "";
                 foreach ($motivos as $mot) {
                    $options.="<option value='".$mot->getId()."'>$mot</options>";
                 }
                 if (!in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132'))){
                    $filter = " AND et.mostrar = :mostrar";
                 }
                 $a = $entityManager->createQuery("SELECT u.id, u.interno, u.dominio, u.nuevoDominio, MAX(cc.fecha) as fecha, cc.id as carga, u.capacidadTanque as capa, u.consumo as consumo, et.disponible as disp, et.mostrar as mostrar, et.id as idEstado
                                                   FROM Unidad u 
                                                   JOIN u.tipoUnidad tu
                                                   LEFT JOIN CargaCombustible cc WITH cc.unidad = u  
                                                   LEFT JOIN EstadoTanque et WITH et.unidad = u
                                                   WHERE u.activo = :activo AND u.estructura = :estructura $filter
                                                   GROUP BY u.id
                                                   ORDER BY u.interno");
                 $a->setParameter('activo', true);
                 $a->setParameter('estructura', $_SESSION['structure']);
                 if (!in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132'))){
                    $a->setParameter('mostrar', true);
                 }                 
                 $coches = $a->getResult();
}catch (Exception $e){die($e->getMessage());}
                 $header = "";
                 if (in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132'))){
                    $header = "<th>Mostrar/Ocultar</th>";
                  }
                  $tabla="<table class='table table-zebra'>
                                <thead>
                                       <tr>
                                           <th>Dominio</th> 
                                           <th>Interno</th>
                                           <th>Registrar Carga</th>
                                           <th>Ver Ultimas Cargas</th>
                                           $header
                                       </tr>
                                </thead>
                                <tbody>";
                  $hoy = new DatetIme();

                  foreach($coches as $coche){
                    $class='';
                    if ($coche['carga']){
                        if ($coche['consumo']){
                          $autonomia = ($coche['capa']/$coche['consumo']);
                          if (($coche['disp']/$autonomia) > 0.89){
                            $class = 'verde';
                          }
                          else{
                              $fecha = DatetIme::createFromFormat('Y-m-d', $coche['fecha']);
                              $interval = $hoy->diff($fecha)->format('%d');
                              if ($fecha < $hoy){                          
                                  $class= 'amarillo';
                              }
                              else{
                                $class='verde';
                              }                      
                          }
                        }
                        else{
                            $fecha = DatetIme::createFromFormat('Y-m-d', $coche['fecha']);
                            $interval = $hoy->diff($fecha)->format('%d');
                            if ($fecha < $hoy){                          
                                $class= 'amarillo';
                            }
                            else{
                              $class='verde';
                            }
                      }
                    }

                    if (in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132'))){
                        if (!$coche['mostrar']){
                          $show = "<td class='$class' align='center'><a href='#' data-sh='s' class='show' data-msge='MOSTRAR' data-int='$coche[interno]' data-st='$coche[idEstado]' title='Mostrar'><i class='far fa-eye fa-2x'></i></a></td>";
                        }
                        else{
                          $show = "<td class='$class' align='center'><a href='#' data-sh='o' class='show' data-msge='OCULTAR' data-int='$coche[interno]' data-st='$coche[idEstado]' title='Ocultar'><i class='far fa-eye-slash fa-2x'></i></a></td>";
                        }
                    }
                    $tabla.="<tr class='$class' data-etc='holis'>
                                  <td class='$class'>".($coche['dominio']?$coche['dominio']:$coche['nuevoDominio'])."</td>
                                  <td class='$class'>$coche[interno]</td>
                                  <td class='$class' align='left'>
                                      <form data-class='$class'>                                      
                                          <input type='text' placeholder='Tacografo' size='5' name='odometro'/>                                          
                                          <input type='text' placeholder='Litros' size='5' name='litros'/>
                                          TAG Maestro
                                          <select name='usoTag' class='chtag'>
                                              <option value='n'>No</option>
                                              <option value='s'>Si</option>
                                          </select>
                                          <span class='tag'>Motivo</span>
                                          <select class='tag' name='motivoTag'>
                                            $options
                                          </select>
                                          <span class='tag'>Observ.</span>
                                          <input class='tag' type='text' name='observaTag'/>
                                          <input type='hidden' name='interno' value='$coche[id]'/>
                                          <input type='hidden' name='accion' value='sve'/>                                                                                
                                          <a href='#' class='addcga' data-fluid='1' data-msge='GAS OIL' data-id='".$coche['id']."'><i class='fas fa-gas-pump fa-2x'></i></a>
                                          <a href='#' class='addcga' data-fluid='2' data-msge='UREA' data-id='".$coche['id']."'><i class='fas fa-seedling fa-2x'></i></a>
                                          <input type='hidden' name='tipofluido'/>    
                                      </form>
                                  </td>                                      
                                  <td class='$class' align='center'><a href='#' class='viewcga' data-id='".$coche['id']."' text='Registrar'><i class='fas fa-search fa-2x'></i></a></td>
                                  $show
                              </tr> ";
                 }
                 $tabla.="</tbody></table>
                 <script>
                        $('.tag').hide();
                        $('.chtag').change(function(){
                                                        var tag = $(this);
                                                        if (tag.val() == 's'){
                                                          tag.siblings('.tag').show();
                                                        }
                                                        else
                                                          tag.siblings('.tag').hide();

                          });
                        $('.addcga').button().click(function(event){
                                                                    
                                                                    event.preventDefault();
                                                                    var a = $(this);
                                                                    var form = $(this).parent();
                                                                    if (confirm('Seguro registrar la carga de '+a.data('msge')+'?'))
                                                                      $.post('/modelo/taller/gstcgl.php',
                                                                            form.serialize()+a.data('fluid'),
                                                                            function(data){
                                                                                           var response =  $.parseJSON(data);
                                                                                           if(!response.status)
                                                                                              alert(response.message);
                                                                                            else{
                                                                                              a.siblings('.tag').hide();
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
                        $('.show').button().click(function(event){
                                                                    event.preventDefault(); 
                                                                    var a = $(this);
                                                                    if (confirm('Seguro '+a.data('msge')+' el interno '+a.data('int')+'?')){
                                                                        $.post('/modelo/taller/gstcgl.php',
                                                                               {accion: 'show', et: a.data('st'), sh: a.data('sh')},
                                                                               function(data){
                                                                                    var response = $.parseJSON(data);
                                                                                    if (response.status)
                                                                                    {
                                                                                        a.parent().html(response.message);
                                                                                    }
                                                                                    else{
                                                                                      alert(response.message);
                                                                                    }
                                                                                });
                                                                    }
                                                                    
                        });

                        $('.viewcga').button().click(function(event){
                                                                    event.preventDefault();
                                                                    var unit = $(this).data('id');
                                                                    $('#detap').remove();
                                                                    var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                             title: 'Ultimas Cargas Registradas',
                                                                                             width:550,
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
  elseif($accion == 'show'){
    try
    {
      $estado = find('EstadoTanque', $_POST['et']);
      $estado->setMostrar(($_POST['sh'] != 'o'));
      $entityManager->flush();
      print json_encode(array('status' => true, 'message' => ($_POST['sh']=='o')?'Oculto':'Visible'));
    } catch (Exception $e) {
        print json_encode(array('status' => false, 'message'=>'Se han producido errores alrealizar la accion!! '.$e->getMessage()));
    }    
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


        $tipoFluido = find('TipoFluido', $_POST['tipofluido']);
        $fecha = new DatetIme();
        $carga = new CargaCombustible();
        if ($_POST['usoTag'] == 's'){
          $carga->setUsoTagMaestro(true);
          $motivo = find('MotivoTAGMaestro', $_POST['motivoTag']);
          $carga->setMotivotagmaestro($motivo);
          $carga->setDescripcionMotivo($_POST['observaTag']);
        }
        $carga->setTipoFluido($tipoFluido);
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
                                           <th>Fecha/Hora Carga</th>
                                           <th>Producto</th>
                                           <th>Tacografo</th>
                                           <th>Cant. Litros</th>
                                       </tr>
                                </thead>
                                <tbody>";
      foreach($cargas as $carga){
                     $tabla.="<tr class='$class'>
                                  <td class='$class'>".($carga->getUnidad()->getDominio()?$carga->getUnidad()->getDominio():$carga->getUnidad()->getNuevoDominio())."</td>
                                  <td class='$class'>".$carga->getUnidad()->getInterno()."</td>
                                  <td class='$class' align='center'>".$carga->getFechaAlta()->format('d/m/Y H:i')."</td>
                                  <td class='$class' align='center'>".$carga->getTipoFluido()->getTipo()."</td>                                  
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

         if ($_SESSION['PAGE'] == 0)
         {
            if (!isset($_SESSION['NUMPAGE']))
            {
              $_SESSION['NUMPAGE'] = 0;
            }
            else{
                if ($_SESSION['NUMPAGE'] < 2)
                {
                  if ($_SESSION['NUMPAGE'] == 0)
                  {
                    $_SESSION['NUMPAGE'] = $_SESSION['NUMPAGE'] + 1;
                    print json_encode(array('type' => 'v', 'resource' => '<object width="100%" height="768px" type="application/pdf" data="/vista/page_1.pdf?#page=2&zoom=100&scrollbar=0&toolbar=0&navpanes=0&view=fit" allowfullscreen></object>'));
                    exit();
                  }
                  elseif ($_SESSION['NUMPAGE'] == 1) {
                    $_SESSION['NUMPAGE'] = $_SESSION['NUMPAGE'] + 1;
                    print json_encode(array('type' => 'v', 'resource' => '<object width="100%" height="768px" type="application/pdf" data="/vista/page_2.pdf?#page=2&zoom=100&scrollbar=0&toolbar=0&navpanes=0&view=fit" allowfullscreen></object>'));
                    exit();
                  }
                }
                $_SESSION['NUMPAGE'] = 0;
            }

          }
        
        $estructura = find('Estructura', $_SESSION['structure']); 
        $cargas = $entityManager->createQuery("SELECT c
                                          FROM EstadoTanque c 
                                          JOIN c.unidad u 
                                          WHERE u.activo = :activo AND u.estructura = :estructura AND c.mostrar = :mostrar
                                          ORDER BY u.interno")
                                ->setParameter('activo', true)
                                ->setParameter('estructura', $estructura)            
                                ->setParameter('mostrar', true)      
                                ->setFirstResult($_SESSION['PAGE'])
                                ->setMaxResults($_SESSION['COUNTORIG']);
        $paginator = new Paginator($cargas, $fetchJoinCollection = true);
        
        $_SESSION['PAGE']+= $_SESSION['COUNTORIG'];
        
        $tablaTotal = "<table border='0'>
                              <tr>";

        $i = 0;
        $j = 0;
        foreach($paginator as $carga){
                $color = (($i % 2)?'#FF8040':'#FFFFFF');
                $font = (($i % 2)?'#FFFFFF':'#FF8040');
                if ($i == 0)
                {  $tablaTotal.="<td valign='top'>";
                   $tablaTotal.= encabezadoTabla();
                }
                $tablaTotal.= getTd($carga, $color, $font);
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
        print json_encode(array('type' => 'd', 'resource' => $tablaTotal));
    }catch (Exception $e) {print $e->getMessage();};
  }
  elseif($accion == 'sveing'){
    try{
        if (!is_numeric($_POST['litros'])){
              print json_encode(array('status' => false, 'message'=>'Los litros ingresados son incorrectos!!'));
              exit();
        }

        if (!($_POST['fecha'])){
              print json_encode(array('status' => false, 'message'=>'La fecha ingresada es invalida!!'));
              exit();
        }

        $fechaIngreso = (isset($_POST['fecha'])?$_POST['fecha']:date('d/m/Y'));
        $fecha = DatetIme::createFromFormat('d/m/Y', $fechaIngreso);
        $carga = new CargaCombustible();
		$tipoFluido = find('TipoFluido', $_POST['producto']);
        $destino = find('Destino', $_POST['destino']);
        $carga->setTipoFluido($tipoFluido);
        $usuario = find('Usuario', $_SESSION['userid']);

        $factura = $_POST['factura'];
        $concepto = $_POST['concepto'];
        $proveedor = $_POST['proveedor'];
        $carga->setFactura($factura);
        $carga->setConcepto($concepto);
        $carga->setProveedor($proveedor);
        $carga->setDestino($destino);
        $carga->setFecha($fecha);
        $carga->setUsuario($usuario);
        $carga->setOdometro(0);
        $carga->setIngreso(true);
        $carga->setLitros($_POST['litros']);        
        $entityManager->persist($carga);
        $entityManager->flush();
        print json_encode(array('status' => true));
    } catch (Exception $e) {
        print json_encode(array('status' => false, 'message'=>'Se han producido errores alrealizar la accion!! '.$e->getMessage()));
    }
  }

  function encabezadoTabla(){
                  $tabla="<table class='ui-widget ui-widget-content ui-corner-all table-zebra' border='1'>
                                <thead class='ui-widget-header'>
                                       <tr>
                                           <th>Interno</th>
                                           <th>Km Disponibles</th>

                                       </tr>
                                </thead>
                                <tbody>";
         return $tabla;
  }
  
  function getTd($carga, $color, $font)
  {        if ($carga->getDisponible() <= 0)
              $disp = "";
           else
               $disp = $carga->getDisponible();
                     $tabla.="<tr bgcolor='$color'>
                                  <td bgcolor='$color'><font color='$font'><b>".$carga->getUnidad()->getInterno()."</b></font></td>
                                  <td bgcolor='$color' align='center'><font color='$font'><b>".$disp."</b></font></td>
                              </tr> ";
                     return $tabla;
  }

  ?>
