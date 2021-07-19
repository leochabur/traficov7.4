<?

  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  $desde = dateToMysql($_POST['desde'], '/');
  $hasta = dateToMysql($_POST['hasta'], '/');
  $interno = "";
  if ($_POST['int'])
     $interno = "and id_micro = $_POST[int]";

  $sql="select interno, date_format(fservicio,'%d/%m/%Y') as fecha, upper(concat(apellido,', ', nombre)) as conductor, id_chofer_1
      from(
    select fservicio, id_micro, id_chofer_1
    from ordenes
    where not borrada and not suspendida and fservicio between '$desde' and '$hasta' and id_estructura = $_SESSION[structure] $interno and id_chofer_1 is not null
    union all
    select fservicio, id_micro, id_chofer_2
    from ordenes
    where not borrada and not suspendida and fservicio between '$desde' and '$hasta' and id_estructura = $_SESSION[structure] $interno and id_chofer_2 is not null
    ) o
inner join unidades u on u.id = o.id_micro
inner join empleados e on e.id_empleado = o.id_chofer_1
where id_micro is not null and (id_propietario = 1 or interno between 2000 and 2999)
group by id_micro, fservicio, id_chofer_1
order by interno, fservicio, apellido";
//die($sql);
if ($_POST['int']){
     $conn=conexcion();
     $result = mysql_query($sql, $conn) or die($sql);
     $data = mysql_fetch_array($result);
     $datos = array();
     $conductores = array();
     $i=0;
     while ($data){
           $fecha = $data['fecha'];
           $datos[$fecha]= array();
           while (($data) && ($fecha == $data['fecha'])){
                 if (!in_array($data['conductor'], $conductores))
                    $conductores[$i++] = $data['conductor'];
                 $datos[$fecha][] = $data['conductor'];
                 $data = mysql_fetch_array($result);
           }
     }
     
     $tabla='<table id="example-advanced" name="example-advanced" border="1">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Fecha</th>';
     foreach($conductores as $value)
          $tabla.="<th>$value</th>";

     $tabla.='</tr></thead><tbody>';
     foreach($datos as $clave=>$valor){
          $tabla.="<tr><td>$clave</td>";
          foreach($conductores as $key=>$value){
               $color="#FFFFFF";
               $txt="";
               if (in_array($value, $valor)){
                  $color="#CF1313";
                  $txt="x";
               }
               $tabla.="<td bgcolor='$color' align='center'>$txt</td>";
          }
          $tabla.="</tr>";
     }
     $tabla.="</tbody></table>              <script>
$('#example-advanced').treetable();</script>";
     die($tabla);

}
else{
$tabla='<table id="example-advanced" name="example-advanced">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Interno</th>
                        <th>Fecha</th>
                        <th>Conductor</th>
                    </tr>
                    </thead>
                    <tbody>';

$formato = 'Y-m-d H:i:s';
     $conn=conexcion();
     $result = mysql_query($sql, $conn) or die($sql);
     $data = mysql_fetch_array($result);
     $minutes="";
     $i=0;
     while ($data){
           $int = $data['interno'];
           $tabla.="<tr data-tt-id='int-$int'><td><span class='folder'>&nbsp;&nbsp;$int</span></td><td></td><td></td></tr>";
           while (($data) &&($int == $data['interno'])){
                 $fecha = $data['fecha'];
                 $tabla.="<tr data-tt-id='$int-$fecha' data-tt-parent-id='int-$int'><td></td><td><span class='file'>$fecha</span></td><td></td></tr>";
                 while (($data) &&($int == $data['interno'])&&($fecha == $data['fecha'])){
                       $tabla.="<tr data-tt-id='chofer-$data[id_chofer_1]' data-tt-parent-id='$int-$fecha'><td></td><td></td><td><span class='drive'>$data[conductor]</span></td></tr>";
                       $data = mysql_fetch_array($result);
                 }
           }
     }
     $tabla.='</tbody>
              </table>
              <script>
$("#example-advanced").treetable({ expandable: true });

// Highlight selected row
$("#example-advanced tbody").on("mousedown", "tr", function() {
  $(".selected").not(this).removeClass("selected");
  $(this).toggleClass("selected");
});

// Drag & Drop Example Code
$("#example-advanced .file, #example-advanced .folder").draggable({
  helper: "clone",
  opacity: .75,
  refreshPositions: true,
  revert: "invalid",
  revertDuration: 300,
  scroll: true
});

$("#example-advanced .folder").each(function() {
  $(this).parents("#example-advanced tr").droppable({
    accept: ".file, .folder",
    drop: function(e, ui) {
      var droppedEl = ui.draggable.parents("tr");
      $("#example-advanced").treetable("move", droppedEl.data("ttId"), $(this).data("ttId"));
    },
    hoverClass: "accept",
    over: function(e, ui) {
      var droppedEl = ui.draggable.parents("tr");
      if(this != droppedEl[0] && !$(this).is(".expanded")) {
        $("#example-advanced").treetable("expandNode", $(this).data("ttId"));
      }
    }
  });
});
              </script>';
     print $tabla;      }
  
?>

