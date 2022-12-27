<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');

                    
  if($_POST['accion'] == 'resume')
  {
    if (!$_POST['anio'])
    {
        print json_encode(array('ok' => false, 'message' => 'El campo año no puede permanecer en blanco!'));
        return;
    }
    $mes = $_POST['mes'];
    $mes++;
    $sql = "  SELECT c.id as cron, tu.id as tipo, c.nombre, o.id as orden
              from ordenes o
              inner JOIN unidades m ON (m.id = o.id_micro)
              inner join tipounidad tu on tu.id = m.id_tipounidad
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              where o.id_estructura = 1 and year(fservicio) = $_POST[anio] and month(fservicio) = $mes  and c.id in (select id_cronograma from peajesporcronogramas group by id_cronograma)";

     $conn = conexcion(true);
     

     $result = mysql_query($sql, $conn);
     
     mysqli_close($conn);

   
   $tabla.="Actualizados";

    print json_encode(array('ok' => true, 'message' => '<a href="#">OK</a>'));
    return;

  }
  
?>

