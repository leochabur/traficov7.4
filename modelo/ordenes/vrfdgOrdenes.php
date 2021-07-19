<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if($accion == 'load')
  {
     $fecha = DateTime::createFromt('Y-m-d', $_POST['fecha']);

     $sql = "SELECT o.nombre,  concat(fservicio,' ',hcitacion) as citacion,
                     concat(fservicio,' ',hsalida) as salida,
                     concat(fservicio,' ',hllegada) as llegada,
                     interno, u.id as idMicro,
                     concat(e.apellido,', ',e.nombre) as conductor,
                     upper(razon_social) as cliente
            from ordenes o
            inner join unidades u on u.id = o.id_micro
            inner join clientes c ON id_cliente = c.id AND id_estructura_cliente = c.id_estructura
            left join empleados e on e.id_empleado = o.id_chofer_1
            where fservicio = '$fecha' and o.id_estructura = $_SESSION[structure] and not borrada and not suspendida
            order by id_micro, hsalida";

     $conn = conexcion(trua);
     
     $result = mysqli_query($conn, $sql);

     $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $tabla.='<table class="table table-zebra" width="100%" align="center">
                <thead>
                  <tr>
                      <th>Fecha</th>
                      <th>Apellido, Nombre</th>
                      <th>Novedad</th>
                      <th>Fecha Alta</th>
                      <th>Generada por</th>
                  </tr>
                </thead>
                <tbody>';
     while ($data)
     {
        $bus = $data['idMicro'];
        $last = array();
        $cuerpo = null;
        while (($data) && ($data['idMicro'] == $bus))
        {
            $citacion = DateTime::createFromt('Y-m-d H:i:s', $data['citacion']);
            $salida = DateTime::createFromt('Y-m-d H:i:s', $data['salida']);
            $llegada = DateTime::createFromt('Y-m-d H:i:s', $data['llegada']);
            if ($salida < $citacion)
            {
              $salida->add(new DateInterval('P1D'));
            }
            if ($llegada < $salida)
            {
              $llegada->add(new DateInterval('P1D'));
            }

            if ($last)//se ha procesado al menos un registro
            {
                if ($last['hl'] > $salida)
                {
                   $cuerpo.="<tr>
                                <td align='right'>".."</td>
                                <td align='left'>$data[2]</td>
                                <td align='right'>$data[3]</td>
                                <td align='center'>$data[4]</td>
                                <td align='center'>$data[5]</td>
                                <td align='left'>$data[8]</td>
                                <td align='center'>$data[6]</td>
                                <td align='left'>$data[7]</td>
                              </tr>";
                }
            }
            $last = array('orden' => $data['nombre'],
                          'hs' => $salida,
                          'hl' => $llegada);
            $data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        }

        if ($cuerpo) //hay al menos una orden superpuestas, de ser estar vacio no considera el interno
        {
          $tabla.="<thead>
                      <tr>
                          <th colspan=''>Ordenes superpuestas correspondinetes al interno $last[interno]</th>
                      </tr>
                      <tr>
                          <th>Fecha</th>
                          <th>Hora Citacion</th>
                          <th>Hora Salida</th>
                          <th>Hora Llegada</th>
                          <th>Servicios</th>
                          <thConductor></th>
                          <th>Cliente</th>
                      </tr>";
          $tabla.= $cuerpo;
        }
               $tabla.="<tr id='$data[0]'>
                            <td align='right'>$data[1]</td>
                            <td align='left'>$data[2]</td>
                            <td align='right'>$data[3]</td>
                            <td align='center'>$data[4]</td>
                            <td align='center'>$data[5]</td>
                            <td align='left'>$data[8]</td>
                            <td align='center'>$data[6]</td>
                            <td align='left'>$data[7]</td>
                            </tr>";
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>
              </div>';
     if ($_POST['vta']){
        $sql="SELECT n.id_empleado, legajo, concat(apellido,', ', nombre) as emples, date_format(n.fecha_alta, '%d/%m/%Y - %H:%i') as fecha_alta,
              upper(apenom) as user_alta, upper(nov_text) as nov_txt
              FROM novedades n
              inner join empleados e on e.id_empleado = n.id_empleado
              inner join usuarios u on u.id = n.usuario
              inner join cod_novedades c on c.id = n.id_novedad
              where id_novedad = $_POST[nde] and (desde = hasta) and (desde = '$desde') and n.activa and n.id_estructura = $_SESSION[structure]
                    and n.id_empleado not in (select id_chofer_1
                                              from ordenes
                                              where fservicio = '$desde' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_1 is not null
                                              group by id_chofer_1
                                              union
                                              select id_chofer_2
                                              from ordenes
                                              where fservicio = '$desde' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_2 is not null
                                              group by id_chofer_2)
                    order by apellido";
        $tabla.='<div id="tabs-2">';
        $tabla.='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Novedad</th>
                        <th>Fecha Alta</th>
                        <th>Generada por</th>
                    </tr>
                    </thead>
                    <tbody>';
                 //   die($sql);
        $data = mysql_fetch_array($result);
        while ($data){
              $tabla.="<tr id='$data[0]'>
                            <td align='right'>$data[1]</td>
                            <td align='left'>$data[2]</td>
                            <td align='right'>$data[5]</td>
                            <td align='center'>$data[3]</td>
                            <td align='left'>$data[4]</td>
                            </tr>";
              $data = mysql_fetch_array($result);

        }
        $tabla.='</tbody>
              </table>
              </div>';
     };

     $tabla.='</div>
              <script>
                        $( "#tabs" ).tabs();
                        $("#conv").button().click(function(){
                                                             if (confirm("Se modificaran todas las novedades de '.$_POST['txtor'].' a '.$_POST['txtde'].'. Confirma la operacion?")) {
                        
                                                             $("#divconv").html("<div align=\"center\"><img  alt=\"cargando\" src=\"../../vista/ajax-loader.gif\" /></div>");
                                                             var ori = $("#nov_orig").val();
                                                             var dest = $("#nov_dest").val();
                                                             var fecha = $("#desde").val();
                                                             $.post("/modelo/ordenes/vrfdg.php", {accion:"updnov", fec: fecha, novo: ori, novd:dest}, function(data){
                                                                                                                                                     $("#dats").html("");
                                                                                                                                                     });
                                                             }
                                                             });
              </script>
                  <style>
                         #example th{
                                padding:13px;
                                font-size: 82.5%;
                                }
                         #example tr{
                                padding:13px;
                                font-size: 92.5%;
                                }
                         .pad{
                                padding:10px;
                                font-size: 85%;
                                }
                         #example tbody tr:hover {

                                        background-color: #FF8080;

}
                  </style>';
    print $tabla;
  }
?>

