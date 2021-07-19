<?php
     session_start();
     define(RAIZ, '/');
     include ('../../controlador/bdadmin.php');
     include_once ('../../controlador/ejecutar_sql.php');

     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){
        $conn = conexcion();
        $cliente = $_POST['cli'];
        $sql="SELECT c.id as id_crono, upper(nombre) as nombre, upper(o.ciudad) as origen , upper(d.ciudad) as destino, km, date_format(tiempo_viaje, '%H:%i') as tiempo_viaje, upper(cli.razon_social) as cliente, upper(clv.razon_social) as cliente_vacio, c.activo
              FROM cronogramas c
              inner join clientes cli on (cli.id = c.id_cliente) and (cli.id_estructura = c.id_estructura_cliente) and (cli.id = $cliente)
              inner join ciudades o on (o.id = c.ciudades_id_origen) and (o.id_estructura = c.ciudades_id_estructura_origen)
              inner join ciudades d on (d.id = c.ciudades_id_destino) and (d.id_estructura = c.ciudades_id_estructura_destino)
              inner join claseservicio cs on (cs.id = c.claseServicio_id) and (cs.id_estructura = c.claseServicio_id_estructura)
              left join clientes clv on (clv.id = c.id_cliente_vacio) and (clv.id_estructura = c.id_estructura_cliente_vacio)
              where (c.id_estructura = $_SESSION[structure])
              ORDER BY c.nombre";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla = '<br>Cronogramas<br><table id="example" class="ui-widget ui-widget-content">
	                      <thead>
		                         <tr>
                                     <th>Codigo</th>
			                         <th>Nombre Cronograma</th>
			                         <th>Origen</th>
			                         <th>Destino</th>
			                         <th>Km</th>
			                         <th>Tiempo Viaje</th>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr id='$data[id_crono]'>
                              <td>$data[id_crono]</td>
                              <td id='td$data[id_crono]'>".htmlentities($data['nombre'])."</td>
                              <td>".htmlentities($data['origen'])."</td>
                              <td>".htmlentities($data['destino'])."</td>
                              <td>$data[km]</td>
                              <td>$data[tiempo_viaje]</td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</tbody>
                  </table>
                  <input type="hidden" id="crono">
                  <style type="text/css">
                         #example { font-size: 75%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script>
                          		$("#example").dataTable({
					                                    "sScrollY": "200px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
				                                       });
                                $("#example tbody tr").click(function(){
                                                                  var id_cron = $(this).attr("id");
                                                                  var n_cron = $("#td"+id_cron).html();
                                                                  $("#crono").val(id_cron);
                                                                  $("#srvcron").html(\'<div align="center"><img  alt="cargando" src="../../vista/ajax-loader.gif" /></div>\');
                                                                  $.post("/modelo/servicios/srvpje.php",{crono: id_cron, nomcron: n_cron, accion: "lpje"},function(data){
                                                                                                                                                           $("#srvcron").html(data);
                                                                                                                                                         });
                                                                  });
                  </script>';
        print $tabla;
     }
     elseif ($accion == 'lpje'){
            $crono = $_POST['crono'];
            $conn = conexcion();
            $result = mysql_query("SELECT id, upper(concat(lugar, ' - ', nombre)) as estacion FROM estacionespeaje  order by lugar", $conn)or die ("error sql");;
            $tabla = '<br><fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Agregar peajes al Cronograma</legend>
                         <select id="peajes">';
            while ($data = mysql_fetch_array($result)){
                  $tabla.="<option value='$data[id]'>$data[estacion]</option>";
            }
            $tabla.= '</select>
                     <input type="button" id="svepjecrono" value="Agregar Peajes">
                     </fieldset>
                     <br>';
            $sql = "SELECT pxc.id, upper(concat(nombre, ' - ', lugar)) as lugar
                    FROM peajesporcronogramas pxc
                    inner join estacionespeaje ep on (ep.id = pxc.id_estacion_peaje) and (ep.id_estructura = pxc.id_estructura_estacion_peaje)
                    where (pxc.id_cronograma = $crono) and (pxc.id_estructura_cronograma = $_SESSION[structure])";
            $result = mysql_query($sql, $conn) or die ($sql);
            $tabla.= '<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Peajes asociados al cronograma: '.$_POST['nomcron'].'</legend>
                         <table id="servicios" class="ui-widget ui-widget-content" width="100%">
	                      <thead>
		                         <tr class="ui-widget-header">
                                     <th>Nombre - Lugar</th>
			                         <th>Quitar</th>
		                          </tr>
                          </thead>
                          <tbody>';
            while ($data = mysql_fetch_array($result)){
              $tabla.="<tr id='tr-$data[id]'>
                              <td>".htmlentities($data['lugar'])."</td>
                              <td><input text='$data[id]' type='button' value='Quitar'></td>
                       </tr>";
            }
            $tabla.= '</tbody>
                      </table>
                      <input type="hidden" id="id_cronog" name="id_cronog" value="'.$_POST['crono'].'">
                      <input type="hidden" id="nom_cronog" name="nom_cronog" value="'.$_POST['nomcron'].'">
                      </fieldset>';
            
            mysql_free_result($result);
            mysql_close($conn);
            $tabla.='
                  <style type="text/css">
                  </style>
                  <script>
                                $("#peajes").selectmenu({width: 550});
                                $("#svepjecrono").button().click(function(){
                                                                       var cron = $("#crono").val();
                                                                       var pje = $("#peajes").val();
                                                                       $("#srvcron").html(\'<div align="center"><img  alt="cargando" src="../../vista/ajax-loader.gif" /></div>\');
                                                                       $.post("/modelo/servicios/srvpje.php",{id: pje, cr: cron, accion: "addpje"}).done(function(){
                                                                                                                                                                    $.post("/modelo/servicios/srvpje.php",{crono: '.$crono.', nomcron: "'.$_POST['nomcron'].'", accion: "lpje"},function(data){
                                                                                                                                                                                                                                                                           $("#srvcron").html(data);
                                                                                                                                                                                                                                                                           });
                                                                                                                                                                    });
                                                                       });
                                $("#servicios tbody tr td :button").button().click(function(){
                                                                                              $(this).toggle();
                                                                                               var cron = $(this).attr("text");
                                                                                               $.post("/modelo/servicios/srvpje.php",{id:cron, accion: "delpje"}).done(function(){$("#tr-"+cron).remove();});
                                                                                              });
                  </script>';
            print $tabla;

     }
     elseif ($accion == 'delpje'){
            print json_encode(delete("peajesporcronogramas", "id", "$_POST[id]"));
     }
     elseif ($accion == 'addpje'){
            $ok = insert("peajesporcronogramas", "id, id_estacion_peaje, id_cronograma, id_estructura_estacion_peaje, id_estructura_cronograma", "$_POST[id], $_POST[cr], $_SESSION[structure], $_SESSION[structure]");
            print $ok;
     }
?>

