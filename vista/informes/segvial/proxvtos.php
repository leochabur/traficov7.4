<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
$query = "select interno, fechav, tipo, dias, str, idInt
          from (
               SELECT interno, date_format(max(vencimiento), '%d/%m/%Y') as fechav, upper(tv.nombre) as tipo,  DATEDIFF(max(vencimiento), date(now())) as dias, 
               max(vencimiento) as vto, e.nombre as str, u.id as idInt
               FROM unidades u
               left join estructuras e on e.id = u.id_estructura
               inner join tipovencimientoporinterno tvi on tvi.idunidad = u.id
               inner join tipovencimiento tv on tv.id = tvi.idtipovencimiento
               inner join vtosinternos vi on (vi.id_micro = u.id) and (vi.id_tipovtv = tvi.idtipovencimiento)
               where (u.activo)
               group by tvi.idunidad, tvi.idtipovencimiento) u
          where  (DATEDIFF(vto, date(now())) < 26)
          order by vto asc";
$result= ejecutarSQL($query);
if (mysql_num_rows($result) > 0){
$div='<font color="#800000"><b><i><u>Proximas Vencimientos de Habilitaciones de Unidades</u></i></b></font><BR><br>
<table class="table table-zebra">
      <thead>
             <tr>
                 <th>Interno </th>
                 <th>Estructura</th>
                 <th>Habilitacion</th>
                 <th>Vencimiento</th>
             </tr>
      </thead>
      <tbody>';
$color = '#C2D2D2';
while ($data = mysql_fetch_array($result)){

      $text = ($data['dias'] < 1)?'<font color="#FF0000"><b>'.$data['fechav'].'</b></font>':$data['fechav'];
      
      $div.="<tr>
                 <td align='left'><a href='/vista/segvial/upvtouda.php?int=$data[idInt]' target='_blanck'>$data[interno]</a></td>
                 <td>$data[str]</td>
                 <td>$data[2]</td>
                 <td align='right'>$text</td>
             </tr>";
}
$div.='</tbody></table>';
print $div;
}
?>
