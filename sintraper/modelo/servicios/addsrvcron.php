<?
  session_start(); //modulo para agregar servicios a un cronograma existente
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];
  if ($accion == 'addsrv'){ //codigo para agregar un servicio al cronograma
     $citado = $_POST['cita'];
     $salida = $_POST['sale'];
     $legada = $_POST['lega'];
     $finali = $_POST['fina'];
     $cronog = $_POST['cron'];
     $iv = $_POST['i_v'];
     $turno = $_POST['tno'];
     $tipo = $_POST['tpo'];
     
     $campos = "id, id_estructura, id_cronograma, id_estructura_cronograma, hcitacion, hsalida, hllegada, hfinserv, i_v, id_turno, id_estructura_turno, id_TipoServicio, id_estructura_TipoServicio, activo";
     $values = "$_SESSION[structure], $cronog, $_SESSION[structure], '$citado', '$salida', '$legada', '$finali', '$iv', $turno, $_SESSION[structure], $tipo, $_SESSION[structure], 1";

     $srv = insert('servicios', $campos, $values);
     $conn = conexcion();
     $sql="SELECT s.id, date_format(hcitacion, '%H:%i') as citacion, date_format(hsalida, '%H:%i') as salida, date_format(hllegada, '%H:%i') as llegada, date_format(hfinserv, '%H:%i') as hfin, upper(tipo) as tipo, upper(turno) as turno, if (i_v = 'i', 'IDA', 'VUELTA') as i_v, s.activo as activo
           FROM servicios s
           inner join turnos t on (t.id = s.id_turno) and (t.id_estructura = s.id_estructura_turno)
           inner join tiposervicio ts on (ts.id = s.id_TipoServicio) and (ts.id_estructura = s.id_estructura_TipoServicio)
           where (s.id = $srv) and (s.id_estructura = $_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     if($data = mysql_fetch_array($result)){
              $tr_serv="<tr id='$data[id]'>
                            <td><div class='hora' id='hcitacion-$data[id]'>$data[citacion]</div></td>
                            <td><div class='hora' id='hsalida-$data[id]'>$data[salida]</div></td>
                            <td><div class='hora' id='hllegada-$data[id]'>$data[llegada]</div></td>
                            <td><div class='hora' id='hfinserv-$data[id]'>$data[hfin]</div></td>
                            <td>".utf8_encode($data['turno'])."</td>
                            <td>$data[tipo]</td>
                            <td>$data[i_v]</td>
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

