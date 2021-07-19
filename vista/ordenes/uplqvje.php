<?php
     session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
     include_once('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
     
$conn = conexcion();
$emple = explode("-", $_POST[cond]);
$sql = "select concat(e1.apellido,', ',e1.nombre) as emp1, e1.id_empleado as id_1
        from empleados e1
        where id_empleado = $emple[1]";
//die($sql);
$result = ejecutarSQL($sql, $conn);
if ($row = mysql_fetch_array($result)){
   $chofer1 = $row[1];
   $name_c1 = $row[0];
}

$sql = "SELECT * FROM liquidacion_servicio_turismo where id_orden = $_POST[orden] and id_conductor = $chofer1";
$query = ejecutarSQL($sql, $conn);
$liquidacion_abierta = 0;
$entregado_a_rendir = 0;
$status = 0; $id_liq=0;
if ($data = mysql_fetch_array($query)){
   $liquidacion_abierta = 1;
   $status = $data[cerrada];
   $entregado_a_rendir = $data[entregado_a_rendir];
   $id_liq = $data[id];
}

$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="/vista/js/validate-form/jquery.validate.min.js"></script>
  
  
<script type="text/javascript" src="/vista/js/validate-form/jquery.metadata.js"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#modorden .error{
	font-size:0.9em;
	color:#ff0000;
}


table.order thead tr th {
background-color: #e6EEEE;
border: 1px solid #FFF;
font-size: 8pt;
padding: 4px;}

</style>
<script>

	$(function() {
                 $( "#tabs" ).tabs();
                 $("#tipo_g").selectmenu({width: 150});
                 $(".monto").mask("9999.99");
                 $(":button").button();';
                 if ($liquidacion_abierta)
                    $tabla.='$.post("/modelo/ordenes/uplqvje.php",{accion:"load", orden:'.$_POST[orden].', cond:'.$chofer1.', entregado:'.$entregado_a_rendir.', status:'.$status.'},function(data){$("#body").html(data);});';
                 $tabla.='$(".addgst").click(function(){
                                               var add = $(this);
                                               add.hide();
                                               $.post("/modelo/ordenes/uplqvje.php",{accion:"add", type:$("#tipo_g").val(),
                                                                                     monto: $("#importe").val(), orden: $("#orden").val(),
                                                                                     emple:$("#chofer1").val(), entr:0},function(data){
                                                                                                                                       $.post("/modelo/ordenes/uplqvje.php",{accion:"load", orden: $("#orden").val(), cond:$("#chofer1").val(), entregado:'.$entregado_a_rendir.'},function(data){$("#body").html(data); add.show();});
                                                                                                                                       });

                                               });
                                               
                 $("#openliq").click(function(){
                                                var boton = $(this);
                                                boton.hide();
                                                $.post("/modelo/ordenes/uplqvje.php",{monto:$("#arendir").val(),accion:"oplq", orden:'.$_POST[orden].', cond:'.$chofer1.'}, function(data){
                                                                                                                                                                                           var response = $.parseJSON(data);
                                                                                                                                                                                           if (response.status){
                                                                                                                                                                                              boton.show();
                                                                                                                                                                                              $("#dialog").dialog("close");
                                                                                                                                                                                           }
                                                                                                                                                                                           else{
                                                                                                                                                                                                boton.show()
                                                                                                                                                                                                alert(response.msge);
                                                                                                                                                                                           }
                                                                                                                                                                                           });

                                                });';
            if (!$status){
                 $tabla.='$("#closel").click(function(){
                                               if (confirm("Seguro cerrar la liquidacion?")){
                                                  var boton = $(this);
                                                  boton.hide();
                                                  $.post("/modelo/ordenes/uplqvje.php",{accion:"close", liq: '.$id_liq.'}, function(data){
                                                                                                                                        var response = $.parseJSON(data);
                                                                                                                                        if (response.status){
                                                                                                                                           $("#dialog").dialog("close");
                                                                                                                                        }
                                                                                                                                        else{
                                                                                                                                             boton.show()
                                                                                                                                             alert(response.msge);
                                                                                                                                        }
                                                                                                                                        });
                                               }
                                               });';
            }


	$tabla.='});

</script>
<BODY>';


    $tabla.="<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Rendicion Gastos Conductores</legend>
                       <div id='tabs'>
                            <ul>
                                <li><a href='#tabs-1'>$name_c1</a></li>
                            </ul>
            <div id='tabs-1'>";
    if ($liquidacion_abierta){ //si existe una liquidacion abierta para la orden y conductor dados, muestra los datos para rendirla
              $sql = "SELECT i.id, i.detalle
                      FROM gastos_por_servicio_turismo g
                      INNER JOIN items_gastos_turismo i ON i.id = g.id_item_gasto
                      WHERE g.id_orden = $_POST[orden]";
              $result = ejecutarSQL($sql, $conn);
              $select = "<select id='tipo_g'>";
              while ($row = mysql_fetch_array($result)){
                    $select.="<option value='$row[0]'>$row[1]</option>";
              }
              $select.="</select>";
  
              $tabla.="<table width='75%' class='order'>
                              <thead>
                                     <tr>
                                         <th colspan='5' align='center'>Rendicion de Gastos</th>
                                     </tr>
                                     <tr>
                                         <th align='center'>Tipo Gasto</th>
                                         <th align='center'>Descripcion</th>
                                         <th align='center'>Importe</th>
                                         <th>+/-</th>
                                     </tr>
                                     <tr>
                                         <th align='center'>$select</td>
                                         <th align='center'><input type='text' id='desc' size='20' class='ui-widget-content ui-corner-all'></th>
                                         <th align='center'><input type='text' id='importe' size='20' STYLE='text-align:right' class='monto ui-widget-content ui-corner-all'></th>
                                         <th align='center'>";
                                         if (!$status)
                                            $tabla.="<img class='addgst' src='../../add.png' width='20' height='20' border='0'></th>";
                                     $tabla.="</tr>
                                     </thead>
                                     <tbody id='body'>
                                     </tbody>
                                     <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>";
                                     if (!$status)
                                                $tabla.="<td align='right'><input type='button' value='Cerrar Liquidacion' id='closel'></td>";
                                     else
                                         $tabla.="<td></td>";
                                     $tabla.="</tr>
                                     </tbody>
                       </table>
                       <input type='hidden' name='orden' id='orden' value='$_POST[orden]'/>
                       <input type='hidden' name='chofer1' id='chofer1' value='$chofer1'/>";
    }
    else{
         $tabla.="<table width='75%' class='order'>
                              <thead>
                                     <tr>
                                         <th colspan='2' align='center'>Entregado a Rendir</th>
                                     </tr>
                                     <tr>
                                         <th>Importe</th>
                                         <th></th>
                                     </tr>
                              </thead>
                              <tbody>
                                     <tr>
                                         <td align='right'><input type='text' name='arendir' id='arendir' size='10' class='monto ui-widget-content ui-corner-all'></td>
                                          <td align='right'><input type='button' value='Guardar' id='openliq'></td>
                                     </tr>
                              </tbody>
                       </table>";
    }
    
    mysql_close($conn);



  
  
  $tabla.="</div>
  </div>
             </fieldset>
             </BODY>
             </HTML>";
             @mysql_free_result($query);
             @mysql_close($con);
print $tabla;
?>

