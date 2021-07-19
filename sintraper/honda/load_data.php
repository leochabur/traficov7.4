<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include('./dateutils.php');
  include('./bdadmin.php');

  $desde = dateToMysql($_POST['desde'], '/');
  $hasta = dateToMysql($_POST['hasta'], '/');

  $accion = $_POST['accion'];

  if ($accion == 'lcd'){
     $sql = "select c.id, upper(nombre)
             from cronogramas c
             inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
             where id_cliente = 10 and s.activo and c.activo and id_TipoServicio = $_POST[tpo]
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
  
?>

