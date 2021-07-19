<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
     include ('../../controlador/bdadmin.php');

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
			                         <th>Vacio afectado a:</th>
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
                              <td>$data[cliente_vacio]</td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</table>
                  <style type="text/css">
                         table { font-size: 75%; }
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
                                $("#example tr").click(function(){
                                                                  var id_cron = $(this).attr("id");
                                                                  var n_cron = $("#td"+id_cron).html();

                                                                  $("#srvcron").html(\'<div align="center"><img  alt="cargando" src="../../vista/ajax-loader.gif" /></div>\');
                                                                  $.post("/modelo/servicios/srvlist.php",{crono: id_cron, nomcron: n_cron, accion: "lsrv"},function(data){
                                                                                                                                                           $("#srvcron").html(data);
                                                                                                                                                         });
                                                                  });
                  </script>';
        print $tabla;
     }
     elseif ($accion == 'lsrv'){
            $crono = $_POST['crono'];
            $sql = "SELECT s.id, date_format(hcitacion, '%H:%i') as citacion, date_format(hsalida, '%H:%i') as salida, date_format(hllegada, '%H:%i') as llegada, date_format(hfinserv, '%H:%i') as hfin, upper(tipo) as tipo, upper(turno) as turno, if (i_v = 'i', 'IDA', 'VUELTA') as i_v, s.activo as activo
                    FROM servicios s
                    inner join turnos t on (t.id = s.id_turno) and (t.id_estructura = s.id_estructura_turno)
                    inner join tiposervicio ts on (ts.id = s.id_TipoServicio) and (ts.id_estructura = s.id_estructura_TipoServicio)
                    WHERE (s.id_estructura = $_SESSION[structure]) and ((s.id_cronograma = $crono) and (s.id_estructura_cronograma = $_SESSION[structure]))
                    order by hcitacion";
            $conn = conexcion();
            $result = mysql_query($sql, $conn) or die (mysql_error($conn));
            $tabla = '<br>Servicios correspondientes al cronograma: '.$_POST['nomcron'].'<br><table id="servicios" class="ui-widget ui-widget-content">
	                      <thead>
		                         <tr>
                                     <th>H. Citacion</th>
			                         <th>H. Salida</th>
			                         <th>H. Llegada</th>
			                         <th>H. Fin</th>
			                         <th>Tipo Servicios</th>
			                         <th>Turno Servicio</th>
		                          </tr>
                          </thead>
                          <tbody>';
            while ($data = mysql_fetch_array($result)){
              $tabla.="<tr id='$data[id]'>
                              <td>".($data['citacion'])."</td>
                              <td>".($data['salida'])."</td>
                              <td>".($data['llegada'])."</td>
                              <td>".($data['hfin'])."</td>
                              <td>".htmlentities($data['tipo'])."</td>
                              <td>".htmlentities($data['turno'])."</td>
                       </tr>";
            }
            mysql_free_result($result);
            mysql_close($conn);
            $tabla.= '</table>
                  <style type="text/css">
                         table { font-size: 75%; }
                         #servicios tbody tr.even:hover, #servicios tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #servicios tbody tr.odd:hover, #servicios tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #servicios tr.even:hover {background-color: #ECFFB3;}
                         #servicios tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #servicios tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #servicios tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #servicios tr.odd:hover {background-color: #E6FF99;}
                         #servicios tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #servicios tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #servicios tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script>
                          		$("#servicios").dataTable({
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
                                $("#servicios tr").dblclick(function(){alert($(this).attr("id"));});
                  </script>';
            print $tabla;

     }
?>

