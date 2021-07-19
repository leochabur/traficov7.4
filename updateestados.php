<?php
     set_time_limit(0);
     $remoto = mysql_connect('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp');
     mysql_select_db('c0mbexport', $remoto);
     $sql = "select u.id, km
from ordenes o
inner join unidades u on u.id = o.id_micro
where not borrada and not suspendida and concat(fservicio,' ', hfinservicio) between date_sub(date_format(now(),'%Y-%m-%d %H:%i:00'), interval 14 MINUTE) and date_format(now(),'%Y-%m-%d %H:%i:00') ";
     $result = mysql_query($sql, $remoto);
     while ($row = mysql_fetch_array($result)){
           $upd = "update estados_tanques_combustible set km_disponibles = km_disponibles - $row[1] where id_unidad = $row[0]";
           mysql_query($upd, $remoto);
     }
     mysql_close($remoto);

?>
