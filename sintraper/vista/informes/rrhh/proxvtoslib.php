<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
$query = "select legajo, emple, fechav, tipo, dias
          from (
               SELECT legajo,upper(concat(apellido,', ',nombre)) as emple, date_format(max(vigencia_hasta), '%d/%m/%Y') as fechav, upper(l.licencia) as tipo,  DATEDIFF(max(vigencia_hasta), date(now())) as dias, max(vigencia_hasta) as vto, l.id
               FROM (select * from empleados where (id_cargo = 1) and (activo) and (not borrado))e
               inner join licenciasxconductor lxc on lxc.id_conductor = e.id_empleado
               inner join licencias l on l.id = lxc.id_licencia
               inner join licenciaconductor lc on (lc.id_conductor = e.id_empleado) and (lc.id_licencia = lxc.id_licencia)
               group by lxc.id_conductor, lxc.id_licencia) u
          where  (DATEDIFF(vto, date(now())) < 21) and (id in (6))
          order by vto asc";
$result= ejecutarSQL($query);
if (mysql_num_rows($result) > 0){
$vtvs='<font color="#800000"><b><i><u>Libretas Trabajo a vencer los proximos 20 dias</u></i></b></font><BR><br>
   <table border="1" id="tablita">
      <thead>
      <tr>
          <th>Legajo</th>
          <th>Conductor</th>
          <th>Tipo Vencimiento</th>
          <th>Vencimiento</th>
      </tr>
      </thead>
      <tbody>';
      $color = '#C2D2D2';
           while ($data = mysql_fetch_array($result)){
                 $vto = $data['fechav'];
                 if ($data['dias'] < 11)
                    $vto = "<font color='#FF8080'>$data[fechav]</font>";
                 elseif($data['dias'] < 1){
                    $vto = "<font color='#FF0000'><b>$data[fechav]</b></font>";
                 }
                 $color = ($color == '#C2D2D2')?'#D0D0D0':'#C2D2D2';
                 $vtvs.="<tr bgcolor=\"$color\">
                            <td>$data[0]</td>
                            <td>".htmlentities($data[1])."</td>
                            <td>$data[3]</td>
                            <td>$vto</td>
                        </tr>";
           }

      $vtvs.="</tbody>
   </table>";
}
print $vtvs;
?>
