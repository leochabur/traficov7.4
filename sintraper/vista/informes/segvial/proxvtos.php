<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
$query = "select interno, fechav, tipo, dias
          from (
               SELECT interno, date_format(max(vencimiento), '%d/%m/%Y') as fechav, upper(tv.nombre) as tipo,  DATEDIFF(max(vencimiento), date(now())) as dias, max(vencimiento) as vto
               FROM unidades u
               inner join tipovencimientoporinterno tvi on tvi.idunidad = u.id
               inner join tipovencimiento tv on tv.id = tvi.idtipovencimiento
               inner join vtosinternos vi on (vi.id_micro = u.id) and (vi.id_tipovtv = tvi.idtipovencimiento)
               where (u.activo)
               group by tvi.idunidad, tvi.idtipovencimiento) u
          where  (DATEDIFF(vto, date(now())) < 16)
          order by vto asc";
$result= ejecutarSQL($query);
if (mysql_num_rows($result) > 0){
$div='<font color="#800000"><b><i><u>Proximas Vencimientos de Habilitaciones de Unidades</u></i></b></font><BR><br>
<table border="1" width="75%" id="tablita" align="center" class="ui-widget ui-widget-content">
             <tr class="ui-widget-header ">
                 <th>Interno</th>
                 <th>Habilitacion</th>
                 <th>Vencimiento</th>
             </tr>';
$color = '#C2D2D2';
while ($data = mysql_fetch_array($result)){
      $color = ($color == '#C2D2D2')?'#D0D0D0':'#C2D2D2';
      $text = ($data['dias'] < 1)?'<font color="#FF0000"><b>'.$data['fechav'].'</b></font>':$data['fechav'];
      
      $div.="<tr bgcolor=\"$color\">
                 <td align='right'>$data[interno]</td>
                 <td>$data[2]</td>
                 <td align='right'>$text</td>
             </tr>";
}
$div.='</table>';
print $div;
}
?>
