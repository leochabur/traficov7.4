<?php
    session_start();
    define(RAIZ, '/');
    include ('../../controlador/bdadmin.php');
    include_once ('../../controlador/ejecutar_sql.php');

    $accion = $_POST['accion'];
     
    if ($accion == 'ld'){
        $sql = "
				SELECT b.id, upper(nombre), interno
				FROM baseoperativaunidades b
				inner join unidades u on u.id = b.id_unidad
				inner join baseoperativaxestructura bo on bo.id = b.id_baseoperativa
				order by nombre, interno";

		$body = "";

		$result = ejecutarSQL($sql);
		while ($row = mysql_fetch_array($result)){
			$body.="<tr>
						<td>$row[1]</td>
						<td>$row[2]</td>
						<td><form><input type='button' value='Quitar Unidad'/>
								  <input type='hidden' name='base' value='$row[0]'>
								  <input type='hidden' name='accion' value='delass'>
							</form>
						</td>
					</tr>";
		}

    	$tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Asignar Unidades a Base</legend>
		                 <table class="table table-zebra" id="unas">
		                 	<thead>
		                 		<tr>
		                 			<th>Base Operativa</th>
		                 			<th>Interno</th>
		                 			<th>Quitar</th>
		                 		</tr>
		                 	</thead>
		                 	<tbdody>
		                 		'.$body.'
		                 	</tbdody>
		                 </table>
		          </fieldset>';
		$script = "<script>
							$('#unas tbody :button').button().click(function(){
																				var id = $(this);
																				var dta = id.parent().serialize();
																				$.post('/modelo/servicios/addintbse.php',
																						dta,
																						function(data){
									                                                                    var res = JSON.parse(data);
									                                                                    if (res.status){
									                                                                    	id.parent().parent().parent().remove();
									                                                                    }
									                                                                    else{
									                                                                  
									                                                                    }											
																						});
																				});
					</script>";

    	print $tabla.$script;
    }
    elseif ($accion == 'ass'){
    	$sql = "INSERT INTO baseoperativaunidades (id_baseoperativa, id_unidad) VALUES ($_POST[bse], $_POST[int])";
		try{
			$result = ejecutarSQL($sql);    	
			print json_encode(array('status' => true));
		}catch (Exception $e){
			print json_encode(array('status' => false));
		}
    }
    elseif ($accion == 'delass'){
    	$sql = "DELETE FROM baseoperativaunidades WHERE id = $_POST[base]";
		try{
			$result = ejecutarSQL($sql);    	
			print json_encode(array('status' => true));
		}catch (Exception $e){
			print json_encode(array('status' => false));
		}    	
    }    
?>