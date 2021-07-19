<?php
     set_time_limit(0);

                  /*   $conn = mysql_connect('rrhh.masterbus.net', 'masterbus', 'master,07a');// conectar_mysql() or die(alerta(mysql_error()));//CONECTAR
                     mysql_select_db('rrhh', $conn);
                     $sql="SELECT legajo, id_licencia
                           FROM licenciasxconductor l
                           inner join empleados e on e.id_empleado = l.id_conductor
                           where e.activo";
                     $result = mysql_query($sql, $conn);   */
                     //";
                     
                     $export = mysql_connect('export.masterbus.net', 'mbexpuser', 'Mb2013Exp');
                     mysql_select_db('mbexport', $export); /*
                     $sql = "select id, hfinservicio
                            from ordenes
                            where fservicio > '2015-01-01'
                            and id_servicio is null";
                     $result = mysql_query($sql, $export);
                     while ($data = mysql_fetch_array($result)){
                           $up = "UPDATE ordenes set hllegada = '$data[1]' where id = $data[0]";
                           mysql_query($up);
                     }  */
              /*       $fp = fopen("servicios_toyota.csv", "r");
                     while(!feof($fp)) {
                                       $linea = fgets($fp);
                                       echo $linea . "<br />";
                     }
                     fclose($fp);*/
                     $fila = 1;
if (($gestor = fopen("servicios_toyota.csv", "r")) !== FALSE) {
$linea=1;
$res="";
    while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {
          $srv = $datos[1];
          $sql = "SELECT nombre, s.*
                  FROM cronogramas c
                  inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
                  where s.id = $srv";
          $result = mysql_query($sql, $export);

          if ($data = mysql_fetch_array($result)){
             if ($data[0] == $datos[0] && $datos[2] == $data['hcitacion'] && $datos[3] == $data['hsalida']) {
                //echo "SON EXCATAMENTE IGUALES";
                
                if ($datos[4] == 'Produccion')
                   $tipo = 2;
                elseif ($datos[4] == 'Administracion'){
                       $tipo = 1;
                }
                
                if ($datos[5] == 'Tarde')
                   $turno = 2;
                elseif ($datos[5] == 'Noche')
                       $turno = 3;
                else
                    $turno = 1;
                $iv = $datos[6];

                
                if ($data['id_TipoServicio'] == $tipo && $data['id_turno'] == $turno && $data['i_v'] == $iv){

                }
                else{
                    // $upd="update servicios set i_v = '$iv' , id_turno = $turno, id_TipoServicio = $tipo where id = $srv and id_estructura = 1";
                    // mysql_query($upd, $export);
                }
             }
             else{
                  $res.="$srv,";
                 echo "NO SON IGUALES -> LINEA $linea<br>";
                 //die();
             }
          }
          $linea++;
    }
    fclose($gestor);
    echo "<br>($res)";
}

?>
