<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include('../../modelsORM/manager.php'); 

include_once('../../modelsORM/call.php'); 
include_once('../../modelsORM/controller.php'); 
include_once('../../modelsORM/src/LineaPlanilla.php'); 
//use Symfony\Component\Validator\Validation;

$accion = $_POST['accion'];

if ($accion == 'ldln'){

	$newTarifa = "";
	$planilla = find('PlanillaDiaria', $_POST['pl']);

	$select = '<select id="bl" name="bl" class="ui-widget ui-widget-content  ui-corner-all">
					<option>Seleccione una opcion</option>';                                             
                                                    
	foreach ($planilla->getBloques() as $value) {
		$select.="<option value='".$value->getId()."'>$value</option>";
	}
    $select.="</select>
    		  <input type='button' value='Reordenar' id='numerar'/>
    		  <script>
    		  	$('#numerar').button().click(function(){
		    		  									$.post('/vista/servicios/addLnPl.php', 
															{accion: 'numerar', bloque:$('#bl').val()}, 
															function(data){
																					var response = $.parseJSON(data);
																					if (response.ok){
																						$('#bl').trigger('change');
																					}
																					else{
																						alert(response.msge);
																					}
															});		
    		  	});

    		  	$('#bl').selectmenu({width: 250}); 
    		  	$('#bl').change(function(){
											$.post('/vista/servicios/addLnPl.php', 
													{accion: 'ldlnbl', bl:$('#bl').val()}, 
													function(data){
																	$('#dats').html(data);
													});
    		  	});
    		  </script>";
    print $select;
    exit();


}
elseif($accion == 'ldlnbl'){
	try{

		$bloque = find('BloquePlanilla', $_POST['bl']);
		$articulos = articulosClientesOption($bloque->getPlanilla()->getCliente());
		$servicios = getServiciosCliente($bloque->getPlanilla()->getCliente());
		$ciudades = ciudadesOptions();
		$options;
		foreach ($servicios as $value) {
			if ($value->getIdaVelta() == 'i'){
				$hora = $value->getLlegada();
			}
			else{
				$hora = $value->getSalida();
			}
			$options.="<option value='".$value->getId()."'>".strtoupper($value->getCronograma())." - ".$hora->format('H:i')." (".$value->getId().")</option>";
		}
		$tabla = "<table class='table table-zebra'>
					<thead>
						<tr>
							<th colspan='7'>$bloque</th>
						</tr>
						<tr>
							<th>Quitar</th>
							<th>Orden</th>
							<th>".$bloque->getTituloEntrada()."</th>
							<th>Localidad</th>
							<th>Recorrido</th>
							<th>Articulo</th>
							<th>".$bloque->getTituloSalida()."</th>
						</tr>
					</thead>
				</thead>
				<tbody>";
		foreach ($bloque->getLineas() as $line) {
				$in="";
				$out="";
				if ($line->getEntrada())
					$in = $line->getEntrada()->getCronograma()." - ".$line->getEntrada()->getLlegada()->format('H:i')."(".$line->getEntrada()->getId().")";
				if ($line->getSalida())
					$out = $line->getSalida()->getCronograma()." - ".$line->getSalida()->getSalida()->format('H:i')."(".$line->getSalida()->getId().")";		
				$tabla.="<tr>
							<td><input type='button' class='delLine' data-id='".$line->getId()."' value='Quitar'/></td>
							<td valign='middle'><input type='text' size='2' id='td-".$line->getId()."' value='".$line->getOrden()."'/><input type='button' id='".$line->getId()."' class='order' value='+'/></td>
							<td>$in</td>
							<td>".$line->getLocalidad()."</td>
							<td>$line</td>
							<td>".$line->getArticulo()."</td>
							<td>$out</td>
						</tr>";
		}

		$tabla.='</tbody>
				</table>
				 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Nueva Linea</legend>';
		$tabla.="<form id='formLine' action='/vista/servicios/addLnPl.php'>
					<table>
						<tr>
							<td>Localidad</td>
							<td>
								<select id='ciudad' name='ciudad'>
									$ciudades
								</select>
							</td>
						</tr>
						<tr>
							<td>Nombre Recorrido</td>
							<td><input type='text' name='nombre'></td>
						</tr>
						<tr>
							<td>Articulo</td>
							<td>
								<select id='article' name='article'>
									$articulos
								</select>
							</td>
						</tr>
						<tr>
							<td>Servicio Entrada</td>
							<td>
								<select id='s_ida' name='s_ida'>
									<option values='0'></option>
									$options
								</select>
							</td>
						</tr>
						<tr>
							<td>Servicio Salida</td>
							<td>
								<select id='s_vta' name='s_vta'>
									<option values='0'></option>
									$options
								</select>
							</td>
						</tr>
						<tr>
							<td colspan='2'> <input type='submit' value='Agregar Linea' id='saveLine'/></td>
						</tr>
					</table>
					<input type='hidden' name='accion' value='addLine'/>
					<input type='hidden' name='bloque' value='".$bloque->getId()."'/>
				 </form>
				 </fieldset>
				 <script>
					$('.order').button().click(function(){
														  var btn = $(this).attr('id');
														  var data = $('#td-'+btn).val();
														  $.post('/vista/servicios/addLnPl.php', 
																		{accion: 'chord', ln: btn, ord: data}, 
																		function(data){
																					var response = $.parseJSON(data);
																					if (response.ok){
																						$('#bl').trigger('change');
																					}
																					else{
																						alert(response.msge);
																					}
																		});
					});
				 	$('.delLine').button().click(function(){
						 									var btn = $(this);
						 									if (confirm('Seguro eliminar la linea?')){
																$.post('/vista/servicios/addLnPl.php', 
																		{accion: 'delLine', ln: btn.data('id')}, 
																		function(data){
																					var response = $.parseJSON(data);
																					if (response.ok){
																						$('#bl').trigger('change');
																					}
																					else{
																						alert(response.msge);
																					}
																		});
						 									}
				 	});
				 	$('#s_vta, #s_ida').selectmenu({width: 450}); 
				 	$('#article, #ciudad').selectmenu({width: 250}); 
				 	$('#saveLine').button();
				 	$('#formLine').submit(function(event){
				 											event.preventDefault();
				 											var f = $(this);
															$.post(f.attr('action'), 
																   f.serialize(), 
																	function(data){
																					var response = $.parseJSON(data);
																					if (response.ok){

																						$('#bl').trigger('change');
																					}
																	})
				 	});
				 </script>";
		print $tabla;

	}
	catch(Exception $e){
				    $response = array('status' => false, 'message' => $e->getMessage());
	    			print json_encode($response);
	}
}
elseif ($accion == 'chord'){
	try{		
		$linea = $entityManager->find('LineaPlanilla', $_POST['ln']);	
		$linea->setOrden($_POST['ord']);
		$entityManager->flush();
		print json_encode(array('ok' => true));
	}catch(Exception $e) {print json_encode(array('ok' => true, 'msge' => $e->getMessage()));}

}
elseif ($accion == 'delLine'){
	try{		
		$linea = $entityManager->find('LineaPlanilla', $_POST['ln']);	
		$entityManager->remove($linea);
		$entityManager->flush();
		print json_encode(array('ok' => true));
	}catch(Exception $e) {print json_encode(array('ok' => true, 'msge' => $e->getMessage()));}

}
elseif ($accion == 'numerar'){
	try{
		$bloque = find('BloquePlanilla', $_POST['bloque']);
		$i = 1;
		foreach ($bloque->getLineas() as $linea) {
			$linea->setOrden($i);
			$i++;
		}
		$entityManager->flush();
		print json_encode(array('ok' => true));
}catch(Exception $e) {print json_encode(array('ok' => true, 'msge' => $e->getMessage()));}

}
elseif ($accion == 'addLine'){
	try{
		$ciudad = getCiudad($_POST['ciudad'], $_SESSION['structure']);
		$entrada =getServicio($_POST['s_ida'], $_SESSION['structure']);
		$salida =getServicio($_POST['s_vta'], $_SESSION['structure']);
		$articulo = find('ArticuloCliente', $_POST['article']);
		$bloque = find('BloquePlanilla', $_POST['bloque']);
		$linea = new LineaPlanilla();
		$linea->setOrden($bloque->getLineas()->count()+1);
		$linea->setNombreLinea($_POST['nombre']);
		$linea->setLocalidad($ciudad);
		$linea->setArticulo($articulo);
		$linea->setEntrada($entrada);
		$linea->setSalida($salida);
		$linea->setBloque($bloque);

		$entityManager->persist($linea);	

		$entityManager->flush();
		print json_encode(array('ok' => true));
}catch(Exception $e) {print json_encode(array('ok' => true, 'msge' => $e->getMessage()));}

}
elseif ($accion == 'add'){

	$scriptTipos='function procesarTiposVehiculos(){
					var tiposAsignados = new Array();
					$("#tiposV tbody tr").each(function (index) {
															var tipo;
															var inc = false;
                 											$(this).children("td").each(function (index2) {							
                 																							if (index2 == 0){
                 																								var sino = $(this).children(":checkbox");
                 																								if (sino.is(":checked")){
                 																									tipo = new Object();
                 																									tipo.id = sino.data("id");
                 																									inc = true;
                 																								}
                 																							}
                 																							if (index2 == 3){
                 																								if (inc){
                 																									var monto = $(this).children(":text");
                 																									tipo.monto = monto.val();
                 																								}
                 																							}
                 																							if (index2 == 4){
                 																								var def = $(this).children(":radio");
                 																								if (inc){
	                 																								tipo.default = def.is(":checked");
                 																								}	
                 																							}
                 																							if (index2 == 2){
                 																								var art = $(this).children("select");
                 																								if (inc){
	                 																								tipo.articulo = art.val();
                 																								}	      							
                 																							}
                 																							});
                 											if (inc){
                 												tiposAsignados.push(tipo);
                 												inc = false;
                 											}
                 											});
                 									return tiposAsignados;
                 									}';


	$types = '<table id="tiposV" class="table">
		        	<thead>
						<tr>
							<th>Aplica Tipo Vehiculo</th>
							<th>Tipo Vehiculo</th>
							<th>Articulo</th>
							<th>Importe Tarifa</th>
							<th>Default</th>
		        		<tr>
					</thead>
					<tbody>';

	$tipos = listaTiposVehiculos($_SESSION['structure'], 0);
	$articulos = articulosClientesOption($_POST['cli']);
	foreach ($tipos as $t) {		
		$types.="<tr>
					<td><input type='checkbox' class='ui-widget ui-widget-content ui-corner-all' data-id='".$t->getId()."'></td>
					<td>$t</td>
					<td><select>$articulos</select></td>
					<td><input type='text' class='ui-widget ui-widget-content ui-corner-all'></td>
					<td><input type='radio' name='default'></td>
				</tr>";
	}
	$types.="</tbody></table>";

	$scriptDias='function procesarDias(){
					var diassAsignados = new Array();
					$("#diass tbody tr").each(function (index) {
		                 											$(this).children("td").each(function (index2) {
		                 												var sino = $(this).children(":checkbox");
                 														if (sino.is(":checked")){
                 															var dia = new Object();
                 															dia.numero = sino.data("id");
                 															diassAsignados.push(dia);
                 														}
		                 											});	
		                 										});
		            return diassAsignados;
		        }';		

	$tabla ='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		        <legend class="ui-widget ui-widget-header ui-corner-all">Nueva Tarifa</legend>
		        Nombre Tarifa
		        <input type="text" id="nameTf" name="nameTf" class="ui-widget ui-widget-content ui-corner-all" />
		        Liquida X Hora <input type="checkbox" name="lxh" class="lxh"/>
		        <input type="button" id="addtf" value="Guardar Tarifa">
		        '.$types.'	        
		        <table class="table table-zebra" id="diass">
					<thead>
						<tr>
							<th>Lunes</th>
							<th>Martes</th>
							<th>Miercoles</th>
							<th>Jueves</th>
							<th>Viernes</th>
							<th>Sabado</th>
							<th>Domingo</th>							
						</tr>
					</thead>
					<tbody>
					<tr>
						<td><input type="checkbox" data-id="1"></td>
						<td><input type="checkbox" data-id="2"></td>
						<td><input type="checkbox" data-id="3"></td>
						<td><input type="checkbox" data-id="4"></td>						
						<td><input type="checkbox" data-id="5"></td>
						<td><input type="checkbox" data-id="6"></td>
						<td><input type="checkbox" data-id="7"></td>						
					</tr>
					</tbody>
			</table>';

	$scriptCronos='function procesarCronogramas(){
					var cronosAsignados = new Array();
					$("#cronosinc tbody tr").each(function (index) {
		                 											$(this).children("td").each(function (index2) {
		                 												var sino = $(this).children(":checkbox");
                 														if (sino.is(":checked")){
                 															var crono = new Object();
                 															crono.id = sino.data("id");
                 															cronosAsignados.push(crono);
                 														}
		                 											});	
		                 										});
		            return cronosAsignados;
		        }';					
	$cronos = listadoCronogramas($_POST['cli']);
	$body = "";
	foreach ($cronos as $c)
	{
		$body.="<tr><td>".$c->getNombre()."</td><td>".$c->getOrigen()."</td><td>".$c->getDestino()."</td><td><input type='checkbox' data-id='".$c->getId()."'></td></tr>";
	}

	$cronos ="<table class='table table-zebra' id='cronosinc'>
					<thead>
						<tr>
							<th>Servicio</th>
							<th>Origen</th>
							<th>Destino</th>
							<th>Si/No</th>
						</tr>
					</thead>
					<tbody>
						$body
					</tbody>
				</table>";

	$script = "<script>
						$('#cronosinc').dataTable({'scrollY':200,
        									'scrollCollapse': true,
        									'jQueryUI': true,
        									 'orderClasses': false,
        									 'paging': false
    									  }); 
						$('#tipos').selectmenu({width: 200});  
						$('#addtf').button().click(function(){
																var lxh = 0;
																if ($('.lxh').prop('checked'))
																	lxh = 1;
																var tarifaTipo = JSON.stringify(procesarTiposVehiculos());

																var diasSemana = JSON.stringify(procesarDias());

																var cronograma = JSON.stringify(procesarCronogramas());
													
																var idf = $('#idfactcli').val();
																$.post('/modelo/informes/cria/factcli.php',
     																	{accion: 'nwtftv', nombre: $('#nameTf').val(), tarifas: tarifaTipo, dias: diasSemana, crono: cronograma, fact: idf, lh: lxh},
     																	function(res){	
     																			console.log(res);
     																	});
																
						});
						$scriptTipos
						$scriptDias
						$scriptCronos
				</script>";
	$tabla.="$cronos</fieldset>$script";
	print $tabla;
}
elseif($accion == 'ldcl'){
	$clioptions = clientesOptions($_POST['str']);
	$empty="<option value='0'>Seleccione un Cliente</option>";

	$script = "<script>
						$('#clientes').change(function(){
															$('#facts').html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
															$('#dats').html('');
															$.post('/vista/servicios/addtrfNew.php',
																	{accion: 'addfact', cli: $(this).val()},
																	function(data){
																		$('#facts').html(data);
																	});
						});
				</script>";


	print "<select id='clientes' class='ui-widget ui-widget-content  ui-corner-all'>$empty".$clioptions."</select>$script";  

}
elseif($accion == 'delOptionTarifa'){
	try{
	/*	$validator = Validation::createValidatorBuilder()
	                              ->addMethodMapping('loadValidatorMetadata')
	                              ->getValidator();*/
		$fact = find('TarifaServicio', $_POST['ts']);

		$delAction = $_POST['delAct'];

		if ($delAction == 'day'){
			$dia = find('DiaSemana', $_POST['param']);
			$fact->removeDiasSemana($dia);
		}
		elseif ($delAction == 'crono'){
			$crono = cronograma($_SESSION['structure'], $_POST['param']);
			$fact->removeCronograma($crono);
		}
		elseif($delAction == 'tpo'){
			$tarifa = find('TarifaTipoServicio', $_POST['param']);
			$fact->removeTarifasTipoVehiculo($tarifa);
			$entityManager->remove($tarifa);
		}


	   /* $errors = $validator->validate($fact);      
	    if (count($errors) > 0) 
	    {
	          $errores = "";
	          foreach($errors as $error)
	          {
	              $errores.= $error->getMessage();
	          }
	          $response = array('status' => false, 'message' => $errores);
	          print json_encode($response);
	          exit();
	    }		*/

		$entityManager->flush();
	    $response = array('status' => true, 'message' => "Accion realizada con exito!!");
	    print json_encode($response);
	    exit();	
	}
	catch(Exception $e){
				    $response = array('status' => false, 'message' => $e->getMessage());
	    			print json_encode($response);
	}
}
elseif($accion == 'addOptionTf'){
try{
	$fact = find('TarifaServicio', $_POST['ts']);
	$addAction = $_POST['addAct'];	
	if ($addAction == 'day'){
		$dia = find('DiaSemana', $_POST['param']);
		$fact->addDiasSemana($dia);
	}
	elseif($addAction == 'type'){  /////aggrega una nueva tarifa a la facturacion

		$tarifaTipo = new TarifaTipoServicio();
		$tipo = tipoV($_POST['typeNT'], $_SESSION['structure']);
		
		if ($_POST['artNT']){
			$articulo = find('ArticuloCliente', $_POST['artNT']);
			$tarifaTipo->setArticulo($articulo);
		}
		$tarifaTipo->setTipo($tipo);
		if ($_POST['montoNT'])
			$tarifaTipo->setImporte($_POST['montoNT']);
		$tarifaTipo->setDefecto(isset($_POST['presupuestada']));
		if (!$fact->existeTarifa($tipo)){
			
			$tarifaTipo->setTarifaServicio($fact);
			$fact->addTarifasTipoVehiculo($tarifaTipo);
			$entityManager->persist($tarifaTipo);
		}
		else{
				$response = array('status' => false, 'message' => 'Ya existe una tarifa cargada para el tipo de Vehiculo!!');
				print json_encode($response);
				exit();
		}

	}
	elseif($addAction == 'crono'){
		$crono = cronograma($_SESSION['structure'], $_POST['param']);
		/*if ($fact->existeCronograma($crono)){
	          $response = array('status' => false, 'message' => 'El servicio ya esta asignado a la Tarifa');
	          print json_encode($response);
	          exit();
		}*/
		$fact->addCronograma($crono);
	}	

	
		$entityManager->flush();
    	$response = array('status' => true, 'message' => "Accion realizada con exito!!");
    	print json_encode($response);
    	exit();	
	}
	catch(Exception $e) {
						          			  $response = array('status' => false, 'message' => 'No se ha podido realizar la accion solicitada!!');
									          print json_encode($response);
									          exit();
	}
	//\Doctrine\DBAL\DBALException
}
elseif($accion == 'view'){

	$fact = find('TarifaServicio', $_POST['fc']);

	if ($_POST['tpo'] == 'day'){
		$header = "	<tr>
							<th>Dia Semana</th>
							<th>Quitar</th>
					</tr>";
		foreach ($fact->getDiasSemana() as $value) {
			$body.= "<tr>
						<td>$value</td>
						<td><input data-dia='".$value->getId()."' data-tarifa='".$fact->getId()."' type='button' value='Eliminar Dia' class='delDay'/></td>
					</tr>";
		}

		foreach (diasSemana() as $value) {
			$options.="<option value='".$value->getId()."'>$value</option>";
		}
		$addNew = "<select id='days'>$options</select><input type='button' value='Agregar Dia' class='addDay' data-tarifa='".$fact->getId()."' />";
		$title = "Dias cargados a la tarifa";
		$script = " $('#days').selectmenu({width: 150});
					$('.delDay').button().click(function(){
															var data = $(this);
															var t = data.data('tarifa');
															var d = data.data('dia');
															$.post('/vista/servicios/addtrfNew.php',
																  {accion: 'delOptionTarifa', delAct: 'day', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });
															});
					$('.addDay').button().click(function(){
															var trfa = $(this).data('tarifa');
															var dia = $('#days').val();
															$.post('/vista/servicios/addtrfNew.php',
																  {accion: 'addOptionTf', addAct: 'day', ts: trfa, param: dia},
																  function(res){
																  				var response = JSON.parse(res);
																  				if (response.status){
																  					$('#deta').dialog('close');
																  				}
																  				else{
																  					alert(response.message);
																  				}
																  });
															});
					";
	

	}
	elseif ($_POST['tpo'] == 'cron'){
		$title = "Servicios cargados a la tarifa";
		$header = "	<tr>
							<th>Servicio</th>
							<th>Origen</th>
							<th>Destino</th>
							<th>Quitar</th>
					</tr>";
		foreach ($fact->getCronogramas() as $value) {
			$body.= "<tr>
						<td>$value</td>
						<td>".$value->getOrigen()."</td>
						<td>".$value->getDestino()."</td>
						<td><input type='button' class='delcron' value='Eliminar Servicio' data-crono='".$value->getId()."' data-tarifa='".$fact->getId()."'/></td>
					</tr>";
		}

		$cronos = listadoCronogramas($fact->getFacturacion()->getCliente()->getId());
		foreach ($cronos as $crono) {
			$bodyAdd.= "<tr>
						<td>$crono</td>
						<td>".$crono->getOrigen()."</td>
						<td>".$crono->getDestino()."</td>
						<td><input type='button' value='Agregar Servicio' data-fact='".$fact->getId()."' data-crono='".$crono->getId()."'/></td>
					</tr>";
		}		

		$addNew="<br><fieldset class='ui-widget ui-widget-content ui-corner-all'>
					<legend class='ui-widget ui-widget-header ui-corner-all'>Agregar Servicios</legend>
					<table id='crnAdd' class='table table-zebra'>
						<thead>
							<tr>
								<th>Servicio</th>
								<th>Origen</th>
								<th>Destino</th>
								<th>Accion</th>
							</tr>
						</thead>
						<tbody>
							$bodyAdd
						</tbody>
					</table>
				</fieldset>";		

		$script = "	$('.delcron').button().click(function(){
															var obj = $(this);
															var t = obj.data('tarifa');
															var d = obj.data('crono');
															$.post('/vista/servicios/addtrfNew.php',
																  {accion: 'delOptionTarifa', delAct: 'crono', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });															
															});
					$('#crnAdd :button').button().click(function(){
																	var crono = $(this).data('crono');
																	var fact = $(this).data('fact');
																	$.post('/vista/servicios/addtrfNew.php',
																		  {accion: 'addOptionTf', addAct: 'crono', ts: fact, param: crono},
																		  function(res){
																		  				var response = JSON.parse(res);
																		  				if (!response.status){												  				
																		  					alert(response.message);
																		  				}
																  	});
					});
					$('#crnAdd').dataTable({'scrollY':200,
        									'scrollCollapse': true,
        									'jQueryUI': true,
        									 'orderClasses': false,
        									 'paging': false
    									  });
					$('#days').selectmenu({width: 150});
					$('.delDay').button().click(function(){
															var data = $(this);
															var t = data.data('tarifa');
															var d = data.data('dia');
															$.post('/vista/servicios/addtrfNew.php',
																  {accion: 'delOptionTarifa', delAct: 'day', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });
															});
					$('.addDay').button().click(function(){
															var trfa = $(this).data('tarifa');
															var dia = $('#days').val();
															$.post('/vista/servicios/addtrfNew.php',
																  {accion: 'addOptionTf', addAct: 'day', ts: trfa, param: dia},
																  function(res){
																  				var response = JSON.parse(res);
																  				if (response.status){
																  					$('#deta').dialog('close');
																  				}
																  				else{
																  					alert(response.message);
																  				}
																  });
															});
					";

	}
	elseif ($_POST['tpo'] == 'type'){
		$tipos = listaTiposVehiculos($_SESSION['structure'], 0);
		$types = array();
		foreach ($tipos as $tipo) {
			$types[$tipo->getId()] = $tipo->getTipo();
		}
		$title = "Tarifas cargadas";
		$header = "	<tr>
							<th>Tipo Unidad</th>
							<th>Importe</th>
							<th>Articulo</th>
							<th>Presupuestada</th>
							<th>Si/No</th>
					</tr>";
		foreach ($fact->getTarifasTipoVehiculo() as $value) {
			$body.= "<tr>
						<td>".$types[$value->getTipo()->getId()]."</td>
						<td>".$value->getImporte()."</td>
						<td>".$value->getArticulo()."</td>
						<td><input type='checkbox' name='defecto' ".($value->getDefecto()?'checked':'')." value='".$value->getId()."'></td>
						<td><input type='button' value='Eliminar Tarifa' data-tf='".$value->getId()."' data-fact='".$fact->getId()."'/></td>
					</tr>";
		}
		$optionsV = listaTiposVehiculos($_SESSION['structure']);
		$articulos = articulosClientesOption($fact->getFacturacion()->getCliente()->getId());
		$addNew = "<br><fieldset class='ui-widget ui-widget-content ui-corner-all'>
						<legend class='ui-widget ui-widget-header ui-corner-all'>Agregar Nueva Tarifa</legend>
						<form id='formNT'>
							<table class='table table-zebra' id='tableNT' width='100%'>
								<thead>
									<tr>
										<th>Tipo Unidad</th>
										<th>Importe</th>
										<th>Articulo</th>
										<th>Accion</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><select id='typeNT' name='typeNT'>$optionsV</select></td>
										<td><input type='text' name='montoNT'/></td>
										<td><select name='artNT'>$articulos</select></td>
										<td><input type='button' id='saveNT' value='Guardar Tarifa'/></td>
									</tr>
								</tbody>
							</table>
							<input type='hidden' name='ts' value='".$fact->getId()."'/>
							<input type='hidden' name='addAct' value='type'/>
							<input type='hidden' name='accion' value='addOptionTf'/>
						</form>
					</legend>";

		$script = " $(\"input[name='defecto']\").click(function(){
																	var ch;
																	if ($(this).is(':checked'))
																		ch = 1;
																	else
																		ch = 0;
																	var val = $(this).val();
																	$.post('/vista/servicios/addtrfNew.php',
																  		   {accion: 'setDefault', sn: ch, id:val},
																  			function(res){
																  				
																  				alert(res);
																  			}
																  	);																		
																	});
					$('#tableNT select').selectmenu({width: 180});
					$('#tableVal :button').button().click(function(){
																	var b = $(this);
																	var fact = b.data('fact');
																	var tf = b.data('tf');
																	$.post('/vista/servicios/addtrfNew.php',
																  		   {accion: 'delOptionTarifa', delAct: 'tpo', ts: fact, tpo: tf},
																  			function(res){
																  				var response = JSON.parse(res);
																  				alert(res);
																  			}
																  	);																	
																	

																	});
					$('#tableNT :button').button().click(function(){	
																	var datos = $('#formNT').serialize();
																	alert(datos);
																	$.post('/vista/servicios/addtrfNew.php',
																  		   datos,
																  			function(res){
																  				var response = JSON.parse(res);
																  				alert(res);
																  			}
																  	);
					});";

	}

	$tabla ="<fieldset class='ui-widget ui-widget-content ui-corner-all'>
			 <legend class='ui-widget  ui-widget-header ui-corner-all'>$title</legend>	
				<table class='table table-zebra' id='tableVal' width='100%'>
					<thead>
						$header
					</thead>
					<tbody>
						$body
					</tbody>
				</table>
				$addNew
			<script>
				$('#tableVal').dataTable({'scrollY':200,
        									'scrollCollapse': true,
        									'jQueryUI': true,
        									 'orderClasses': false,
        									 'paging': false
    									  });
				$script
			</script>";		
	print $tabla;
}
elseif($accion == 'setDefault'){
	$entityManager->getConnection()->beginTransaction();
	try {
		$tarifa = find('TarifaTipoServicio', $_POST['id']);
		print($tarifa->getId());
		exit();
		if ($_POST['sn']){
			$tarifa->setDefecto(true);
		}
		else
			$tarifa->setDefecto(false);
		$entityManager->flush();
	    $entityManager->getConnection()->commit();
	} catch (Exception $e) {
	    $entityManager->getConnection()->rollback();
	    $entityManager->close();
	    print 'error:   '.$e->getMessage();
	}
}
elseif($accion == 'ldart'){
		$articulos = articulosCliente($_POST['cli']);
		foreach ($articulos as $articulo) {
			try{
			$bodyAdd.= "<tr>
						<td>".$articulo->getDescripcion()."</td>
						<td><input type='text' value='".$articulo->getImporte()."' id='data-".$articulo->getId()."'/></td>
						<td><input type='button' value='Guardar Precio' data-id='".$articulo->getId()."'/></td>
					</tr>";
				}catch (Exception $e){
										print $e->getMessage();
										exit();
				};
		}		

		$addNew="<table id='crnAdd' class='table table-zebra'>
						<thead>
							<tr>
								<th>Descripcion</th>
								<th>Importe</th>
								<th>Accion</th>
							</tr>
						</thead>
						<tbody>
							$bodyAdd
						</tbody>
					</table>
					<script>
						$('#crnAdd :button').button().click(function(){
																	var id = $(this).data('id');
																	var precio = $('#data-'+id).val();
																	$.post('/modelo/servicios/valcro.php',
																		 {accion:'setPcie', art: id, pcie: precio},
																		 function(data){
																		 				var response = $.parseJSON(data);
																		 				if (!response.ok)
																		 					alert('Error al procesar la accion');
																		 				else
																		 					alert('Accion realizada exitosamente');
																		 });
													

							});

					</script>";	
		print $addNew;	
}