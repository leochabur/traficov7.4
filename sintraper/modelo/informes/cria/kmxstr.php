<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     
     $conn = conexcion();

     $sql = "SELECT sum(km)
             FROM ordenes o
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada)";
     $result_km = mysql_query($sql);
     $tot_km=1;
     if ($data = mysql_fetch_array($result_km)){
        $tot_km = $data[0];
     }
     $sql = "SELECT count(*), sum(km), upper(e.nombre), e.id
             FROM ordenes o
             inner join estructuras e on e.id = o.id_estructura
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada)
             group by id_estructura";
     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>
                    <tr class="ui-widget-header">
                        <th>Estructura</th>
                        <th>Total Km</th>
                        <th>Total Servicios</th>
                        <th>%</th>
                    </tr>';
     while ($data = mysql_fetch_array($result)){
               $tabla.="<tr bgcolor='$color' id='$data[3]'>
                            <td align='left'>".htmlentities($data[2])."</td>
                            <td align='right'>$data[1]</td>
                            <td align='right'>$data[0]</td>
                            <td align='right'>".round((($data[1]/$tot_km)*100),2)." %</td>
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
                                                                     var tkm = $(this).find("td").eq(1).html();
                                                                     var nomstr = $(this).find("td").eq(0).html();
                                                                     var estr = $(this).attr("id");
                                                                     var desd = $("#fdesde").val();
                                                                     var hast = $("#fhasta").val();
                                                                     $.post("/modelo/informes/cria/kmxstr.php", {accion:"reskmstr", desde: desd, hasta: hast, km: tkm, str: estr, nombre: nomstr}, function(data){  $("#dats").html(data);
                                                                                                                                                                           });
                                                                     });

                  </script>';
    print $tabla;
  }
  elseif($accion == 'reskmstr'){
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     $str = $_POST['str'];
     $km = $_POST['km'];

     $conn = conexcion();

     $sql = "SELECT count(*), sum(km), interno, count(distinct(id_cliente)), count(distinct(id_chofer_1)), u.id
             FROM ordenes o
             inner join estructuras e on e.id = o.id_estructura
             left join unidades u on u.id = o.id_micro
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str)
             group by o.id_micro
             order by interno";

     $result = mysql_query($sql, $conn);
     $tabla='<table id="detinternos" name="detinternos" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th colspan="6">SERVICIOS REALIZADOS EN '.$_POST['nombre'].'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Interno</th>
                        <th>Total Km</th>
                        <th>Total Servicios</th>
                        <th>Cant Clientes presto servicio</th>
                        <th>Cant. choferes que lo condujo</th>
                        <th>%</th>
                    </tr>
                    </thead>
                    <tbody>';
     while ($data = mysql_fetch_array($result)){
               $tabla.="<tr bgcolor='$color' id='$data[5]'>
                            <td align='left'>$data[2]</td>
                            <td align='right'>$data[1]</td>
                            <td align='right'>$data[0]</td>
                            <td align='right'>$data[3]</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>".round((($data[1]/$km)*100),4)." %</td>
                            </tr>";
     }
           $tabla.="<tr><td colspan='9'><hr align='tr'></td></tr>";

     $tabla.='</tbody>
              </table>
              <input type="hidden" name="struct" id="struct" value="'.$str.'">
              <input type="hidden" name="fdesde" id="fdesde" value="'.$desde.'">
              <input type="hidden" name="fhasta" id="fhasta" value="'.$hasta.'">
              <input type="button" value="Volver" id="back">
                  <style>
                         #detinternos tbody{ font-size: 85%; }
                         #detinternos tbody tr:hover {
                                                     background-color: #FF8080;
                                                     }

                          #detinternos tbody tr {cursor: pointer}
}
                  </style>
                  <script type="text/javascript">
                          $("#detinternos tr:odd").css("background-color", "#ddd");
                          $("#detinternos tr:even").css("background-color", "#ccc");
                          $("#back").button().click(function(){
                                                               $("#dats").html("<div align=\"center\"><img  src=\"../../ajax-loader.gif\" /></div>");
                                                               $.post("/modelo/informes/cria/kmxstr.php", {accion:"reskm", desde: $("#desde").val(), hasta: $("#hasta").val()}).done(function(data){
                                                                                                                                                                                                    $("#dats").html(data);
                                                                                                                                                                                                    })
                                                               });
                          $("#detinternos tbody tr").click(function(){
                                                                     var tkm = $(this).find("td").eq(1).html();
                                                                     var nint = $(this).find("td").eq(0).html();
                                                                     var estr = $("#struct").val();
                                                                     var desd = $("#fdesde").val();
                                                                     var hast = $("#fhasta").val();
                                                                     var coche = $(this).attr("id");
                                                                     $.post("/modelo/informes/cria/kmxstr.php", {accion:"reskmcli", desde: desd, hasta: hast, km: tkm, str: estr, int:coche, interno:nint}, function(data){
                                                                                                                                                                                                             $("#dats").html(data);
                                                                                                                                                                                                             });
                                                                     });
                  </script>';
    print $tabla;
  }
  elseif($accion == 'reskmcli'){
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     $str = $_POST['str'];
     $coche = $_POST['int'];
     $km = $_POST['km'];

     $conn = conexcion();

     $sql = "SELECT count(*), sum(km), upper(razon_social), count(distinct(id_chofer_1))
             FROM ordenes o
             inner join estructuras e on e.id = o.id_estructura
             left join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
             where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (o.id_micro = $coche)
             group by id_cliente
             order by razon_social";

          $result = mysql_query($sql, $conn);
     $tabla='<table id="kmxcli" name="kmxcli" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th colspan="5">SERVICIOS PRESTADOS POR EL INTERNO '.$_POST['interno'].'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Cliente</th>
                        <th>Total Km</th>
                        <th>Total Servicios</th>
                        <th>Cant. choferes que lo condujo</th>
                        <th>%</th>
                    </tr>
                    </thead>
                    <tbody>';
     while ($data = mysql_fetch_array($result)){
               $tabla.="<tr>
                            <td align='left'>$data[2]</td>
                            <td align='right'>$data[1]</td>
                            <td align='right'>$data[0]</td>
                            <td align='right'>$data[3]</td>
                            <td align='right'>".round((($data[1]/$km)*100),4)." %</td>
                            </tr>";
     }
     $tabla.="<tr><td colspan='9'><hr align='tr'></td></tr>";
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

