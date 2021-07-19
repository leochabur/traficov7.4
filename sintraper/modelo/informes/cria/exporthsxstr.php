<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
header("Content-type: application/octet-stream");
//indicamos al navegador que se está devolviendo un archivo
header("Content-Disposition: attachment; filename=hsxestructura.xls");
//con esto evitamos que el navegador lo grabe en su caché
header("Pragma: no-cache");
header("Expires: 0");
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  function secToHs($sec){
           $mins = $sec / 60;
           $second = $mins%60;
           if ($second < 10){
              $second="0$second";
           }
           return (floor($mins/60)).":$second";
  }
  
     $desde = $_GET['desde'];
     $hasta = $_GET['hasta'];
     $str = $_GET['str'];
     $hs = $_GET['hs'];

     $conn = conexcion();

     $sql = "select emple, sum(hs)
             from(
                  select id_chofer_1 as emple, TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_chofer_1 is not null)
                  union all
                  select id_chofer_2 as emple, TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_chofer_2 is not null)
                  ) o
             group by emple";
     $result = mysql_query($sql, $conn);
     $hstot = array();
     while ($data = mysql_fetch_array($result)){
           $hstot[$data[0]] = $data[1];
     }

     $sql = "SELECT emple, upper(concat(apellido,', ',em.nombre)) as apenom, upper(if (id_cliente_vacio is not null, concat('VACIO - ',cv.razon_social), c.razon_social)) as cliente, sum(hs), count(*), sum(km)
             from(
                  select id_chofer_1 as emple, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs, id_estructura, vacio, km
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_chofer_1 is not null)
                  union all
                  select id_chofer_2 as emple, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs, id_estructura, vacio, km
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_chofer_2 is not null)
                  )o
             inner join estructuras e on e.id = o.id_estructura
             left join empleados em on em.id_empleado = o.emple
             left join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
             left join clientes cv on cv.id = o.id_cliente_vacio and cv.id_estructura = o.id_estructura_cliente_vacio
             group by o.emple, id_cliente, id_cliente_vacio
             order by apenom, vacio, cliente";

     $result = mysql_query($sql, $conn);
     $tabla='<table id="kmxcli" name="kmxcli" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $emple = $data[0];
           $tabla.="<tr class='ui-widget-header' >
                        <td colspan='5'>".htmlentities($data[1])."</td>
                    </tr>
                    <tr class='ui-widget-header'>
                        <td>CLIENTE</td>
                        <td>TOT HS.</td>
                        <td>CANT. SERV.</td>
                        <td>TOT KM</td>
                        <td>%</td>
                    </tr>";
           while (($data) && ($emple == $data[0])){
               $tabla.="<tr>
                            <td align='left'>$data[2]</td>
                            <td align='right'>".secToHs($data[3])."</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>$data[5]</td>
                            <td align='right'>".str_replace(".", ",",round((($data[3]/$hstot[$emple])*100),4))."%</td>
                        </tr>";
               $data = mysql_fetch_array($result);
           }
           $tabla.="<tr>
                        <td align='right'><b>TOTAL</b></td>
                        <td align='right'><b>".secToHs($hstot[$emple])."</b></td>
                        <td></td>
                        <td></td>
                        <td align='right'><b>100%</b></td>
                    </tr>
                    <tr >
                        <td colspan='5'><hr align='tr'></td>
                    </tr>";
     }
     $tabla.="";
     $tabla.='</tbody>
              </table>

                  <style>
                         #kmxcli tbody{ font-size: 85%; }
                         #kmxcli tbody tr:hover {
                                                     background-color: #FF8080;
                                                     }

                          #kmxcli tbody tr {cursor: pointer}
}
                  </style>
                  <script type="text/javascript">
                          $("#kmxcli tbody tr:odd").css("background-color", "#ddd");
                          $("#kmxcli tbody tr:even").css("background-color", "#ccc");
                          $("#back").button().click(function(){
                                                               $("#dats").html("<div align=\"center\"><img  src=\"../../ajax-loader.gif\" /></div>");
                                                               $.post("/modelo/informes/cria/kmxstr.php", {accion:"reskmstr", desde: $("#desde").val(), hasta: $("#hasta").val(), }).done(function(data){$("#dats").html(data);})
                                                               });
                  </script>';
    print $tabla;
  
?>

