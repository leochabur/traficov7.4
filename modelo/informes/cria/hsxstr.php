<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
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

  if($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     
     $conn = conexcion();

     $sql = "SELECT sum(TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))))
             FROM ordenes o
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada)";
     $result_hs = mysql_query($sql);
     $tot_hs=1;
     if ($data = mysql_fetch_array($result_hs)){
        $tot_hs = $data[0];
     }
     $sql = "SELECT count(*), sum(time_to_sec(timediff(hfinservicio, hcitacion))), upper(e.nombre), e.id, count(distinct(id_chofer_1))
             FROM ordenes o
             inner join estructuras e on e.id = o.id_estructura
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada)
             group by id_estructura";
     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>
                    <tr class="ui-widget-header">
                        <th>Estructura</th>
                        <th>Total Hs</th>
                        <th>Total Servicios</th>
                        <th>Total Personal Afectado</th>
                        <th>%</th>
                    </tr>';
     while ($data = mysql_fetch_array($result)){
               $tabla.="<tr bgcolor='$color' id='$data[3]' text='$data[1]'>
                            <td align='left'>".htmlentities($data[2])."</td>
                            <td align='right'>".secToHs($data[1])."</td>
                            <td align='right'>$data[0]</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>".round((($data[1]/$tot_hs)*100),4)." %</td>
                            </tr>";
     }
           $tabla.="<tr><td colspan='9'><hr align='tr'></td></tr>";

     $tabla.='</tbody>
              </table>

              <input type="hidden" name="fdesde" id="fdesde" value="'.$desde.'">
              <input type="hidden" name="fhasta" id="fhasta" value="'.$hasta.'">
              <input type="hidden" name="kmt" id="kmt" value="'.$tot_km.'">
                  <style>

                         #example tbody tr:hover {
                                        background-color: #FF8080; }
                         #example tbody tr {cursor: pointer}

}
                  </style>
                  <script type="text/javascript">
                          $("#example tr:odd").css("background-color", "#ddd");
                          $("#example tr:even").css("background-color", "#ccc");
                          $("#example tbody tr").click(function(){
                                                                     var tkm = $(this).attr("text");
                                                                     var nomstr = $(this).find("td").eq(0).html();
                                                                     var estr = $(this).attr("id");
                                                                     var desd = $("#fdesde").val();
                                                                     var hast = $("#fhasta").val();
                                                                     $.post("/modelo/informes/cria/hsxstr.php", {accion:"reshsstr", desde: desd, hasta: hast, hs: tkm, str: estr, nombre: nomstr}, function(data){  $("#dats").html(data);
                                                                                                                                                                           });
                                                                     });

                  </script>';
    print $tabla;
  }
  elseif($accion == 'reshsstr'){
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     $str = $_POST['str'];
     $hs = $_POST['hs'];

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
             inner join empleados em on em.id_empleado = o.emple
             inner join clientes c on c.id = o.id_cliente and c.id_estructura = $str
             left join clientes cv on cv.id = o.id_cliente_vacio and cv.id_estructura = $str
             group by o.emple, id_cliente, id_cliente_vacio
             order by apenom, vacio, cliente";

     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/cria/exporthsxstr.php?desde='.$desde.'&hasta='.$hasta.'&str='.$str.'&hs='.$hs.'" text="Exportar a Excel"><img src="../../../vista/excel.jpg" width="35" height="35" border="0" text="Exportar a Excel"></a>
             <table id="kmxcli" name="kmxcli" class="ui-widget ui-widget-content" width="100%" align="center">
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
                            <td align='right'>".round((($data[3]/$hstot[$emple])*100),4)." %</td>
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
  }
  
?>

