<?php
session_start();
include_once('../../tmpDoctrine/controller.php'); 
use Symfony\Component\Validator\Validation;

$accion = $_POST['accion'];

if ($accion == 'addfact'){

	$fact = facturacionCliente($_POST['cli']);
	if ($fact){
		$idFact = $fact->getId();
		$selector = "$( \".ttff[value='".$fact->getTipoFacturacion()."']\" ).attr('checked',true);
					$('#montohe').val('".$fact->getImporteHExtra()."');
					$('#atc').show();";

		$tarifas = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                        <legend class="ui-widget ui-widget-header ui-corner-all">Configuracion servicios</legend>            
                          <table class="table table-zebra" >
							<thead>
								<tr>
									<th>Articulo</th>
									<th>Cant. Servicios</th>
									<th>Dias Semana</th>
									<th>Tarifas</th>					
								</tr>
							</thead>
							<tbody>';
		foreach ($fact->getTarifas() as $tarifa) {
			$tarifas.="<tr data-id='".$tarifa->getId()."'>
							<td>$tarifa</td>
							<td><a href='#' data-id='cron' class='viewDet'>".count($tarifa->getCronogramas())."</a></td>
							<td><a href='#' data-id='day' class='viewDet'>".count($tarifa->getDiasSemana())."</a></td>
							<td><a href='#' data-id='type' class='viewDet'>".count($tarifa->getTarifasTipoVehiculo())."</a></td>
						</tr>";
		}
		$tarifas.="</tbody>
				  </table>
				  </fieldset>";
		$scriptView = "$('.viewDet').click(function(event){
								event.preventDefault();	
								var fact = $(this).parent().parent().data('id');
								var type = $(this).data('id');

 								$('#deta').remove();
                                var dialog = $('<div id=\'deta\'></div>').appendTo('body');                                   
                                dialog.dialog({
                                                title: 'Detalle datos facturacion',
                                                width:750,                                       
                                                height:450,
                                                modal:true,
                                                autoOpen: false                         
                                                 });
                                dialog.load('/vista/servicios/addtrf.php',
                                             {accion: 'view', fc: fact, tpo: type},
                                             function (){ 
                                                         });
                                dialog.dialog('open');
						});";
	}
	else{
		$idFact = 0;
		$selector="$('#atc').hide();";
	}	


	$new = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
				<form id="nwfc">
                        <legend class="ui-widget ui-widget-header ui-corner-all">Parametros facturacion</legend>            
                          <table>
                                <tr valign="center">
                                    <td >
                                        Tipo Facturacion
                                    </td>
                                    <td>
                                        Importe Fijo<input type="radio" name="tipof" value="f" class="ttff">
                                        Factura por Tramo<input type="radio" name="tipof" value="t" class="ttff">
                                        Ambos<input type="radio" name="tipof" value="a" class="ttff">
                                    </td>
                                </tr>
                                <tr valign="center">
                                    <td >
                                        Calcula H. Extras
                                    </td>
                                    <td>
                                        Si<input type="radio" name="che" value="1">
                                        No<input type="radio" name="che" value="0">
                                    </td>
                                </tr>
                                <tr valign="center">
                                    <td >
                                        Importe Hora Extra
                                    </td>
                                    <td>
                                        <input type="text" name="montohe" id="montohe">
                                    </td>
                                </tr> 
                                <tr valign="center">
                                    <td colspan="2">
                                        <input type="button" value="Guardar/Modificar Facturacion Cliente" id="gmfc">
                                        <input type="button" value="Agregar Tarifa" id="atc">
                                    </td>
                                </tr>                                                                                                     
                         </table>
                         <input type="hidden" name="accion" value="nwfc">
                         <input type="hidden" name="fact" value="'.$idFact.'" id="idfactcli">
                    </form>
                    '.$tarifas.'
            </fieldset>';


     $script = "<script>
     						$selector
     						$('#gmfc').button().click(function(){
     															var data = $('#nwfc').serialize()+'&cliente=' + $('#clientes').val();					
     															alert(data);
     															$.post('/modelo/informes/cria/factcli.php',
     																	data,
     																	function(res){	
     																			$('#dats').html(res);
     																			$('#atc').show();
     																	});
     						});

     						$('#atc').button().click(function(){
     															$('#dats').html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
     															$.post('/vista/servicios/addtrf.php',
     																	{accion:'add', cli: $('#clientes').val()},
     																	function(res){	
     																			$('#dats').html(res);
     																	});     
     						});
     						$scriptView
     			</script>";
     $new.=$script;
     print $new;

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
		        <input type="text" id="nameTf" name="nameTf" class="ui-widget ui-widget-content ui-corner-all" />
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
																console.log(JSON.stringify(procesarTiposVehiculos()));
																var tarifaTipo = JSON.stringify(procesarTiposVehiculos());

																var diasSemana = JSON.stringify(procesarDias());

																var cronograma = JSON.stringify(procesarCronogramas());
																console.log(cronograma);
																var idf = $('#idfactcli').val();
																$.post('/modelo/informes/cria/factcli.php',
     																	{accion: 'nwtftv', nombre: $('#nameTf').val(), tarifas: tarifaTipo, dias: diasSemana, crono: cronograma, fact: idf},
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
															$.post('/vista/servicios/addtrf.php',
																	{accion: 'addfact', cli: $(this).val()},
																	function(data){
																		$('#facts').html(data);
																	});
						});
				</script>";


	print "<select id='clientes' class='ui-widget ui-widget-content  ui-corner-all'>$empty".$clioptions."</select>$script";  

}
elseif($accion == 'delOptionTarifa'){
	$validator = Validation::createValidatorBuilder()
                              ->addMethodMapping('loadValidatorMetadata')
                              ->getValidator();
	$fact = find('TarifaServicio', $_POST['ts']);

	$delAction = $_POST['delAct'];

	if ($delAction == 'day'){
		$dia = find('DiaSemana', $_POST['param']);
		$fact->removeDiasSemana($dia);
	}
	elseif ($delAction == 'crono'){
		$crono = find('Cronograma', $_POST['param']);
		$fact->removeCronograma($crono);
	}
	elseif($delAction == 'tpo'){
		$tarifa = find('TarifaTipoServicio', $_POST['tpo']);
		$fact->removeTarifasTipoVehiculo($tarifa);
		$entityManager->remove($tarifa);
	}


    $errors = $validator->validate($fact);      
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
    }		

	$entityManager->flush();
    $response = array('status' => true, 'message' => "Accion realizada con exito!!");
    print json_encode($response);
    exit();	
}
elseif($accion == 'addOptionTf'){

	$fact = find('TarifaServicio', $_POST['ts']);
	$addAction = $_POST['addAct'];	
	if ($addAction == 'day'){
		$dia = find('DiaSemana', $_POST['param']);
		$fact->addDiasSemana($dia);
	}
	elseif($addAction == 'type'){
		//$tarifaServicio = find('TarifaServicio', $_POST['tafa']);
		$tarifaTipo = new TarifaTipoServicio();
		$tipo = find('TipoVehiculo', $_POST['typeNT']);
		
		if ($_POST['artNT']){
			$articulo = find('ArticuloCliente', $_POST['artNT']);
			$tarifaTipo->setArticulo($articulo);
		}
		$tarifaTipo->setTipo($tipo);
		$tarifaTipo->setImporte($_POST['montoNT']);
		$tarifaTipo->setDefecto(false);
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

	try{
		$entityManager->flush();
    	$response = array('status' => true, 'message' => "Accion realizada con exito!!");
    	print json_encode($response);
    	exit();	
	}
	catch(\Doctrine\DBAL\DBALException $e) {
						          			  $response = array('status' => false, 'message' => 'No se ha podido realizar la accion solicitada 3.0!!');
									          print json_encode($response);
									          exit();
	}
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
															$.post('/vista/servicios/addtrf.php',
																  {accion: 'delOptionTarifa', delAct: 'day', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });
															});
					$('.addDay').button().click(function(){
															var trfa = $(this).data('tarifa');
															var dia = $('#days').val();
															$.post('/vista/servicios/addtrf.php',
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
															$.post('/vista/servicios/addtrf.php',
																  {accion: 'delOptionTarifa', delAct: 'crono', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });															
															});
					$('#crnAdd :button').button().click(function(){
																	var crono = $(this).data('crono');
																	var fact = $(this).data('fact');
																	$.post('/vista/servicios/addtrf.php',
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
															$.post('/vista/servicios/addtrf.php',
																  {accion: 'delOptionTarifa', delAct: 'day', ts: t, param: d},
																  function(res){
																  				var response = JSON.parse(res);
																  				alert(response.message);
																  });
															});
					$('.addDay').button().click(function(){
															var trfa = $(this).data('tarifa');
															var dia = $('#days').val();
															$.post('/vista/servicios/addtrf.php',
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
							<th>Si/No</th>
					</tr>";
		foreach ($fact->getTarifasTipoVehiculo() as $value) {
			$body.= "<tr>
						<td>".$types[$value->getTipo()->getId()]."</td>
						<td>".$value->getImporte()."</td>
						<td>".$value->getArticulo()."</td>
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

		$script = " $('#tableNT select').selectmenu({width: 180});
					$('#tableVal :button').button().click(function(){
																	var b = $(this);
																	var fact = b.data('fact');
																	var tf = b.data('tf');
																	$.post('/vista/servicios/addtrf.php',
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
																	$.post('/vista/servicios/addtrf.php',
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