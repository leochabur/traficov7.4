<?php
     session_start();
     include_once('../paneles/viewpanel.php');
     include_once('../main.php');
     include_once('../../controlador/bdadmin.php');
     define(RAIZ, '');

     encabezado('Menu Principal - Sistema de Administracion - Campana');

   //  $con = conexcion();




     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
     }
     else
         $fecha = date("d/m/Y");

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
 <script>
    var ordenes;
	$(function(){

                 $("#table").chromatable({height: "400px", scrolling: "yes"});
                 $('#table').tableHover();
                 $(':submit').button();
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $("#table tr:even").css("background-color", "#FFF");
                 ordenes = new Array();
                 $('#copy').click(function(){
                                             $('#copy').hide();
                                             $.post('/modelo/ordenes/closeordres.php', {accion: "cpy", orders: ordenes.join(',')}, function(data) {$('#orddest').html("<b>Se actualizaron "+data+" ordenes</b>");});

                                             });
	});
	
    function checkTodos (obj) {
             ordenes = new Array();
             if (obj.checked){
                   $( "#table td input:checkbox" ).each(function (){
                                                                     ordenes.push(this.id);
                                                                   });
             }
             $("#table input:checkbox").attr('checked', obj.checked);
    }

    if (!Array.indexOf) {
        Array.prototype.indexOf = function (obj, start) {
                                for (var i = (start || 0); i < this.length; i++) {
                                    if (this[i] == obj) {
                                       return i;
                                    }
                                }
                                return -1;
                                }
    }
    
    function cargarCheck(orden){
             if (orden.checked){
                ordenes.push(orden.id);
             }
             else{
                  var a = ordenes.indexOf(orden.id);
                  ordenes.splice(a,1);
             }
    }



	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#upcontact div{padding: 2px;}
#cargar {font-size: 72.5%;}

option.0{background-color: #;}
option.1{background-color: ;}
table

{

	width: 400px;

}

td.click, th.click

{

	background-color: #bbb;

}

td.hover, tr.hover

{

	background-color: #69f;

}

th.hover, tfoot td.hover

{

	background-color: ivory;

}

td.hovercell, th.hovercell

{

	background-color: #abc;

}

td.hoverrow, th.hoverrow

{

	background-color: #6df;

}
table {
	font-family:arial;
	background-color: #CDCDCD;

	font-size: 8pt;
	text-align: left;
}
table thead tr th, table.tablesorter tfoot tr th {
	background-color: #e6EEEE;
	border: 1px solid #FFF;
	font-size: 8pt;
	padding: 4px;
}
table thead tr .header {
	background-image: url(bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}
</style>
<BODY>
<?php
     menu();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Cerrar ordenes</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center">Ordenes del dia<input id="fecha" name="fecha" value="<?php echo $fec;?>" type="text" size="30"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">
         <table align="center">
         <tr>
             <td><input type="checkbox" id="all-empty" name="allempty" onClick="checkTodos(this)">Todos/Ninguno</td>
         </tr>
         <tr>
             <td>
              <table id='table' align="center" class="" border="0" width="85%">
                     <thead>
            	            <tr>
                                <th>Si/No</th>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicio</th>
                                <th>Destino</th>
                                <th>Conductor</th>
                                <th>Interno</th>

                            </tr>
                     </thead>
                     <tbody>
                            <?php
                             /*    die("SELECT o.id, date_format(o.hcitacion, '%H:%i'), date_format(o.hsalida, '%H:%i'), upper(de.ciudad), upper(ha.ciudad), upper(cl.razon_social), upper(o.nombre), upper(concat(e.apellido, ', ',e.nombre)) as apenom, interno
                                                       FROM ordenes o
                                                       inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                                       inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                                                       inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                                                       inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)
                                                       left join empleados e on (e.id_empleado = o.id_chofer_1) and (e.id_estructura = o.id_estructura_chofer1)
                                                       left join unidades u on (u.id = o.id_micro)
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (not finalizada) and (o.id_estructura = $_SESSION[structure])");  */
                                 $con = conexcion();
                                 $query = mysql_query("SELECT o.id, date_format(o.hcitacion, '%H:%i'), date_format(o.hsalida, '%H:%i'), upper(de.ciudad), upper(ha.ciudad), upper(cl.razon_social), upper(o.nombre), upper(concat(e.apellido, ', ',e.nombre)) as apenom, interno
                                                       FROM ordenes o
                                                       inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                                       inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                                                       inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                                                       inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)
                                                       left join empleados e on (e.id_empleado = o.id_chofer_1)
                                                       left join unidades u on (u.id = o.id_micro)
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (not finalizada) and (o.id_estructura = $_SESSION[structure])
                                                       ORDER BY o.hcitacion", $con);
				                 while($row = mysql_fetch_array($query)){
					                        print "<tr>
                                                       <td><input type=\"checkbox\" id=\"$row[id]\" onclick=\"cargarCheck(this);\"></td>
                                                       <td>$row[1]</td>
                                                       <td>$row[2]</td>
                                                       <td>".htmlentities($row[5])."</td>
                                                       <td>".htmlentities($row[6])."</td>
                                                       <td>".htmlentities($row[7])."</td>
                                                       <td>$row[8]</td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
              </td>
              </tr>
              </table>
         <hr align="tr">
         <div>
              <div id="orddest" align="center"><input type="submit" id="copy" name="copy" class="button" value="Cerrar Ordenes Seleccionadas"></div>
         </div>
         <hr align="tr">
	</fieldset>
</BODY>
</HTML>
