<?php
session_start();
$str = "";
if ($_SESSION[lv] <= 3)
   $str="and id_estructura = $_SESSION[structure]";



include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');

$type = "licencias";
if (($_SESSION['modaf'] == 2) && ($_SESSION['structure'] == 1))
{
  $type = "(SELECT * FROM licencias where id in (1,3))";
}


$query = "select legajo, emple, fechav, tipo, dias, str
          from (
               SELECT legajo,upper(concat(apellido,', ',e.nombre)) as emple, date_format(max(vigencia_hasta), '%d/%m/%Y') as fechav, upper(l.licencia) as tipo,  DATEDIFF(max(vigencia_hasta), date(now())) as dias, max(vigencia_hasta) as vto, l.id, es.nombre as str
               FROM (select * from empleados where (id_cargo = 1) and (activo) and (not borrado) $str) e
               left join estructuras es on es.id = e.id_estructura
               inner join licenciasxconductor lxc on lxc.id_conductor = e.id_empleado
               inner join $type l on l.id = lxc.id_licencia
               inner join licenciaconductor lc on (lc.id_conductor = e.id_empleado) and (lc.id_licencia = lxc.id_licencia)
               group by lxc.id_conductor, lxc.id_licencia) u
          where  (DATEDIFF(vto, date(now())) < 60) and (id in (1,2,3))
          order by vto asc";
$result= ejecutarSQL($query);
if (mysql_num_rows($result) > 0){
$vtvs='<font color="#800000"><b><i><u>Licencias a vencer los proximos 60 dias</u></i></b></font><BR><br>
   <table class="table table-zebra" id="tablita">
      <thead>
      <tr>
          <th>Legajo</th>
          <th>Conductor</th>
          <th>Estructura</th>          
          <th>Licencia</th>
          <th>Vencimiento</th>
      </tr>
      </thead>
      <tbody>';
      $color = '#C2D2D2';
           while ($data = mysql_fetch_array($result)){
                 $vto = $data['fechav'];
                 if ($data['dias'] < 11)
                    $vto = "<font color='#FF0000'><br>$data[fechav]</br></font>";
                 elseif($data['dias'] < 1){
                    $vto = '<font color="#FF0000"><b>'.$data[fechav].'</b></font>';
                 }
                 //$color = ($color == '#C2D2D2')?'#D0D0D0':'#C2D2D2';
                 $vtvs.="<tr bgcolor=\"$color\">
                            <td>$data[0]</td>
                            <td>".htmlentities($data[1])."</td>
                            <td>".htmlentities($data[str])."</td>                            
                            <td>$data[3]</td>
                            <td>$vto</td>
                        </tr>";
           }

      $vtvs.="</tbody>
   </table>";
}
print $vtvs;
?>
