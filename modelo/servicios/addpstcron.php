<?
  session_start(); //modulo para agregar servicios a un cronograma existente
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];
  if ($accion == 'addpst'){ //codigo para agregar un servicio al cronograma
     $salida = $_POST['sale'];
     $legada = $_POST['lega'];
     $cronog = $_POST['cron'];
     $posta = $_POST['pst'];
     $orden = $_POST['ord'];
     
     $campos = "id, id_cronograma, id_estructura_cronograma, hora_relativa_llegada, orden, hora_relativa_salida, id_postaHorario";
     $values = "$cronog, $_SESSION[structure], '$legada', $orden, '$salida', $posta";

     $srv = insert('postasCronogramas', $campos, $values);
     $conn = conexcion();
     $sql="SELECT upper(descripcion) as descrip, date_format(hora_relativa_llegada, '%H:%i') as llegada, date_format(hora_relativa_salida, '%H:%i') as salida, latitud, longitud, orden, pc.id
              FROM (select * from postasCronogramas) pc
              inner join (select * from postasHorarios where id = $posta) ph on ph.id = pc.id_postaHorario
              WHERE (pc.id_cronograma = $cronog) and (pc.id_estructura_cronograma = $_SESSION[structure])
              order by orden";
     $result = mysql_query($sql, $conn);
     if($data = mysql_fetch_array($result)){
              $tr_serv="<tr id='$data[id]'>
                            <td><div id='descripcion-$data[id]'>$data[descrip]</div></td>
                            <td><div class='hora' id='hora_relativa_llegada-$data[id]'>$data[llegada]</div></td>
                            <td><div class='hora' id='hora_relativa_salida-$data[id]'>$data[salida]</div></td>
                            <td><div>$data[latitud]</div></td>
                            <td><div>$data[longitud]</div></td>
                            <td>$data[orden]</td>
                            <td><input type='button' id='boton$data[id]' value='Desac.' onClick='modSrv(this.value, $data[id]);'></td>";
              mysql_free_result($result);
     }
     mysql_close($conn);
     print $tr_serv;
  }
  elseif ($accion == 'acdesrv'){
         $state = $_POST['sta'];
         $srv = $_POST['srv'];
         update('servicios', 'activo', $state, "(id = $srv) and (id_estructura = $_SESSION[structure])");
  }
  elseif ($accion == 'acdecro'){
         $state = $_POST['sta'];
         $crono = $_POST['cron'];
         print update('cronogramas', 'activo', $state, "(id = $crono) and (id_estructura = $_SESSION[structure])");
  }
?>

