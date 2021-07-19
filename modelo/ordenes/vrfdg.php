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

  if($accion == 'load'){
     $desde = dateToMysql($_POST['desde'], '/');

     $sql = "select n.id_empleado, legajo, concat(apellido,', ', nombre), cant_serv, date_format(inicia,'%H:%i') as inicia,
                    date_format(finaliza, '%H:%i') as finaliza, date_format(alta_nove, '%d/%m/%Y - %H:%i') as fecha_alta, upper(apenom) as user_alta, upper(nov_text) as nov_txt
             from(
                  select id_chofer_1, count(*) as cant_serv, min(hcitacion) as inicia, max(hfinservicio) as finaliza
                  from ordenes
                  where fservicio = '$desde' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_1 is not null
                  group by id_chofer_1
                  union
                  select id_chofer_2, count(*), min(hcitacion), max(hfinservicio)
                  from ordenes
                  where fservicio = '$desde' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_2 is not null
                  group by id_chofer_2) o
             inner join (SELECT id_empleado, fecha_alta as alta_nove, usuario, id_novedad
                         FROM novedades n
                         WHERE id_novedad = $_POST[nor] and (desde = hasta) and (desde = '$desde') and activa and id_estructura = $_SESSION[structure]) n on n.id_empleado = o.id_chofer_1
             inner join empleados e on e.id_empleado = n.id_empleado
             inner join usuarios u on u.id = n.usuario
             inner join cod_novedades c on c.id = n.id_novedad
             order by apellido";
    // die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla.='<div id="tabs">
                   <ul>
                       <li><a href="#tabs-1">Convertir '.$_POST['txtor'].' -> '.$_POST['txtde'].'</a></li>';
     if ($_POST['vta']){
        $tabla.='<li><a href="#tabs-2">Convertir '.$_POST['txtde'].' -> '.$_POST['txtor'].'</a></li>';
     };
     $tabla.='</ul>
                   <div id="tabs-1">
                   <p>
                     <font color="#000000"> <b>El siguiente proceso modificara las novedades generadas como '.$_POST['txtor'].' a '.$_POST['txtde'].' de todos aquellos conductores que tengan servicios diagramados el '.$_POST['desde'].'</b></font>
                   </p>
                   <div id="divconv"><input type="button" value="Convertir Novedades" id="conv"></div>';

     $tabla.='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Cant. Serv. Diag.</th>
                        <th>Inicia Jornada</th>
                        <th>Finaliza Jornada</th>
                        <th>Novedad</th>
                        <th>Fecha Alta</th>
                        <th>Generada por</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
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
  elseif($accion == "updnov"){
                 $fecha = dateToMysql($_POST['fec'], '/');
                 $sql = "SELECT n.id
                 FROM novedades n
                 inner join (select id_chofer_1
            from ordenes
            where fservicio = '$fecha' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_1 is not null
            group by id_chofer_1
            union
            select id_chofer_2
            from ordenes
            where fservicio = '$fecha' and id_estructura = $_SESSION[structure] and not borrada and not suspendida and id_chofer_2 is not null
            group by id_chofer_2) chd on chd.id_chofer_1 = n.id_empleado
            where id_novedad = $_POST[novo] and (desde = hasta) and (desde = '$fecha') and n.activa and n.id_estructura = $_SESSION[structure]";
            $result = ejecutarSQL($sql);
            while ($data = mysql_fetch_array($result)){
                  update("novedades", "id_novedad", "$_POST[novd]", "id = $data[id]");
            }
  }
  
?>

