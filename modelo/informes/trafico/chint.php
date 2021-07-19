<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if($accion == 'list'){

     $desde = dateToMysql($_POST['desde'], '/');

     $sql = "SELECT * FROM estadoDiagramasDiarios WHERE fecha = '$desde' AND id_estructura = $_SESSION[structure]";           

     $conn = conexcion();

     $result = mysql_query($sql, $conn);

     if ($row = mysql_fetch_array($result))
     {
        $fechaFinalizacion = DateTime::createFromFormat('Y-m-d H:i:s', $row['fechahorafinalizacion']);
     }
     else
        die('El diagrama aun no se ha finalizado');

      $sql = "select *
              from (
              select nombre, date_format(fservicio,'%d/%m/%Y') as fservicio, o.id, date_format(fecha_accion,'%d/%m/%Y %H:%i:%s') as fecha_accion, id_chofer_1, id_micro, borrada, suspendida, 1 as orden, id_user, id_cliente, date_format(hsalida,'%H:%i') as hsalida
              from ordenes o
              where o.fservicio = '$desde' and o.id_estructura = $_SESSION[structure]
              union all
              select nombre, date_format(fservicio,'%d/%m/%Y') as fservicio, o.id, date_format(fecha_accion,'%d/%m/%Y %H:%i:%s') as fecha_accion, id_chofer_1, id_micro, borrada, suspendida, 0 as orden, id_user, id_cliente, date_format(hsalida,'%H:%i') as hsalida
              from ordenes_modificadas o
              where fservicio = '$desde' and o.id_estructura = $_SESSION[structure]) o
              left join unidades u on u.id = o.id_micro
              inner join clientes c on c.id = o.id_cliente
              inner join usuarios us on us.id = id_user
              order by o.id, fecha_accion ASC";
     $result = mysql_query($sql, $conn) or die(mysql_error($conn));
     $tabla.='<span><h4>Fecha/Hora cierre diagrama: '.$fechaFinalizacion->format('d/m/Y H:i:s').'</h4></span>
              <table id="example" name="example" class="table table-zebra" width="100%" align="center">
              <thead>
                  <tr>
                      <th>Fecha</th>
                      <th>Servicio</th>
                      <th>Ciente</th>
                      <th>H. Salida</th>
                      <th>Interno Original</th>
                      <th>Nuevo Interno</th>
                      <th>Fecha Hora Cambio</th>
                      <th>Responsable</th>
                  </tr>
              </thead>
              <tbody>';


    $data = mysql_fetch_array($result);
    $body = "<tbody>";
     while ($data)
     {
        $orden = $data['id'];
        $last = null;
        $bodyAux = "";
        $delete = true;
        $cambio = false;
        while (($data) && ($orden == $data['id']))
        {
            if ($data['orden'])
            {
              $delete = ($data['borrada'] || $data['suspendida']);
            }
           if ($last)
           {
              if ($last['id_micro'] != $data['id_micro']) //cambio el interno, debe verificar si fue luego de la finalizacion del diagrama
              {
                $fechaChange = DateTime::createFromFormat('d/m/Y H:i:s', $data['fecha_accion']);
                if ($fechaChange > $fechaFinalizacion)
                {
                    if (!$cambio)
                    {
                        $bodyAux="<tr>
                                    <td>$data[fservicio]</td>
                                    <td>$data[nombre]</td>
                                    <td>$data[razon_social]</td>
                                    <td>$data[hsalida]</td>
                                    <td>$last[interno]</td>
                                    <td>$data[interno]</td>
                                    <td>$data[fecha_accion]</td>
                                    <td>$data[apenom]</td>
                                </tr>";
                        $cambio = true;
                    }
                }
              }
           }
           $last = $data;
           $data = mysql_fetch_array($result);
        }
        if (!$delete){
          $body.=$bodyAux;
        }
     }

     $tabla.=$body.'</tbody>
              </table>';
    print $tabla;
  }
  
?>

