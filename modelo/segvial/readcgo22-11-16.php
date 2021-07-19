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
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT id, interno
             FROM unidades
             where id_estructura =  $_SESSION[structure] and activo
             order by interno";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="internos" name="internos" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
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
             where e.id_estructura =  $_SESSION[structure]
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

     $cond = '';
     if ($_POST['conductores']){
           $cond = "and (d.id_empleado = $_POST[conductores])";
     }
     $res = isset($_POST['reso'])?"and fecha_respuesta is null":"";
     $ent = isset($_POST['entr'])?"and fecha_entrega is null":"";

     $sql = "SELECT concat(des.apellido, ', ', des.nombre) as dest, date_format(fecha_emision, '%d/%m/%Y') as emi, date_format(fecha_entrega, '%d/%m/%Y') as entre,
       concat(sol.apellido, ', ', sol.nombre) as sol, mediante, descripcion_hecho, rs.descripcion, d.id, nro_descargo
FROM descargos d
left join empleados des on des.id_empleado = d.id_empleado
left join empleados sol on sol.id_empleado = d.id_solicitante
left join resolucionSiniestro rs on rs.id = d.resolucion
where not eliminado and fecha_emision between '$desde' and '$hasta' $cond $res $ent";
 //    die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $pendi="";
     if ($_POST['pend']){
        //$pendi = "<div class='navigation' style='width:200px'><b>Pendientes de reparacion</b></div><br>";
     }

     
     $tabla=$pendi.'<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Nro Descargo</th>
                        <th>Destinatario</th>
                        <th>F. Emision</th>
                        <th>F, Entrega</th>
                        <th>Solicitante</th>
                        <th>Mediante...</th>
                        <th>Descripcion Hecho</th>
                        <th>Resolucion</th>
                        <th>Editar</th>
                        <th>Imprimir</th>
                        <th>Eliminar</th>
                    </tr>
                    </thead>
                    <tbody>';

     while ($data = mysql_fetch_array($result)){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $tabla.="<tr id='$data[7]' $class>
                            <td align='left'>$data[nro_descargo]</td>
                            <td align='left'>$data[0]</td>
                            <td align='center'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='left'>$data[4]</td>
                            <td align='left'>$data[5]</td>
                            <td align='left'>$data[6]</td>
                             <td align='center'><a href='../../vista/segvial/updcgo.php?nro=$data[id]&ds=$desde&hs=$hasta'><img src='../../vista/edit.png' border='0' width='15' height='15'></a></td>
                            <td align='center'><a href='../../modelo/segvial/dcgo.php?dcgo=$data[id]' target='_blank'><img src='../../vista/print.png' border='0' width='15' height='15'></a></td>
                            <td align='center'><img src='../../vista/menos.png' border='0' width='15' height='15' class='menos'></td>
                            </tr>";
     }

     $tabla.='</tbody>
              </table>
              <script>
                      $(".menos").click(function(){
                                                   var id = $(this).parent().parent().attr("id");
                                                   if (confirm("Seguro eliminar el descargo?")){
                                                      $.post("/modelo/segvial/updcgo.php", {accion:"deldcgo", dcgo:id}, function(data) {
                                                                                                                                       var response = $.parseJSON(data);
                                                                                                                                       if (response.status){
                                                                                                                                                            $("#"+id).remove();
                                                                                                                                       }
                                                                                                                                       else{
                                                                                                                                            alert(response.msge);
                                                                                                                                       }
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
                                font-size: 80.5%;
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

