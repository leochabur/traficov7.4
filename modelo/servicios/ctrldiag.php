<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
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
              where (c.id_estructura = $_SESSION[structure]) and (c.activo)
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
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</tbody></table>
                  <style type="text/css">

                         #example tbody tr.even:hover,
                         #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover,
                         #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}

                  </style>
                  <script>
                          		$("#example").dataTable({
					                                    "sScrollY": "150px",
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
                                                                  $.post("/modelo/servicios/ctrldiag.php",{crono: id_cron, nomcron: n_cron, accion: "lsrv", ctrl:$("#perfiles").val()},function(data){
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
                    WHERE (s.id_estructura = $_SESSION[structure]) and
                          ((s.id_cronograma = $crono) and (s.id_estructura_cronograma = $_SESSION[structure]))
                          and (s.id not in (SELECT id_servicio FROM servicioscontroldiagrama where id_controlDiagrama = $_POST[ctrl]))
                          and (s.activo)
                    order by hcitacion";
            $conn = conexcion();
            $result = mysql_query($sql, $conn) or die (mysql_error($conn));
            $tabla = '<br>Servicios correspondientes al cronograma: '.$_POST['nomcron'].'<br>
                      <table id="srvlist" class="ui-widget ui-widget-content">
	                      <thead>
		                         <tr>
                                     <th>H. Citacion</th>
			                         <th>H. Salida</th>
			                         <th>H. Llegada</th>
			                         <th>H. Fin</th>
			                         <th>Tipo Servicios</th>
			                         <th>Turno Servicio</th>
                                     <th>Agregar</th>
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
                              <td><input type='button' id='servi$data[id]' value='Agregar' onClick='modSrv(this.value, $data[id]);'></td>
                       </tr>";
            }
            mysql_free_result($result);
            mysql_close($conn);
            $tabla.= '</table>
                  <style type="text/css">
                         #srvlist tbody tr.even:hover,
                         #srvlist tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #srvlist tbody tr.odd:hover,
                         #srvlist tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #srvlist tr.even:hover {background-color: #ECFFB3;}

                         #srvlist tr.odd:hover {background-color: #E6FF99;}

                  </style>
                  <script>
                          		$("#srvlist").dataTable({
					                                    "sScrollY": "100px",
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
                                $("#srvlist tr").dblclick(function(){alert($(this).attr("id"));});

                                function modSrv(label, servi){
                                     $.post("../../modelo/servicios/ctrldiag.php", {ctrl:$("#perfiles").val(), srv:servi, accion:"addsrvctrl"},function(data){
                                                                                                                                                              var response = $.parseJSON(data);
                                                                                                                                                              if (response){
                                                                                                                                                                 $("#"+servi).remove();
                                                                                                                                                                 var ctl = $("#perfiles").val();
                                                                                                                                                                 $("#srvctrl").html("<div align=\'center\'><img  alt=\'cargando\' src=\'../ajax-loader.gif\'/></div>");
                                                                                                                                                                 $.post("/modelo/servicios/ctrldiag.php",{ctrl: ctl, accion: "lsrvctrl"},function(data){
                                                                                                                                                                                                                                                        $("#srvctrl").html(data);
                                                                                                                                                                                                                                                        });
                                                                                                                                                              }
                                                                                                                                                              });
                                }
                  </script>';
            print $tabla;

     }
     elseif ($accion == 'lsrvctrl'){
     
$sql="SELECT scd.id,
      upper(razon_social) as cliente,
      upper(c.nombre) as nombre,
      upper(o.ciudad) as origen,
      upper(d.ciudad) as destino,
      date_format(hcitacion, '%H:%i') as citacion,
      date_format(hsalida, '%H:%i') as salida,
      date_format(hllegada, '%H:%i') as llegada,
      date_format(hfinserv, '%H:%i') as hfin,
      upper(tipo) as tipo,
      upper(turno) as turno
FROM cronogramas c
inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
inner join clientes cli on (cli.id = c.id_cliente) and (cli.id_estructura = c.id_estructura_cliente)
inner join ciudades o on (o.id = c.ciudades_id_origen) and (o.id_estructura = c.ciudades_id_estructura_origen)
inner join ciudades d on (d.id = c.ciudades_id_destino) and (d.id_estructura = c.ciudades_id_estructura_destino)
inner join claseservicio cs on (cs.id = c.claseServicio_id) and (cs.id_estructura = c.claseServicio_id_estructura)
inner join turnos t on (t.id = s.id_turno) and (t.id_estructura = s.id_estructura_turno)
inner join tiposervicio ts on (ts.id = s.id_TipoServicio) and (ts.id_estructura = s.id_estructura_TipoServicio)
inner join servicioscontroldiagrama scd on scd.id_servicio = s.id and scd.id_estructuraServicio = s.id_estructura
WHERE (s.id_estructura = $_SESSION[structure]) and (scd.id_controlDiagrama = $_POST[ctrl])
order by hcitacion";

 $conn = conexcion();

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla = '<table id="examples" class="ui-widget ui-widget-content">
	                      <thead>
		                         <tr>
                                     <th>Cliente</th>
                                     <th>Servicio</th>
			                         <th>Destino</th>
			                         <th>H. Salida</th>
			                         <th>H. Llegada</th>
			                         <th>Tipo Servicios</th>
			                         <th>Turno Servicio</th>
                                     <th>Quitar</th>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr id='scd-$data[id]'>
                              <td>$data[cliente]</td>
                              <td>".htmlentities($data['nombre'])."</td>
                              <td>".htmlentities($data['destino'])."</td>
                              <td>".($data['salida'])."</td>
                              <td>".($data['llegada'])."</td>
                              <td>$data[tipo]</td>
                              <td>$data[turno]</td>
                              <td><input type='button' value='Quitar' onClick='quitar($data[id]);'></td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</tbody></table>
                  <style type="text/css">

                         #examples tbody tr.even:hover,
                         #examples tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #examples tbody tr.odd:hover,
                         #examples tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #examples tr.even:hover {background-color: #ECFFB3;}
                         #examples thead { font-size: 70%; }
                         #examples { font-size: 80%; }

                  </style>
                  <script>
                         function quitar(scd){
                                  $.post("../../modelo/servicios/ctrldiag.php", {ctrl:scd, accion:"delsrvctrl"},function(data){
                                                                                                                                var response = $.parseJSON(data);
                                                                                                                                if (response){
                                                                                                                                   $("#scd-"+scd).remove();
                                                                                                                                }
                                                                                                                                });
                         }
                          		$("#examples").dataTable({
					                                    "sScrollY": "600px",
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
                  </script>';
        print $tabla;


}
elseif ($accion == 'addsrvctrl'){
       try{
           $id = insert("servicioscontroldiagrama", "id, id_servicio, id_estructuraServicio, id_controlDiagrama",
                        "$_POST[srv], $_SESSION[structure], $_POST[ctrl]");
           print (json_encode($id));
       } catch (Exception $e) {
                               print (json_encode(0));
                              }
}
elseif ($accion == 'delsrvctrl'){
       try{
            delete("servicioscontroldiagrama", "id", "$_POST[ctrl]");
           print (json_encode(1));
       } catch (Exception $e) {
                               print (json_encode(0));
                              }
}
?>

