<?php
     session_start();

     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     

     
      if (isset($_POST['cliente'])){
         $cliente = $_POST['cliente'];
         $fechaVis=$_POST['fecha'];
      }
      else
         $fechaVis = date("Y-m-d");


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
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
    var element;
    var campo;
    var cmpstr;
	$(function() {


                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#EEF834");
                 $(".check").css("background-color", "#C0FFFF");
                 $(".susp").css("background-color", "#D0D0D0");
                 $(".scas").css("background-color", "#FFC0C0");
                 $("#table tbody tr div").mouseover(function() {
                                                                     $(this).addClass("tr_hover");
                                                                    });
                 $("#table tbody tr div").mouseout(function() {
                                                                     $(this).removeClass("tr_hover");
                                                                     });
                 $('#cargar, #chepmtycli').button();
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});

                 $('#fecha').datepicker({dateFormat:'yy-mm-dd'});
                 $('table input:button').click(function(){
                                                          var ord = $(this).attr('id');
                                                          var hs = $('#hsalida-'+ord).val();
                                                          var hl = $('#hllegada-'+ord).val();
                                                          var hf = $('#hfinservicio-'+ord).val();
                                                          var pax = $('#cantpax-'+ord).val();

                                                          $.post('/modelo/ordenes/modpxhs.php', {orden: ord, hsalida: hs, hllegada:hl, hfinserv: hf, cantpax: pax},function(data){ $('#'+ord).val('Listo');});

                                                          });


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
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}
</style>
<BODY>
<?php
     menu();

?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center">
              <select id="cliente" name="cliente" title="Please select something!" >
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select><input id="fecha" name="fecha" value="<?php echo $fechaVis;?>" type="text" size="20"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>

              <input type="hidden" id="posx" name="posx">
              <input type="hidden" id="posy" name="posy" <?php if (isset($_POST['posy'])) print "value='$_POST[posy]'";?>>
              <input type="hidden" id="order" name="order" <?php if (isset($_POST['order']) && ($_POST['order'] != '')) print "value='$_POST[order]'";?>>
              
         </form>
         </div>
         <div id='mjw'></div>
         <hr align="tr">
         <table width=100%>
                <tr>
                    <td class="fin">&Oacute;rdenes finalizadas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="check">&Oacute;rdenes checkeadas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="susp">&Oacute;rdenes suspendidas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="scas">&Oacute;rdenes sin conductores</td>
                    </tr>
         </table>
         <div id="tablaordenes">
              <table id='table' align="center" class="tablesorter" border="0" width="100%">
                     <thead>
            	            <tr class="">
                                <th id="nombre">Servicio</th>
                                <th id="interno">Interno</th>
                                <th id="chofer1">Conductor 1</th>
                                <th id="chofer2">Conductor 2</th>
                                <th id="hcitacion">H. Salida</th>
                                <th id="hsalida">H. Llegada</th>
                                <th id="hfinservicio">H. Fin Servicio</th>
                                <th id="hfinservicio">Pasajeros</th>
                                <th id="accion">Accion</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 if (isset($_POST['cliente'])){
                                 $con = conexcion();
                                 $sql = "SELECT o.id as id_orden,
                                         finalizada, date_format(hllegada, '%H:%i') as hllegada,
                                         date_format(hsalida, '%H:%i') as hsalida,
                                         date_format(hfinservicio, '%H:%i') as hfinserv,
       o.nombre,
       if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1,
       upper(c.razon_social) as razon_social,
       concat(ch2.apellido, ', ',ch2.nombre) as chofer2,
       comentario,
       interno,
       suspendida,
       checkeada,
       emp.color,
       cantpax
FROM ordenes o
LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
LEFT JOIN unidades m ON (m.id = o.id_micro)
LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
WHERE (fservicio = '$fechaVis') and (not borrada) and (not suspendida) and (o.id_estructura = $_SESSION[structure]) and (c.id = $cliente)
order by o.hsalida";
                                 //die($sql);
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                               //  die ("khkhkj".mysql_num_rows($query));
				                 while($row = mysql_fetch_array($query)){
                                            $id = $row['id_orden'];
                                            $tdclass="";
                                            if ($row['finalizada']){
                                               $tdclass="fin";
                                            }
                                            elseif ($row['checkeada']){
                                                   $tdclass="check";
                                            }
                                            elseif ($row['suspendida']){
                                               $tdclass="susp";
                                            }
                                            elseif ((!$row['id_empleado']) && (!$row['id_empleado2'])){
                                                   $tdclass="scas";
                                            }

					                        print "<tr>
                                                       <td class=\"$tdclass\">".utf8_decode($row['nombre'])."</td>
                                                       <td class=\"$tdclass\"><div style='color:#$row[color];'>$row[interno]</div></td>
                                                       <td class=\"$tdclass\">".htmlentities($row['chofer1'])."</td>
                                                       <td class=\"$tdclass\">".htmlentities($row['chofer2'])."</td>
                                                       <td class=\"$tdclass\"><input class='hora' type='text' size='5' value='$row[hsalida]' id='hsalida-$id'></td>
                                                       <td class=\"$tdclass\"><input class='hora' type='text' size='5' value='$row[hllegada]' id='hllegada-$id'></td>
                                                       <td class=\"$tdclass\"><input class='hora' type='text' size='5' value='$row[hfinserv]' id='hfinservicio-$id'></td>
                                                       <td class=\"$tdclass\"><input class='pax' type='text' size='5' value='$row[cantpax]' id='cantpax-$id'></td>
                                                       <td class=\"$tdclass\"><input id='$id' type='button' value='Guardar'></td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                                 }
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>
    <script>
                  <?php
                       if (isset($_POST['cliente'])){
                          print '$("#cliente> option[value='.$cliente.']").attr("selected", "selected");';
                       }
                  ?>
                  $('select').selectmenu({width: 350});

	</script>
		
		

         

         



</BODY>
</HTML>
