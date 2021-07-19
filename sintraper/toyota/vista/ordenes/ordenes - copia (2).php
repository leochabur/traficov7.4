<?php
     session_start();
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');
     $con = mysql_connect('localhost', 'root', 'leo1979');
     mysql_select_db('trafico', $con);

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
    var element;
	$(function() {

                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#FF8080");
                 $("#table tbody tr div").mouseover(function() {
                                                                     $(this).addClass("tr_hover");
                                                                    });
                 $("#table tbody tr div").mouseout(function() {
                                                                     $(this).removeClass("tr_hover");
                                                                     });


                 $.editable.addInputType('masked', {
                                                     element : function(settings, original) {
                                                                                            var input = $('<input />').mask(settings.mask);
                                                                                            $(this).append(input);
                                                                                            return(input);
                                                                                            }
                                                   });
                 $('#cargar').button();
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $('#fecha').datepicker({dateFormat:'yy-mm-dd'});
                 $('input:submit').button();
                 $('.hora').editable('/nuevotrafico/modelo/procesa/upd_ordenes.php', {type:"masked", mask: "~9:%9"});
               	 $('.cond').editable('/nuevotrafico/modelo/procesa/upd_ordenes.php', {
                                                  data   : <?php echo json_encode($cond);?>,
		                                          type   : 'select',
		                                          submit : 'Guardar',
		                                          callback : function(value, settings) {

                                                                                        }

	                                             });
                 $( "#dialog-form" ).dialog({
                                    autoOpen: false,
                                    height: 450,
                                    width: 750,
                                    modal: true,
                                    open: function(event, ui) {
                                                                 $.post('/nuevotrafico/modelo/procesa/mod_ordenes.php', {accion: "ldo", orden: element}, function(data) {
                                                                                                                                                                         var arg = data.split('-');
                                                                                                                                                                         if (arg[9] == 0){
                                                                                                                                                                            $("#origen option[value="+arg[6]+"]").attr("selected",true);
                                                                                                                                                                            $("#destino option[value="+arg[5]+"]").attr("selected",true);
                                                                                                                                                                            $("#corresponde option[value="+arg[7]+"]").attr("selected",true);
                                                                                                                                                                            $("#conductor option[value="+arg[8]+"]").attr("selected",true);
                                                                                                                                                                            $("#hcitacion").val(arg[0]);
                                                                                                                                                                            $("#hsalida").val(arg[1]);
                                                                                                                                                                            $("#hllegada").val(arg[2]);
                                                                                                                                                                            $("#hfin").val(arg[3]);
                                                                                                                                                                            $("#km").val(arg[10]);
                                                                                                                                                                            $("#nombre").val('Vacio - '+$("#nombre-"+element).html());
                                                                                                                                                                            $('select').selectmenu({style:'popup', width: 350});
                                                                                                                                                                            $('#fservicio').val($('#fecha').val());
                                                                                                                                                                         }
                                                                                                                                                                         else{
                                                                                                                                                                              alert('La orden corresponde a un servicio vacio');
                                                                                                                                                                              $( "#dialog-form").dialog( "close" );
                                                                                                                                                                         }

                                                                                                            });
                                                               }
                                    });



                 $(".menu").contextMenu({
                                          menu: 'myMenu'
                                         },
                                         function(action, el, pos) {
                                                                    var elem = ($(el).attr('id')).split('-');
                                                                    if (action == 'cerrar'){
                                                                       cerrarOrden(elem[1]);
                                                                    }
                                                                    if (action == 'vacio'){
                                                                       element = elem[1];
                                                                       abrirDialogo();
                                                                    }
                                                                    }
                                         );
                 $("#envioFormulario").click(function(){
                                                        var data = $("#upcontact").serialize();
                                                        $.post("/nuevotrafico/modelo/procesa/procesar_ordenes.php", data, function(data) {$( "#dialog-form").dialog( "close" ); $("#load").submit();});
                                                      });

	});
	
	function cerrarOrden(id){
          $.post('/nuevotrafico/modelo/procesa/mod_ordenes.php', {accion: "cls", orden: id}, function(data) {
                                                                                                             $('[id$='+id+']').css("background-color", "#FF8080");
                                                                                                            });


	}

	function abrirDialogo(){
          $( "#dialog-form").dialog( "open" );
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

#table { font-size: 82.5%; }
#upcontact div{padding: 2px;}
#table thead tr th{padding: 7px;}
#cargar {font-size: 72.5%;}
.tr_hover {background-color: #ffccee}
</style>
<BODY>
<?php
     menu();
?>
    <br><br>

    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center"><input id="fecha" name="fecha" value="<?php echo $fecha;?>" type="text" size="30"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">
         <div id="tablaordenes">
              <table id='table' align="center" class="ui-widget ui-widget-content tablesorter" border="0">
                     <thead>
            	            <tr class="">
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicio</th>
                                <th>Conductor 1</th>
                                <th>Cliente</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $query = mysql_query("SELECT o.id, finalizada, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, o.nombre, concat(apellido, ', ',ch1.nombre) as chofer1, upper(c.razon_social) as razon_social
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON ((ch1.id_empleado = o.id_chofer_1) and (ch1.id_estructura = o.id_estructura_chofer1))
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       WHERE (fservicio = '$fecha')", $con);
				                 while($row = mysql_fetch_array($query)){
                                            $hora=""; $cond=""; $tdclass="fin"; $divclass="fin"; //inicializamos todas las clases correspondientes a los estilos a aplicar
                                            if (!$row['finalizada']){
                                               $tdclass=""; //para no aplicar el color como finalizada
                                               $divclass="menu"; //como no esta finalizada puede mostrar el menu
                                               $hora="hora"; //para poder modificar los horarios
                                               $cond="cond"; //para poder modificar los conductores
                                            }

					                        $id = $row['id'];
					                        print "<tr>
                                                       <td class=\"$tdclass\"><div class=\"$hora $divclass\" id=\"hcitacion-$id\">$row[hcitacion]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$hora $divclass\" id=\"hsalida-$id\">$row[hsalida]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"nombre-$id\">$row[nombre]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$cond $divclass\" id=\"id_chofer_1-$id\">$row[chofer1]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$cond $divclass\" id=\"id_diagnostico-$id\">$row[razon_social]</div></td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>
		<ul id="myMenu" class="contextMenu">
			<li class="edit"><a href="#cerrar" id="edit">Cerrar Orden</a></li>
			<li class="cut separator"><a href="#vacio">Crear Vacio</a></li>
			<li class="copy"><a href="#copy">Eliminar Orden</a></li>
			<li class="paste"><a href="#paste">Enviar a otra fecha...</a></li>
			<li class="delete"><a href="#delete">Duplicar orden</a></li>
		</ul>
		
		
 <div id="dialog-form" title="Diagramar Servicio Vacio">
              <form id="upcontact">
	                <fieldset>
                              <div class="div">
		                      <label for="hcitacion">Fecha servicio</label>
                              <input id="fservicio" name="fservicio" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hcitacion">Nombre Servicio</label>
                              <input id="nombre" name="nombre" size="35" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="h_cita">Cliente</label>
                              <input id="cliente" name="cliente" type="text" size="35" class="ui-widget ui-widget-content  ui-corner-all" value="Master Bus S.A." readonly="readonly">
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="turno">Correspondiente a:</label>
                              <select id="corresponde" name="corresponde" title="Please select something!" >
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Origen:</label>
                              <select id="origen" name="origen" title="Please select something!" >
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Destino:</label>
                              <select id="destino" name="destino" title="Please select something!" >
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Conductor:</label>
                              <select id="conductor" name="conductor" title="Please select something!" >
                                                <?php
                                                 armarSelectCond($_SESSION['structure']);
                                                ?>
                              </select>
                              </div>


                              <div class="div">
		                      <label for="hcitacion">Hora Citacion</label>
		                      <input type="text" name="hcitacion" id="hcitacion" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" />
                              <span></span>
                              </div>
                              <div class="div">
                              <label for="hsalida">Hora Salida</label>
		                      <input type="text" name="hsalida" id="hsalida" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" />
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hllegada">Hora Llegada</label>
		                      <input type="text" name="hllegada" id="hllegada" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" />
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hfin">Hora Fin Servicio</label>
		                      <input type="text" name="hfin" id="hfin" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hfin">Km</label>
		                      <input type="text" name="km" id="km" maxlength="5" size="5" class="ui-widget-content ui-corner-all {required:true}"/>
                              <span></span>
                              </div>
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Guardar Orden" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="soes"/>
              </form>
         </div>

</BODY>
</HTML>
