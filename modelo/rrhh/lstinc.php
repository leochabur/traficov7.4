<?
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $accion = $_POST['accion'];
  if ($accion == 'sbana'){ //
     $desde = explode("/", $_POST['desde']);
     $hasta = explode("/", $_POST['hasta']);
     $dd = 1;
     $dh = mktime( 0, 0, 0, $hasta[0], 1, $hasta[1] );

     $dh= date("t",$dh);
     $fdesde  = "$desde[1]-$desde[0]-$dd";
     $fhasta  = "$hasta[1]-$hasta[0]-$dh";
     $totdes = mktime(0,0,0,$desde[0],$dd,$desde[1]);
     $tothas = mktime(0,0,0,$hasta[0],$dh,$hasta[1]);
     $segundos_diferencia = $tothas - $totdes;
     $dias_diferencia = $segundos_diferencia / (60 * 60 * 24)+1;

     $meses = round($dias_diferencia/30);
     $francos = $meses * 6;


     $sql="SELECT upper(nov_text) as novedad, c.id
           FROM cod_novedades c
           inner join novporincent n on n.id_novedad = c.id
           where activa
           order by nov_text";

     $conn = mysql_connect("rrhh.masterbus.net", "masterbus", "master,07a");
     mysql_select_db("rrhh", $conn);

     $tabla ='<table id="example" name="example" width="100%" border="0">
                     <thead>
                            <tr>
                                <th>LEGAJO</th>
                                <th>CONDUCTOR</th>
                                <th>Total Dias</th>';
     $nov = mysql_query($sql, $conn);
     $novs = array();


     while ($row = mysql_fetch_array($nov)){
           //$novs[$i]=$row['id'];
            $novs[$row['id']] = " ";
           $tabla.="<th>$row[novedad]</th>";

     }

    // exit;
      //    print_r($novs);


     $novedades = implode(",", $novs);
     /*$sql="SELECT n.id_empleado, cn.id as id_novedad, legajo, upper(concat(apellido, ', ',nombre)) as chofer, nov_text, sum(if('2012-08-01' between desde and hasta, DATEDIFF(hasta, '2012-08-01')+1, if('2012-09-30' between desde and hasta, DATEDIFF('2012-09-30', desde)+1,DATEDIFF(hasta,desde)))) as dias
           FROM novedades n
           inner join cod_novedades cn on cn.id = n.id_novedad
           inner join empleados e on e.id_empleado = n.id_empleado
           where (('2012-08-01' between desde and hasta) or ('2012-09-30' between desde and hasta)) and (n.id_novedad in (SELECT id_novedad FROM novporincent))
           group by n.id_empleado, n.id_novedad
           order by n.id_empleado, n.id_novedad";*/
     $sql="(SELECT n.id_empleado, cn.id as id_novedad, legajo, upper(concat(apellido, ', ',nombre)) as chofer, nov_text, n.id,
sum(
    if('$fdesde' between desde and hasta,
      if('$fhasta' between desde and hasta,
        DATEDIFF('$fhasta', '$fdesde')+1,
      DATEDIFF(hasta, '$fdesde')+1),
    if('$fhasta' between desde and hasta,
      DATEDIFF('$fhasta', desde)+1,
    DATEDIFF(hasta,desde))
  )) as dias
           FROM novedades n
           inner join cod_novedades cn on cn.id = n.id_novedad
           inner join empleados e on e.id_empleado = n.id_empleado
           where (('$fdesde' between desde and hasta) or ('$fhasta' between desde and hasta)) and (n.id_novedad in (SELECT id_novedad FROM novporincent)) and (e.id_cargo = 1) and (e.id_empleador = 1)
           group by n.id_novedad, n.id_empleado
           order by n.id_empleado, n.id_novedad)
union
(SELECT id_empleado, 0 as id_novedad, legajo, upper(concat(apellido, ', ',nombre)) as chofer, '' as nov_text, '' as id, '' as dias
FROM empleados e
where (id_empleado not in (select id_empleado
from novedades n
where (('$fdesde' between desde and hasta) or ('$fhasta' between desde and hasta)) and (n.id_novedad in (SELECT id_novedad FROM novporincent)) and (e.id_cargo = 1) and (e.id_empleador = 1)
group by id_empleado)) and activo)
order by chofer";
//die($sql);

     $tabla.='<th>Total Trabajado</th></tr>
                   </thead>
                   <tbody>';
     $nov = mysql_query($sql, $conn) or die(mysql_error($conn));
    // $row = mysql_fetch_array($nov) or die(mysql_error($conn));
  //   die("registro ".count($novs));
     $row = mysql_fetch_array($nov);

     while ($row){
           $tabla.="<tr id='$row[id_empleado]' text='$row[chofer]'>
                        <td>$row[legajo]</td>
                        <td>".htmlentities($row['chofer'])."</td>
                        <td>$dias_diferencia</td>
                        <td>";
           $empleado = $row['id_empleado'];
           $tmp = $novs;
           $temp=$dias_diferencia-$francos;
           while ($empleado == $row['id_empleado']){
                 if ($row['id_novedad'] != 0)
                    $tmp[$row['id_novedad']]=$row['dias'];
                 $temp=$temp-$row['dias'];
                 $row = mysql_fetch_array($nov);
               /*  if ($row['dias']){
                    die(print_r($novs));
                 }   */
           }
           
           $tabla.=implode("</td><td>", $tmp);
           $tabla.="</td><td>$temp</td></tr>";
     }
     $tabla.='</tbody>
                   </table>
                   </table>
                  <style type="text/css">

                         #example tbody{ font-size: 75%; }
                         #example thead { font-size: 45%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                         #example tbody tr {cursor: pointer}
                  </style>
                  <script>
                          		$("#example").dataTable({
					                                    "sScrollY": "400px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
				                                       });
                                 $("#example tbody tr").click(function(){
                                                                         var cond = $(this).attr("id");
                                                                         var dialog = $("<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>").appendTo("body");

                                                                         dialog.dialog({
                                                                                        close: function(event, ui) {dialog.remove();},
                                                                                        title: $(this).attr("text"),
                                                                                        width:850,
                                                                                        height:400,
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
                                                                                   dialog.load("/modelo/rrhh/detnovemp.php",{emple:cond, desde:$("#desde").val(), hasta:$("#hasta").val()},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass("loading");});
                                                                         });
                  </script>';
            //  mysql_free_result($nov);
             // mysql_close($conn);

   print ($tabla);
  }

?>

