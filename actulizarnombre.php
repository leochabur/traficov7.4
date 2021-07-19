<?php
error_reporting(0);
     set_time_limit(0);

                     $export = mysql_connect('traficonuevo.masterbasasdus.net', 'c0mbexpuser', 'Mb2013Exp');
                     mysql_select_db('c0mbexport', $export);
                     
                     $sql="select o.id as id, o.nombre as nombre, id_servicio
                                  from ordenes o
                                  join servicios s on s.id = o.id_servicio
                                  join cronogramas c on c.id = s.id_cronograma
                                  where o.id_estructura = 1 and fservicio = '2020-05-19' and
                                        id_TipoServicio = 2 and not borrada and not suspendida and c.id_cliente = 10
                                  order by id_servicio";
                 
                     $result = mysql_query($sql, $export);
                     $i=0;
                     $data = mysql_fetch_array($result);
                     $indice = array(0=> '- (A)', 1 => '- (B)', 2 => '- (C)', 3 => '- (D)');
                     while ($data)
                     {
                        $srv = $data['id_servicio'];
                        $i = 0;
                        while (($data) && ($srv == $data['id_servicio']))
                        {

                          $nombre = $data['nombre'].$indice[$i];
                          $update = "UPDATE ordenes SET nombre = '$nombre' WHERE id = $data[id]";

                          mysql_query($update, $export);
                          $i++;
                          $data = mysql_fetch_array($result);
                        }
                     }
                  
                     print ('Se actualizaron '.$i.' ordenes con exito');
?>
