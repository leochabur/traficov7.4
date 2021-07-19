<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'pxday'){
          if ($_POST['uda']){
             $coche = "and (id_micro = $_POST[uda])";
          }
          $sql="select *, date_format(venc, '%d/%m/%Y') as vto
                from(
                     SELECT upper(nombre) as vtv, max(v.vencimiento) as venc, upper(apenom) as usr, date_format(fechaAlta, '%d/%m/%Y - %H:%i') as fup, un.activo, interno, patente
                     FROM vtosinternos v
                     inner join tipovencimiento tv on tv.id = v.id_tipovtv
                     inner join usuarios u on u.id = usuarioAlta
                     inner join unidades un on un.id = v.id_micro
                     group by id_tipovtv, id_micro) u
                where (datediff(venc, date(now())) < $_POST[dias]) and (activo) $coche
                order by venc desc";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse los proximos $_POST[dias] dias</legend>
                    <table id='example'>
                     <thead>
            	            <tr>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Tipo Vencimiento</th>
                                <th>Fecha Vencimiento</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[interno]</td>
                                   <td>$data[patente]</td>
                                   <td>$data[vtv]</td>
                                   <td>$data[vto]</td>
                                   <td>$data[usr]</td>
                                   <td>$data[fup]</td>
                               </tr>";
          }
          $tabla.='</tbody>
                  </table>
                  </fieldset>
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
                  <script type="text/javascript">
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
                  </script>
                  ';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'rafec'){
          $desde = dateToMysql($_POST['desde'], "/");
          $hasta = dateToMysql($_POST['hasta'], "/");
          if ($_POST['uda']){
             $coche = "and (id_micro = $_POST[uda])";
          }
          $sql="select *, date_format(venc, '%d/%m/%Y') as vto
                from(
                     SELECT upper(nombre) as vtv, max(v.vencimiento) as venc, upper(apenom) as usr, date_format(fechaAlta, '%d/%m/%Y - %H:%i') as fup, un.activo, interno, patente
                     FROM vtosinternos v
                     inner join tipovencimiento tv on tv.id = v.id_tipovtv
                     inner join usuarios u on u.id = usuarioAlta
                     inner join unidades un on un.id = v.id_micro
                     group by id_tipovtv, id_micro) u
                where (venc between '$desde' and '$hasta') and (activo) $coche
                order by venc desc";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse entre el $_POST[desde] y el $_POST[hasta]</legend>
                    <table id='example'>
                     <thead>
            	            <tr>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Tipo Vencimiento</th>
                                <th>Fecha Vencimiento</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[interno]</td>
                                   <td>$data[patente]</td>
                                   <td>$data[vtv]</td>
                                   <td>$data[vto]</td>
                                   <td>$data[usr]</td>
                                   <td>$data[fup]</td>
                               </tr>";
          }
          $tabla.='</tbody>
                  </table>
                  </fieldset>
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
                  <script type="text/javascript">
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
                  </script>
                  ';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
?>

