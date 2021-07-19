<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'pxday'){
          $emp = '';
          if ($_POST['emp']){
             $emp = "and (e.id_empleado = $_POST[emp])";
          }
          $tipo = '';
          if ($_POST['tipo']){
             $tipo = "and (id = $_POST[tipo])";
          }
          $sql="select legajo, upper(apenom), upper(licencia), fecha_alta, date_format(vto, '%d/%m/%Y') as vto
                from(
                     SELECT legajo, concat(apellido,', ', nombre) as apenom, licencia, max(vigencia_hasta) as vto, lc.fecha_alta, l.id
                     FROM licenciaconductor lc
                     inner join licencias l on l.id = lc.id_licencia
                     inner join empleados e on e.id_empleado = lc.id_conductor
                     where (e.activo) $emp and (lc.id_licencia in (SELECT id_licencia FROM licenciasxconductor where id_conductor = e.id_empleado))
                     group by id_conductor, id_licencia) o
                where (datediff(vto, date(now())) < $_POST[dias]) $tipo";
          //die($sql);
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                   <a href='/modelo/rrhh/vtoempexp.php?accion=$accion&emp=$_POST[emp]&tipo=$_POST[tipo]&dias=$_POST[dias]'><img src='../../vista/excel.jpg' width='45' height='45' border='0'></a>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse los proximos $_POST[dias] dias</legend>
                    <table id='example'>
                     <thead>
            	            <tr>
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Tipo Vencimiento</th>
                                <th>Fecha Vencimiento</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[0]</td>
                                   <td>$data[1]</td>
                                   <td>$data[2]</td>
                                   <td>$data[4]</td>
                                   <td></td>
                                   <td>$data[3]</td>
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
					                                    "sScrollY": "300px",
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
          $emp = '';
          if ($_POST['emp']){
             $emp = "and (e.id_empleado = $_POST[emp])";
          }
          $tipo = '';
          if ($_POST['tipo']){
             $tipo = "and (id = $_POST[tipo])";
          }
          $sql="select legajo, upper(apenom), upper(licencia), fecha_alta, date_format(vto, '%d/%m/%Y') as vtos
                from(
                     SELECT legajo, concat(apellido,', ', nombre) as apenom, licencia, max(vigencia_hasta) as vto, lc.fecha_alta, l.id
                     FROM licenciaconductor lc
                     inner join licencias l on l.id = lc.id_licencia
                     inner join empleados e on e.id_empleado = lc.id_conductor
                     where (e.activo) $emp and (lc.id_licencia in (SELECT id_licencia FROM licenciasxconductor where id_conductor = e.id_empleado))
                     group by id_conductor, id_licencia) o
                where (vto between '$desde' and '$hasta') $tipo
                order by vto desc";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse entre el $_POST[desde] y el $_POST[hasta]</legend>
                     <a href='/modelo/rrhh/vtoempexp.php?accion=$accion&emp=$_POST[emp]&tipo=$_POST[tipo]&desde=$_POST[desde]&hasta=$_POST[hasta]'><img src='../../vista/excel.jpg' width='45' height='45' border='0'></a>
                    <table id='example'>
                     <thead>
            	            <tr>
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Tipo Vencimiento</th>
                                <th>Fecha Vencimiento</th>
                                <th>Usuario</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[0]</td>
                                   <td>$data[1]</td>
                                   <td>$data[2]</td>
                                   <td>$data[4]</td>
                                   <td></td>
                                   <td>$data[3]</td>
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

