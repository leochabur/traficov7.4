<?php
     session_start();       //Modulo que muestra el diagrama diario correspondiente a la fecha determinada por el rango

     include('../main.php');
     include('../../controlador/bdadmin.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     $con = conexcion();

     encabezado('Menu Principal - Sistema de Administracion - Campana');


     $qcond = "SELECT id_empleado, upper(concat(apellido,', ', nombre)) as apenom
               FROM empleados
               where (id_estructura = $_SESSION[structure]) and (activo)
               order by apellido, nombre";
     $result = mysql_query($qcond, $con);
     while ($data = mysql_fetch_array($result)){
           $cond["$data[id_empleado]"] =  "$data[apenom]";
     }

     mysql_free_result($result);
     
     if (isset($_POST['fecha'])){
        $fecha = $_POST['fecha'];
     }
     else
         $fecha = date("Y-m-d");

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
 <script>

	$(function() {

                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#FF8080");
                 $('#cargar').button();




	});
	
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#table { font-size: 72.5%; }
#upcontact div{padding: 2px;}
#table thead tr th{padding: 7px;}
#table tbody tr td{padding: 0px;}
#cargar {font-size: 72.5%;}
.tr_hover {background-color: #ffccee}

option.0{background-color: #;}
option.1{background-color: ;}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center"><input id="desde" name="desde" value="<?php echo $fecha;?>" type="text" size="30"><input id="hasta" name="hasta" value="<?php echo $fecha;?>" type="text" size="30"><?armarSelectCond($_SESSION['structure']);?><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">
         <table width=100%>
                <tr>
                    <td class="fin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&Oacute;rdenes finalizadas</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td>
                    </tr>
         </table>
         <div id="tablaordenes">
              <table id='table' align="center" class="tablesorter" border="0" width="100%">
                     <thead>
            	            <tr class="">
                                <th>Orden</th>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicio</th>
                                <th>Interno</th>
                                <th>Conductor 1</th>
                                <th>Conductor 2</th>
                                <th>Cliente</th>
                                <th>Observaciones</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $query = mysql_query("SELECT o.id, finalizada, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, o.nombre, concat(ch1.apellido, ', ',ch1.nombre) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON ((ch1.id_empleado = o.id_chofer_1) and (ch1.id_estructura = o.id_estructura_chofer1))
                                                       LEFT JOIN empleados ch2 ON ((ch2.id_empleado = o.id_chofer_2) and (ch2.id_estructura = o.id_estructura_chofer2))
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN micros m ON (m.id_micro = o.id_micro) and (m.id_estructura = o.id_estructura_micro)
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure])", $con);
				                 while($row = mysql_fetch_array($query)){
                                            $obs=""; $hora=""; $cond=""; $tdclass="fin"; $divclass="fin"; //inicializamos todas las clases correspondientes a los estilos a aplicar
                                            if (!$row['finalizada']){
                                               $tdclass=""; //para no aplicar el color como finalizada
                                               $divclass="menu"; //como no esta finalizada puede mostrar el menu
                                               $hora="hora"; //para poder modificar los horarios
                                               $cond="cond"; //para poder modificar los conductores
                                               $obs="txt";
                                            }

					                        $id = $row['id'];
					                        print "<tr>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id-$id\">$id</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$hora\" id=\"hcitacion-$id\">$row[hcitacion]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$hora\" id=\"hsalida-$id\">$row[hsalida]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"nombre-$id\">$row[nombre]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_micro-$id\">$row[interno]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_chofer_1-$id\">$row[chofer1]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_chofer_2-$id\">$row[chofer2]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_diagnostico-$id\">$row[razon_social]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$obs $divclass\" id=\"comentario-$id\">$row[comentario]</div></td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>

		
		

                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>

                                                <?php
                                                 ;
                                                ?>

</BODY>
</HTML>
