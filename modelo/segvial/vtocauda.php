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
          $sql="select interno, if((nueva_patente is null or nueva_patente = ''), patente, nueva_patente) as patente, compania, date_format(vigente_desde, '%d/%m/%Y') as desde, date_format(venc, '%d/%m/%Y') as hasta, usr, fup, id_micro
from(
    SELECT un.id as id_micro, upper(compania) as compania, upper(apenom) as usr, date_format(fecha_alta, '%d/%m/%Y - %H:%i') as fup, un.activo, interno, patente, nueva_patente, vigente_desde, vigente_hasta as venc
    FROM polizasSeguroCoches v
    inner join companiasAseguradoras ca on ca.id = v.id_companiaSeguro
    inner join usuarios u on u.id = usr_alta
    inner join unidades un on un.id = v.id_coche
    where activa) u
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
                                <th>Compañia Aseguradora</th>
                                <th>Vigente desde</th>
                                <th>Vigente hasta</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[interno]</td>
                                   <td>$data[patente]</td>
                                   <td>$data[compania]</td>
                                   <td>$data[desde]</td>
                                   <td>$data[hasta]</td>
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
          $sql="select interno, if((nueva_patente is null or nueva_patente = ''), patente, nueva_patente) as patente, compania, date_format(vigente_desde, '%d/%m/%Y') as desde, date_format(venc, '%d/%m/%Y') as hasta, usr, fup, id_micro
              from(
    SELECT un.id as id_micro, upper(compania) as compania, upper(apenom) as usr, date_format(fecha_alta, '%d/%m/%Y - %H:%i') as fup, un.activo, interno, patente, nueva_patente, vigente_desde, vigente_hasta as venc
    FROM polizasSeguroCoches v
    inner join companiasAseguradoras ca on ca.id = v.id_companiaSeguro
    inner join usuarios u on u.id = usr_alta
    inner join unidades un on un.id = v.id_coche
    where activa) u
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
                                <th>Compañia Aseguradora</th>
                                <th>Vigente desde</th>
                                <th>Vigente hasta</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[interno]</td>
                                   <td>$data[patente]</td>
                                   <td>$data[compania]</td>
                                   <td>$data[desde]</td>
                                   <td>$data[hasta]</td>
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

