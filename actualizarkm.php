<?php
     set_time_limit(0);

                     
           $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
           mysql_select_db('c0mbexport', $conn);
                     
                     $sql = "select s.id, c.km
                             from ordenes o
                             inner join servicios s on s.id= o.id_servicio and s.id_estructura = o.id_estructura_servicio
                             inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                             where fservicio >= '2017-08-01' and o.id_estructura = 1 and c.km > 0
                             group by o.id_servicio";
                      $i=0;
                     $res_estados = mysql_query($sql, $conn);
                     while ($data = mysql_fetch_array($res_estados)){
                           $upd="UPDATE ordenes SET km = $data[1] where id_servicio = $data[0] and fservicio >= '2017-08-01' and id_estructura = 1";
                           mysql_query($upd, $conn);
                          // die($upd);
                           $i++;
                     }
                     print "se actualizaron $i ordenes";

?>
