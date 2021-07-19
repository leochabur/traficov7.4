<?php
     set_time_limit(0);

                     
           $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
           mysql_select_db('c0mbexport', $conn);
                     
                     $sql = "select *
from(
select ho.id, i_v, ho.hllegada as horario_std, o.hllegada as horario_real, s.hllegada as horario_servicio, os.comentario
from ordenes o
inner join horarios_ordenes ho on ho.id = o.id
inner join servicios s on s.id = o.id_servicio
inner join cronogramas c on c.id = s.id_cronograma
left join obsSupervisores os on os.id = ho.id
where o.fservicio between '2016-02-01' and '2016-02-29' and o.id_estructura = 1 and c.id_cliente = 161 and not o.borrada and not o.suspendida and i_v = 'i') o
where horario_real > horario_std and hour(horario_servicio) = 7
order by horario_real";
                      $i=0;
                     $res_estados = mysql_query($sql, $conn);
                     while ($data = mysql_fetch_array($res_estados)){
                           $upd="UPDATE horarios_ordenes set hllegada = '07:40:00' where id = $data[0]";
                           mysql_query($upd, $conn);
                           $i++;
                     }
                     print "se actualizaron $i ordenes";

?>
