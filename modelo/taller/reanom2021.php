<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT id, interno
             FROM unidades
             where id_estructura = $_POST[str] and activo
             order by interno";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="internos" name="internos" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
     if (!isset($_POST[sel])){
        $tabla.='<option value="0">Todos</option>';
     }
     
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[interno]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#internos').selectmenu({width: 100});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif ($accion == 'ldcond'){
     $conn = conexcion();

     $sql =
     "SELECT id_empleado, upper(concat(apellido,', ', nombre)) as empleado
             FROM empleados e
             where e.id_estructura = $_POST[str]
             order by apellido, nombre";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="conductores" name="conductores" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[0]'>$data[1]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#conductores').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $interno = '';
     if ($_POST['inter']){
           $interno = "and (id = $_POST[inter])";
     }
     $cond = '';
     if ($_POST['cond']){
           $cond = "where (id_empleado = $_POST[cond])";
     }
     $all = "";
     if ($_POST['pend'] == 0){
        $all = "and (reparada = 0)";
     }

     $filtraTipo = '';
     if ($_SESSION['userid'] == 117){
        $filtraTipo = 'AND r.id <> 4';
     }
     $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha,
                    interno,
                    if (causa is null, rubro, concat(rubro, ' (',origen,')')) as rubro,
                    detalle_anomalia,
                    date_format(a.fecha_alta, '%d/%m/%Y - %H:%i') as generada,
                    if (a.id_empleado is not null, upper(concat(e.apellido,', ', e.nombre)), upper(apenom)) as creada_por,
                    observacion_taller,
                    a.id,
                    reparada,
                    date_format(fecha_reparacion, '%d/%m/%Y') as frepara,
                    a.fecha_alta
             FROM anomalias a
             inner join rubros_anomalias r on r.id = a.id_rubroanomalia
             inner join (select * from unidades where (id_estructura = $_POST[str]) $interno) u on u.id = a.id_unidad
             left join (select * from empleados $cond) e on e.id_empleado = a.id_empleado
             left join usuarios us on us.id = a.id_usuario_alta
             left join origen_anomalias oa on oa.id = a.causa
             where  activa and fecha between '$desde' and '$hasta' $all $filtraTipo
             order by a.fecha_alta";
  //   die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $pendi="";
     if ($_POST['pend']){
        $pendi = "<div class='navigation' style='width:200px'><b>Reparaciones Finaizadas</b></div><br>";
     }

     
     $tabla=$pendi.'<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Fecha</th>
                        <th>Interno</th>
                        <th>Rubro</th>
                        <th>Detalle</th>
                        <th>Fecha creacion</th>
                        <th>Generada por</th>
                        <th>Fecha reparacion</th>
                        <th>Observacion taller</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $tabla.="<tr id='$data[7]' $class>
                            <td align='center'>$data[0]</td>
                            <td align='right'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='center'>$data[4]</td>
                            <td align='center'>$data[5]</td>
                            <td align='center'>$data[frepara]</td>
                            <td align='right'>$data[6]</td>
                            </tr>";
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>
              <script>
                      $(function(){
                                   $("#example tr").click(function(){
                                                                     var id_an = $(this).attr("id");
                                                                     var dialog = $("<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>").appendTo("body");
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: "Modificar Anomalia",
                                                                                   width:850,
                                                                                   height:600,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: "blind",
                                                                                                duration: 1000
                                                                                         },
                                                                                         hide: {
                                                                                               effect: "blind",
                                                                                               duration: 1000
                                                                                               }
                                                                                   });
                                                                                   dialog.load("/vista/taller/modanom.php",{orden:id_an},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass("loading");});

                                                                    });

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
  elseif($accion == 'modanom'){
                 $repa = 0;
                 $fecha = "NULL";
                 $hora = "NULL";
                 if (isset($_POST['rpda'])){
                    $repa = 1;
                    if ($_POST['frepa']){
                       $fecha = "'".dateToMysql($_POST['frepa'], '/')."'";
                    }
                    if ($_POST['hrepa']){
                       $hora = "'".$_POST['hrepa']."'";
                    }
                 }
                 try{
                    update("anomalias", "reparada, observacion_taller, fecha_reparacion, hora_reparacion, id_usuario_reparacion, orden_trabajo", "$repa, '$_POST[otaller]', $fecha, $hora, $_SESSION[userid], '$_POST[orepa]'", "(id = $_POST[anomalia])");
                    print "1";
                 }
                 catch (Exception $e) {
                       print "0";
                 }
  }
  
?>

