<?php
session_start();

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=vacaciones_personal.xls");

include ('../../controlador/bdadmin.php');


  $tabla="<html><body>";

          $conn = conexcion();
          $where="";
          if ($_GET['det'] != 99)
             $where = "WHERE anio = $_GET[det]";
          $result = mysql_query("SELECT anio, detalle FROM vacacionespersonal $where group by anio order by detalle", $conn);
          $detalle=array();
          $i=0;
          while ($data = mysql_fetch_array($result)){
                $campos.=", (SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal v where v.id_empleado = e.id_empleado and anio = $data[0])";
                $detalle[$i++]=$data[0];
          }
        
          $joinSector = $whereSector = $campoSector = "";
          if ($_GET["sc"] != 99)
          { 
                $joinSector = " INNER JOIN cargo cgo ON cgo.id = e.id_cargo AND cgo.id_estructura = e.id_estructura_cargo
                              INNER JOIN sector sec ON sec.id = cgo.id_sector ";
                $whereSector = " AND sec.id = $_GET[sc]";
                $campoSector = ", cgo.descripcion as puesto, sec.descripcion as sector";
          }

         /* $sql = "SELECT legajo, upper(concat(apellido,', ',nombre)) as apenom, date_format(inicio_relacion_laboral, '%d/%m/%Y')$campos, 
                         (SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) 
                          FROM vacacionespersonal where id_empleado = e.id_empleado)-
                          (SELECT if(DATEDIFF(hasta, desde) is null, 0, sum(DATEDIFF(hasta, desde) + 1))
                           FROM novedades n
                          where id_novedad = 19 and
                                (desde > '2014-06-30') and
                                activa and id_empleado = e.id_empleado)
                  FROM empleados e

                  WHERE (activo) and (not borrado) and (id_empleador = 1)
                  order by apellido";*/
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


          // die($sql);
          $result = mysql_query($sql, $conn);
          $tabla.='<table id="tablitasssss" align="center" width="100%">
                     <thead>
                            <tr class="ui-widget-header">
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>';
          if ($_GET["sc"] != 99)
          {
                $tabla.="<th>Puesto</th>
                        <th>Sector</th>";
          }
                     for($i=0;$i < count($detalle);$i++)
                         if ($detalle[$i]==0)
                                $tabla.="<th>S. Inicial</th>";
                         else
                                $tabla.="<th>$detalle[$i]</th>";
                     $tabla.='<th>Total Dias</th></tr>
                     </thead>
                     <tbody>';
          $j=0;
          while ($data = mysql_fetch_array($result)){
                $color = (($j++%2)==0) ? "#D3D3D3" : "#F3F3F3";
                $tabla.="<tr bgcolor='$color'>
                             <td>$data[0]</td>
                             <td align='left'>$data[1]</td>";
                  if ($_GET["sc"] != 99)
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
                   </table>";
          print $tabla;
  //}
?>

