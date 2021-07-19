<HTML>
<HEAD>
 <TITLE>New Document</TITLE>
</HEAD>
<BODY>
<?php
    // set_time_limit(0);

  //   $conn = mysql_connect('127.0.0.1', 'root', 'leo1979');

   //  mysql_select_db('srcb', $conn);
   $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
           mysql_select_db('c0mbexport', $conn);
     
     $i=1;
if (($fichero = fopen("D:/export/ifracciones.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($fichero, 0,';')) !== FALSE) {

       if ($i > 1){
          if($data[4]) {
          
        //  id,
         //
         $campos = "id,
                 id_coche,
                 fecha,
                 id_conductor_1,
                 id_conductor_2,
                 id_ciudad,
                 id_tipo_infraccion,
                 importe,
                 id_resolucion,
                 fecha_entrega,
                 fecha_pago_real,
                 compromiso_pago,
                 latitud,
                 longitud,
                 id_user,
                 fecha_mod,
                 id_conductor_3,
                 detalle_resolucion,
                 fecha_vencimiento,
                 id_estructura,
                 lugar_infraccion,
                 eliminada";

                       $dni=explode("-", trim($data[4]));
                       $bd = "";
                       $file=$data[3];
                       $auxc="";
                       $auxi = 0;
                       $c1="NULL";$c2="NULL";$c3="NULL";
                       foreach ($dni as $nro){
                              $int = mysql_query("select id from unidades where interno = $data[1]", $conn);
                              if ($int = mysql_fetch_array($int)){
                                 $interno = $int[0];
                              }
                              else{
                                  $interno = 'NULL';
                              }
                       
                       

                           $sql = "SELECT upper(apellido), nombre, id_empleado FROM empleados e where replace(nrodoc,'.','') = '".trim(str_replace('.','',$nro))."'";
                           $result = mysql_query($sql, $conn);
                           $ok=false;
                           if ($row = mysql_fetch_array($result)){
                          //    if (strpos(strtoupper($data[3]), $row[0])===false){
                               //  echo "$row[0]";
                                 $ok=true;
                                 if ($auxi == 0){
                                    $c1=$row[2];
                                 }
                                 elseif ($auxi == 1){
                                        $c2=$row[2];
                                 }
                                 elseif ($auxi == 2){
                                        $c3=$row[2];
                                 }
                            //  }

                           }
                           $auxi++;
                      /*     else{

                                echo "FILA $i NO EXISTE DNI $nro<br>";
                                echo($sql."<br>");
                           } */
                       //   else
                      //         echo "  no nana ";
                       }
                       $reso = $data[10]?$data[10]:'NULL';
                       $values = "$data[0], $interno, '$data[2]', $c1, $c2, null, $data[6], ".($data[7]?str_replace(",",".",$data[7]):'NULL').", $reso, ".($data[11]?"'$data[11]'":'NULL').",".($data[13]?"'$data[13]'":'NULL').", ".($data[12]?"'$data[12]'":'NULL').", 0,0,17,now(),$c3, '$data[9]', ".($data[8]?"'$data[8]'":'NULL').",1,'$data[5]', 0";
                       mysql_query("INSERT INTO infracciones values ($values)",$conn) or die ("INSERT INTO infracciones values($values)");
                   //    print "SEGUN FILE $data[3]    -   SEGUN BD $auxc   FILA $i<br>";
          }
       }

           $i++;

          $dni = str_replace('.','', $data[4]);
          $sql = "SELECT apellido, nombre, id_empleado FROM empleados e where replace(nrodoc,'.','') = '$dni'";
        /*  $result = mysql_query($sql, $conn);
          if ($row = mysql_fetch_array($result)){
             if (trim($row[apellido]) != trim($data[2])){
                print "EXISTE Y NO COINCIDE EN BD: $row[0] -> EN ARCHIVO:$data[2]  -  EN LA FILA $i<BR>";
             }
             else{
                  if ($data[24]){
                     $int = "select id from unidades where interno = $data[24] and id_propietario = 1";
                     $result = mysql_query($int, $conn);
                     if ($interno = mysql_fetch_array($result)){
                        $numero = $interno[0];
                     }
                     else{
                          $numero = 'NULL';
                     }
                  }
                  else{
                       $numero = 'NULL';
                  }
                  
             }
           }*/
       }
    }
             
             
             
         /*
             
             
$sql = "INSERT INTO siniestros
                    (id,
                     id_empleado,
                     siniestro_numero,
                     fecha_siniestro,
                     hora_siniestro,
                     estado_clima,
                     cod_ubicacion,
                     calle1,
                     calle2,
                     id_localidad,
                     tipo_lesion,
                     resp_estimada,
                     tipo_maniobra,
                     norma_no_respetada,
                     cobertura_afectada,
                     id_coche,
                     id_cliente,
                     compania_seguro,
                     numero_poliza,
                     usr_alta,
                     fecha_alta,
                     id_estructura)
             VALUES ($data[1],
                     $row[2],
                     '$data[1]',
                     '$data[9]',
                     '$data[10]',
                     $data[11],
                     $data[12],
                     '$data[13]',
                     '$data[14]',
                     $data[16],
                     $data[19],
                     $data[20],".
                     ($data[21]?$data[21]:'NULL').",
                     ".($data[22]?$data[22]:'NULL').",
                     $data[23],
                     $numero,".
                     ($data[25]?$data[25]:'NULL').",
                     $data[26],
                     '$data[27]',
                     17,
                     now(),
                     1)";
              mysql_query($sql, $conn) or die (mysql_error($conn)."  -  ".$sql);
             }
          }
          else{
              print "NO EXISTE :$data[2] ($data[7])  -  EN LA FILA $i   -     FECHA $data[9]<BR>";
              }           */
            //  $i++;
        // Procesar los datos.
        // En $datos[0] está el valor del primer campo,
        // en $datos[1] está el valor del segundo campo, etc...

?>
</BODY>
</HTML>
