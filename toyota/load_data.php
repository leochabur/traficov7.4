<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include('./dateutils.php');
  include('bdadmin.php');

  if (isset($_POST['desde']))
     $desde = dateToMysql($_POST['desde'], '/');
  if(isset($_POST['hasta']))
     $hasta = dateToMysql($_POST['hasta'], '/');

  $accion = $_POST['accion'];

  if ($accion == 'ltno'){
     $ts="";
     if ($_POST['tis'])
        $ts = "and s.id_TipoServicio = $_POST[tis]";
     $sql="select t.id, upper(t.turno)
           from cronogramas c
           inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
           inner join turnos t on t.id = s.id_turno and t.id_estructura = s.id_estructura_turno
           where id_cliente = $_POST[cliente] and c.activo and s.activo  $ts and id_estructura_TipoServicio = 1
           group by s.id_turno";
       //    die($sql);
    try{
     $conn = conexcion();
     $select = "<select id='fturnos' name='fturnos' class='selec'>";
     $result = mysql_query($sql, $conn);
     while ($crono = mysql_fetch_array($result)){
           $select.="<option value='$crono[0]'>$crono[1]</option>";
     }
     $select.="</select>";
     @mysql_close($result);
     @mysql_close($conn);
     print $select;
     } catch (Exception $e) {print $e->getMessage();}
  }
  elseif ($accion == 'lcd'){
     $ts="";
     if ($_POST['tpo'])
        $ts = "and id_TipoServicio = $_POST[tpo]";
     $sql = "select c.id, upper(nombre)
             from cronogramas c
             inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
             where id_cliente = 10 and s.activo and c.activo $ts
             group by s.id_cronograma
             order by nombre";
     $conn = conexcion();
     $select = "<select id='fservicios' name='fservicios' class='selec'>";
     $result = mysql_query($sql, $conn);
     while ($crono = mysql_fetch_array($result)){
           $select.="<option value='$crono[0]'>$crono[1]</option>";
     }
     $select.="</select>";
     @mysql_close($result);
     @mysql_close($conn);
     print $select;
  }
  elseif ($accion == 'lori') {
     $sql="select ori.id, upper(ori.ciudad)
           from cronogramas c
           inner join ciudades ori on ori.id = ciudades_id_origen and ori.id_estructura = ciudades_id_estructura_origen
           inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
           inner join turnos t on t.id = s.id_turno and t.id_estructura = s.id_estructura_turno
           where id_cliente = $_POST[cliente] and s.activo and s.id_TipoServicio = $_POST[tis] and id_estructura_TipoServicio = 1
           group by ori.id
           order by ori.ciudad";
     $conn = conexcion();
     $select = "<select id='forigen' name='forigen' class='selec'>";
     $result = mysql_query($sql, $conn);
     while ($crono = mysql_fetch_array($result)){
           $select.="<option value='$crono[0]'>$crono[1]</option>";
     }
     $select.="</select>";
     @mysql_close($result);
     @mysql_close($conn);
     print $select;
  }
  elseif ($accion == 'lcf') {
  

     $turno="SELECT * FROM turnos";
     if ($_POST['tno'])
        $turno = "SELECT * FROM turnos WHERE id = $_POST[tno] and id_estructura = 1 ";

     if ($_POST['t_s'])
        $tipo_s = "and (id_TipoServicio = $_POST[t_s] and id_estructura_TipoServicio = 1)";
     else
         $tipo_s="";
         


     $origen = "";
     $i_v = "";
     if ($_POST['cho'] == true){
        if ($_POST['i_v'] == 'i'){
           $origen = "INNER JOIN (SELECT * FROM ciudades WHERE id = $_POST[cor] and id_estructura = 1) ori on ori.id = ciudades_id_origen and ori.id_estructura = ciudades_id_estructura_origen";
        }
        elseif ($_POST['i_v'] == 'v'){
           $origen = "inner join (SELECT * FROM ciudades WHERE id = $_POST[cor] and id_estructura = 1) des on des.id = ciudades_id_destino and des.id_estructura = ciudades_id_estructura_destino";
        }
        else{
             $origen = "inner join (SELECT * FROM ciudades WHERE id = $_POST[cor] and id_estructura = 1) des on des.id = ciudades_id_destino or des.id = ciudades_id_origen";
        }
     }
     else{
          if ($_POST['i_v'] != 'iv'){
             $i_v = " and (i_v = '$_POST[i_v]')";
          }
     }
     
     $servicio = "INNER JOIN (SELECT * FROM servicios WHERE activo $tipo_s $i_v) s ON s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura";



     $sql2="select c.id, upper(c.nombre)<br>
           from (SELECT * FROM cronogramas WHERE id_estructura = 1 and id_cliente = $_POST[cli] and activo) c<br>
           $origen <br>
           $servicio<br>
           inner join ($turno) t on t.id = s.id_turno and t.id_estructura = s.id_estructura_turno <br>
           group by c.id<br>
           order by c.nombre<br>";
    // die($sql2);
     $sql="select c.id, upper(c.nombre)
           from (SELECT * FROM cronogramas WHERE id_estructura = 1 and id_cliente = $_POST[cli] and activo) c
           $origen
           $servicio
           inner join ($turno) t on t.id = s.id_turno and t.id_estructura = s.id_estructura_turno
           group by c.id
           order by c.nombre";
    // die($sql);
     $conn = conexcion();
     $select = "<select id='fservicios' name='fservicios' class='selec'>";
     $result = mysql_query($sql, $conn);
     while ($crono = mysql_fetch_array($result)){
           $select.="<option value='$crono[0]'>$crono[1]</option>";
     }
     $select.="</select>";
     @mysql_close($result);
     @mysql_close($conn);
     print $select;
  }
  
?>

