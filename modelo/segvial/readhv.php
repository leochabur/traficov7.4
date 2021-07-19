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
     $interno = '';
     if ($_POST['inter']){
           $interno = "and (id = $_POST[inter])";
     }
     $cond = '';
     if ($_POST['conductores']){
           $cond = "and (e.id_empleado = $_POST[conductores])";
     }
     
     $filtrarf="";
     if (($_POST['desde'] != '') && ($_POST['hasta'] != '')){
        $filtrarf = "and fecha between '$desde' and '$hasta'";
     }
     
     $sql = "SELECT h.id as numero, date_format(fecha, '%d/%m/%Y') as fecha, date_format(hora, '%H:%i') as hora, legajo, upper(concat(apellido,', ',nombre)) as emple,
       interno, upper(ciudad) as ciudad, upper(direccion_incidente) as dire, descripcion_hecho, organismo
             FROM hechos_vandalicos h
             inner join empleados e on e.id_empleado = h.id_empleado
             inner join ciudades c on c.id = h.lugar_incidente
             left join unidades u on u.id = h.id_micro
             left join organismos_intervinientes_h_v oi on oi.id = h.id_organismo_interviniente
             where not eliminado $filtrarf $cond";
   //  die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $pendi="";
     if ($_POST['pend']){
        //$pendi = "<div class='navigation' style='width:200px'><b>Pendientes de reparacion</b></div><br>";
     }

     
     $tabla=$pendi.'
                    <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Detalle de Hechos</a></li>
  </ul>
    <div id="tabs-1">
    <div><a href="../../vista/segvial/toexcel.php?des="'.$_POST['desde'].'"&has="'.$_POST['hasta'].'"><img src="../../vista/excel.jpg" width="30" height="30" border="0"></a></div>
                    <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Interno</th>
                        <th>Ciudad</th>
                        <th>Ubicacion</th>
                        <th>Descripcion</th>
                        <th>Organismo Interviniente</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     $marcadores = "";
     $i=0;
     while ($data){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $tabla.="<tr id='$data[0]' $class>
                            <td align='right'>$data[0]</td>
                            <td align='center'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='right'>$data[3]</td>
                            <td align='left'>$data[4]</td>
                            <td align='right'>$data[5]</td>
                            <td align='left'>$data[6]</td>
                            <td align='left'>$data[7]</td>
                            <td align='left'>$data[8]</td>
                            <td align='left'>$data[9]</td>
                            <td align='center'><a href='../../vista/segvial/upheva.php?nro=$data[numero]&des=$_POST[desde]&has=$_POST[hasta]&con=$_POST[conductores]&cch=$_POST[inter]'><img src='../../vista/edit.png' border='0' width='15' height='15'></a></td>
                            </tr>";
                            //
               if ($data[latitud] && $data[longitud]){
                  if ($i == 0){
                         $marcadores =  "['$row[detalle]', $row[latitud], $row[longitud], $data[id_sin]]";
                  }
                 else{
                    $marcadores.= ",['$row[detalle]', $row[latitud], $row[longitud], $data[id_sin]]";
                 }
                 $i++;
               }
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>
              <a href="../../vista/segvial/mapa_hv.php?ds='.$desde.'&hs='.$hasta.'" id="mapa" target="_blank">Ver mapa de hechos vandalicos</a>
                </div>
              </div>

              <script>
                      $( "#tabs" ).tabs();
                      $( "#mapa" ).button();

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
  elseif($accion == 'detsin'){
                 $result = ejecutarSQL("SELECT date_format(concat(fecha,' ', hora),'%d/%m/%Y - %H:%i') as fecha_hora, upper(ciudad) as ciudad, u.interno, concat(apellido,' ,', e.nombre) as empleado,
       upper(o.nombre) as orden, organismo, upper(descripcion_hecho) as descripcion, upper(es.nombre) as estr
FROM hechos_vandalicos s
inner join ciudades c on c.id = s.lugar_incidente
left join unidades u on u.id = s.id_micro
left join empleados e on e.id_empleado = s.id_empleado
left join ordenes o on o.id = s.id_orden
left join organismos_intervinientes_h_v oi on oi.id = s.id_organismo_interviniente
left join estructuras es on es.id = s.id_estructura
where s.id = $_POST[sin]");
           $row = mysql_fetch_array($result);

         print'<form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Detalle Hecho Vandalico</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="75%">
                                <tr>
                                    <td WIDTH="20%">Estructura</td>
                                    <td>
                                        <input type="text" size="20" class="required ui-widget ui-widget-content  ui-corner-all" value="'.$row[estr].'">
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha - Hora Hecho</label></td>
                                    <td><input id="fsin" name="fsin" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" value="'.$row[fecha_hora].'"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Conductor</td>
                                    <td>
                                        <input id="nsin" size="35" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[empleado].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Lugar Hecho</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[ciudad].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td>
                                        <input id="nsin" size="8" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[interno].'"/>
                                    </td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Organismo Interviniente</td>
                                    <td>
                                        <input id="nsin" size="30" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[organismo].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Descripcion Hecho</td>
                                    <td>
                                        <textarea rows="10" cols="50">'.$row[descripcion].'</textarea>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                         </table>
	</fieldset>
</form>';





  }
  
?>

