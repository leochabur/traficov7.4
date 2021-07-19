<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
     include ('../../controlador/bdadmin.php');
     include('../../modelo/utils/dateutils.php');

     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){
               $fecha =  dateToMysql($_POST['fecha'],'/');
        $conn = conexcion();
        $cliente = $_POST['cli'];
        $sql="select s.id, nombre, km, hcitacion, hsalida, hllegada, if (sino, 'checked', '')
              from cronogramas c
              inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
              left join (select * from agendaDiagramas where fecha_diagrama = '$fecha') ad on ad.id_servicio = s.id and ad.id_estructura_servicio = s.id_estructura
              where (c.id_estructura = $_SESSION[structure]) and c.id_cliente  = $cliente
              ORDER BY c.nombre";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla = '<br>Servicios<br>
                    <table id="example" class="ui-widget ui-widget-content">
	                      <thead>
		                         <tr>
			                         <th>Nombre Cronograma</th>
			                         <th>Km</th>
                                     <th>H. Salida</th>
                                     <th>H. llegada</td>
                                     <th>SI/NO</td>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr>
                              <td>".htmlentities($data[1])."</td>
                              <td>$data[2]</td>
                              <td>$data[4]</td>
                              <td>$data[5]</td>
                              <td><input type='checkbox' id='$data[0]' $data[6]></td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</table>
                  <style type="text/css">
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
					                                    "sScrollY": "400px",
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
                                $("#example :checkbox").click(function(){
                                                                  var n_cron = $(this).attr("id");
                                                                  var sin = 0;
                                                                  if ($(this).is(":checked")){
                                                                     sin = 1;
                                                                  }
                                                                  $.post("/modelo/servicios/agsrv.php",{srv: n_cron, sn: sin, accion: "svdcn", fecha:$("#fecha").val()},function(data){

                                                                                                                                                         });
                                                                  });
                  </script>';
        print $tabla;
     }
     elseif($accion == 'svdcn'){
       $fecha =  dateToMysql($_POST['fecha'],'/');
       $sql = "INSERT INTO agendaDiagramas (fecha_diagrama, id_servicio, id_estructura_servicio, sino)
              VALUES ('$fecha', $_POST[srv], $_SESSION[structure], $_POST[sn]) ON DUPLICATE KEY UPDATE sino=$_POST[sn]";
       $conn = conexcion();
       mysql_query($sql, $conn) or die($sql);
       mysql_close($conn);
     }
?>

