<?php
     session_start();
    // include_once('../paneles/viewpanel.php');
    include('../../controlador/bdadmin.php');
     include_once('../main.php');

     define(RAIZ, '');

     encabezado('Menu Principal - Sistema de Administracion - Campana');






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
 <script>
    var ordenes;
	$(function(){

                 $("#table").chromatable({height: "400px", scrolling: "yes"});
                 $(':submit').button();
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#destino').datepicker({dateFormat:'dd/mm/yy'});
                 $("#table tr:even").css("background-color", "#FFF");
                 ordenes = new Array();
                 $('#copy').click(function(){
                                             var send  = $.datepicker.parseDate('dd/mm/yy', $('#destino').val());
                                             var dest = send.getFullYear()+"-"+(send.getMonth()+1)+"-"+send.getDate();
                                             var orig  = $.datepicker.parseDate('dd/mm/yy', $('#fecha').val());
                                             var forig = orig.getFullYear()+"-"+(orig.getMonth()+1)+"-"+orig.getDate();
                                             $('#orddest').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                             $.post('/modelo/ordenes/cpydiagrama.php', {accion: "cpy", fecha: dest, forigen: forig, orders: ordenes.join(',')}, function(data) {document.location.href='ordenes.php?fecha='+$('#destino').val();});
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

</style>
<BODY>
<?php
     menu();
     $con = conexcion();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Copiar diagrama anterior</legend>
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
              <table id='table' align="center" class="tablesorter" border="0" width="75%">
                     <thead>
            	            <tr>
                                <th>Si/No</th>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Destino</th>

                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                /* $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estructura = $_SESSION[structure]";
                                 $result = mysql_query($sql, $con);
                                 if (mysql_num_rows($result)){ */
                                 if (isset($_POST['cargar'])){
                                 $sql = "SELECT o.id, date_format(o.hcitacion, '%H:%i'), date_format(o.hsalida, '%H:%i'), upper(de.ciudad), upper(ha.ciudad), upper(cl.razon_social), upper(o.nombre)
                                         FROM (SELECT id, hcitacion, hsalida, nombre, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente FROM ordenes WHERE (fservicio = '$fecha') and (not borrada) and (id_estructura = $_SESSION[structure]))o
                                         inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                         inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                                         inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                                         inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)
                                         order by o.hsalida";
                                /* }
                                 else{
                                 
                                      $sql="SELECT o.id, date_format(ho.hcitacion, '%H:%i'), date_format(ho.hsalida, '%H:%i'), upper(de.ciudad), upper(ha.ciudad), upper(cl.razon_social), upper(o.nombre)
                                            FROM (select * from ordenes WHERE (fservicio = '$fecha') and (not borrada) and (id_estructura = $_SESSION[structure])) o
                                            INNER JOIN (select * from horarios_ordenes where fservicio between DATE_SUB(date(now()), INTERVAL 35 DAY) and date(now()) group by id_servicio) ho on ho.id_servicio = o.id_servicio
                                            inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                            inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                                            inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                                            inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)";
                                 }    */
                               //  die($sql);
                                 $query = mysql_query( $sql, $con) or die(mysql_error($con));
                               ///  die("registors ".mysql_num_rows($query));
                     /*            die("SELECT o.id, date_format(o.hcitacion, '%H:%i'), date_format(o.hsalida, '%H:%i'), upper(de.ciudad), upper(ha.ciudad), upper(cl.razon_social), upper(o.nombre)
                                                       FROM ordenes o
                                                       inner join inner join (select * from horarios_ordenes where fservicio between DATE_SUB(date(now()), INTERVAL 45 DAY) and date(now())) ho on ho.id = o.id
                                                       inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                                       inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                                                       inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                                                       inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)
                                                       WHERE (o.fservicio = '$fecha') and (not o.borrada) and (o.id_estructura = $_SESSION[structure])");   */
                                 while($row = mysql_fetch_array($query)){
					                        print "<tr>
                                                       <td><input type=\"checkbox\" id=\"$row[id]\" onclick=\"cargarCheck(this);\"></td>
                                                       <td>$row[1]</td>
                                                       <td>$row[2]</td>
                                                       <td>".utf8_decode($row[5])."</td>
                                                       <td>".($row[6])."</td>
                                                       <td>".($row[4])."</td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                                 }
                            ?>
                     </tbody>
              </table>
              </td>
              </tr>
              </table>
         <hr align="tr">
         <div>
              <div id="orddest" align="center">Copiar al dia<input id="destino" name="destino" value="<?php echo $fec;?>" type="text" size="30"><input type="submit" id="copy" name="copy" class="button" value="Copiar Ordenes"></div>
         </div>
         <hr align="tr">
	</fieldset>
</BODY>
</HTML>
