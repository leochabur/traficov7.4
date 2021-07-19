<?
  header("Content-Disposition: attachment; filename=\"prox_vtos.xls\"");
  header("Content-Type: application/vnd.ms-excel");
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_GET['accion'];

  if($accion == 'pxday'){
          $emp = '';
          if ($_GET['emp']){
             $emp = "and (e.id_empleado = $_GET[emp])";
          }
          $tipo = '';
          if ($_GET['tipo']){
             $tipo = "and (id = $_GET[tipo])";
          }
          $sql="select legajo, upper(apenom), upper(licencia), fecha_alta, date_format(vto, '%d/%m/%Y') as vto
                from(
                     SELECT legajo, concat(apellido,', ', nombre) as apenom, licencia, max(vigencia_hasta) as vto, lc.fecha_alta, l.id
                     FROM licenciaconductor lc
                     inner join licencias l on l.id = lc.id_licencia
                     inner join empleados e on e.id_empleado = lc.id_conductor
                     where (e.activo) $emp and (lc.id_licencia in (SELECT id_licencia FROM licenciasxconductor where id_conductor = e.id_empleado))
                     group by id_conductor, id_licencia) o
                where (datediff(vto, date(now())) < $_GET[dias]) $tipo";
          //die($sql);
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse los proximos $_GET[dias] dias</legend>
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
                  </table>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'rafec'){
          $desde = dateToMysql($_GET['desde'], "/");
          $hasta = dateToMysql($_GET['hasta'], "/");
          $emp = '';
          if ($_GET['emp']){
             $emp = "and (e.id_empleado = $_GET[emp])";
          }
          $tipo = '';
          if ($_GET['tipo']){
             $tipo = "and (id = $_GET[tipo])";
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
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Vencimientos a cumplirse entre el $_GET[desde] y el $_GET[hasta]</legend>
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
                  </fieldset>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
?>

