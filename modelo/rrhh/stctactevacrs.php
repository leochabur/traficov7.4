<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'load'){
          $conn = conexcion();
          $where="";
          if ($_POST['det'] != 99)
             $where = "WHERE anio = $_POST[det]";
          $result = mysql_query("SELECT anio, detalle FROM vacacionespersonal $where group by anio order by detalle", $conn);
          $detalle=array();
          $i=0;
          while ($data = mysql_fetch_array($result)){
                $campos.=", (SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal v where v.id_empleado = e.id_empleado and anio = $data[0])";
                $detalle[$i++]=$data[0];
          }
          
          $joinSector = $whereSector = $campoSector = "";
          if ($_POST["sector"] != 99)
          { 
                $joinSector = " INNER JOIN cargo cgo ON cgo.id = e.id_cargo AND cgo.id_estructura = e.id_estructura_cargo
                              INNER JOIN sector sec ON sec.id = cgo.id_sector ";
                $whereSector = " AND sec.id = $_POST[sector]";
                $campoSector = ", cgo.descripcion as puesto, sec.descripcion as sector";
          }

          $sql = "SELECT legajo, 
                         upper(concat(apellido,', ',nombre)) as apenom, 
                         date_format(inicio_relacion_laboral, '%d/%m/%Y')$campos, 
                         (SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal where id_empleado = e.id_empleado)
                          -
                         (SELECT if(DATEDIFF(hasta, desde) is null, 0, sum(DATEDIFF(hasta, desde) + 1))
                          FROM novedades n
                          where id_novedad = 19 and (desde > '2014-06-30') and activa and id_empleado = e.id_empleado) $campoSector
                  FROM empleados e
                  $joinSector
                  WHERE (e.activo) and (not borrado) and (id_empleador = 1) $whereSector
                  order by apellido";

           //die($sql);
          $result = mysql_query($sql, $conn);
          $tabla ='<a href="/modelo/rrhh/stctactevacrsexp.php?det='.$_POST['det'].'&sc='.$_POST['sector'].'"><img src="../../vista/excel.jpg" width="35" height="35" border="0"></a><table id="tablitasssss" align="center" width="70%" class="ui-widget ui-widget-content">
                     <thead>
                            <tr class="ui-widget-header">
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>';
          if ($_POST["sector"] != 99)
          {
                $tabla.="<th>Puesto</th>
                        <th>Sector</th>";
          }
                     for($i=0;$i < count($detalle);$i++)
                         if ($detalle[$i]==0)
                                $tabla.="<th>S. Inicial</th>";
                         else
                                $tabla.="<th>$detalle[$i]</th>";
                     $tabla.='<th>Total Adeudado</th></tr>
                     </thead>
                     <tbody>';
          $j=0;
          while ($data = mysql_fetch_array($result)){
                $color = (($j++%2)==0) ? "#D3D3D3" : "#F3F3F3";
                $tabla.="<tr bgcolor='$color'>
                             <td>$data[0]</td>
                             <td align='left'>$data[1]</td>";
              if ($_POST["sector"] != 99)
              {
                    $tabla.="<td>$data[puesto]</td>
                            <td>$data[sector]</td>";
              }
                for($i=0;$i < count($detalle);$i++){
                            $aux=$i+3;
                            $tabla.="<td align='right'>$data[$aux]</td>";
                }
                $aux=$i+3;
                $tabla.="<td align='right'>$data[$aux]</td></tr>";
          }
          $tabla.="</tbody>
                   </table>
                  <style>
                         #tablitasssss { font-size: 85%; }
                         #tablitasssss tbody tr:hover {
                                                 background-color: #FF8080;
                                                 }
                  </style>                   ";
          print $tabla;
  }
?>

