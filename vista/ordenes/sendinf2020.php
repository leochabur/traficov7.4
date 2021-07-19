<?php
     session_start(); //modulo para dar de alta una provincia
     include('../main.php');
     include('../paneles/viewpanel.php');
     include_once('../../controlador/bdadmin.php');
    // define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

#newepe .error{
	font-size:0.8em;
	color:#ff0000;

#vrfdgma .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $.mask.definitions['~']='[012]';
                                                       $.mask.definitions['%']='[012345]';
                                                       $(".hora").mask("~9:%9",{completed:function(){}});
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('#newgepe').validate({
                                                                              submitHandler: function(e){
                                                                                                          var datos = $("#newgepe").serialize();
                                                                                                          $('#mje').html("<br><div align='center'><img  alt='cargando' src='../ajax-loader.gif' /><br></div>");
                                                                                                          $.post("/modelo/ordenes/sendinf.php", datos, function(data){$('#mje').html(data);});

                                                                                                         }
                                                                              });
                                                       $('.form').submit(function(event){
                                                                                          event.preventDefault();
                                                                                          var f = $(this);
                                                                                          $.post(f.attr('action'), 
                                                                                                 f.serialize(), 
                                                                                                 function(data){
                                                                                                              var response = $.parseJSON(data);
                                                                                                              if (response.det){
                                                                                                                    location.reload();
                                                                                                              }
                                                                                                              else
                                                                                                                  alert(response.mje);
                                                                                                              });
                                                                                              });
                                                                                              
                                                       $('#adddest, #send, .boton').button();
		$("#adddestin").validate({
			rules: {
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				email: "Introduzca una direccion de correo valida!"
				},
				submitHandler: function(){
                                          $.post("/modelo/ordenes/sendinf.php", $("#adddestin").serialize(), function(data){
                                                                                                                            var response = $.parseJSON(data);
                                                                                                                            if (response.estado){
                                                                                                                               var markup = "<tr id='trnd-"+response.id+"'><td>"+response.nombre+"</td><td>"+ response.mail +"</td><td></td></tr>";
                                                                                                                               $('#listac').append(markup);
                                                                                                                               $("#adddestin")[0].reset();

                                                                                                                            }
                                                                                                                            });
                                          }
		});

        $('#listac input:button').button().click(function(){
                                               var id = $(this).attr('id');
                                               $.post("/modelo/ordenes/sendinf.php", {accion:'deldes', dest: id}, function(data){
                                                                                                                                var response = $.parseJSON(data);
                                                                                                                                if (response.estado){
                                                                                                                                   $('#tr'+id).remove();
                                                                                                                                }
                                                                                                                                else{
                                                                                                                                     alert(response.mje);
                                                                                                                                }
                                                                                                                                });
                                               

                                               });


                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newgepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Generar y enviar informes</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Fecha Diagrama</td>
                                    <td><input id="fecha" name="fecha" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td align="right"><input type="submit" id="send" value="Previsualizar Informe"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <div id="mje"></div>
            <input type="hidden" name="accion" value="cnfsend">
         </form>
    </div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Opciones informe</legend>
		                 <div id="mensaje"></div>
                         <table border="1" align="center" width="40%" name="tabla" class="tablesorter">
                                <tr>
                                    <td WIDTH="20%">Tipo de Servicio</td>
                                    <td>Llegada a Planta desde...</td>
                                    <td>Salida de Planta hasta...</td>
                                    <td align=""> </td>
                                    <td align=""> </td>
                                </tr>
                                <?php
                                     $conn = conexcion();
                                     $sql = "SELECT if(ci.id, ci.id, 0) as id_ciclo, t.id as tipo_servicio, tipo, date_format(ingresodesde,'%H:%i') as desde,
                                                    date_format(salidahasta,'%H:%i') as hasta
                                             FROM tiposervicio t
                                             left join cicloinforme ci on id_tiposervicio = t.id and  id_estructuratiposervicio = t.id_estructura
                                             where id_estructura = $_SESSION[structure]
                                             ORDER BY tipo";
                                     $result = mysql_query($sql, $conn);
                                     while ($row = mysql_fetch_array($result)){
                                           print "<tr>
                                                      <td>$row[2]</td>
                                                      <form action='/modelo/ordenes/sendinf.php' class='form'>
                                                        <td><input type='text' size='6' name='desde' value='$row[desde]' class='hora' id='val-$row[desde]'></td>
                                                        <td><input type='text' size='6' name='hasta' value='$row[hasta]' class='hora' id='val-$row[hasta]'></td>
                                                        <td align='right'><input type='submit' value='Guardar/Modificar' id='add-$row[1]' class='boton'></td>
                                                        <input type='hidden' name='accion' value='add'/>
                                                        <input type='hidden' name='tipo' value='".$row['tipo_servicio']."'/>
                                                      </form>
                                                      <form action='/modelo/ordenes/sendinf.php' class='form'>
                                                        <td><input type='submit' value='Eliminar' class='boton'></td>
                                                        <input type='hidden' name='accion' value='del'/>
                                                        <input type='hidden' name='tipo' value='".$row['id_ciclo']."'/>
                                                      </form>
                                                      
                                                 </tr>";
                                     }
                                     mysql_close($conn);
                                ?>
                         </table>
            </fieldset>
    </div>
    
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
         <form id="adddestin">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Agregar destinatario informe</legend>
		                 <div id="mensaje"></div>
                         <table border="1" align="center" width="40%" name="tabla" class="tablesorter">
                                <tr>
                                    <td>Nombre Destinatario</td>
                                    <td>Direccion de correo</td>
                                    <td>Guardar </td>
                                </tr>
                                <tr>
                                    <td><input type="text" size="20" name="nombre"></td>
                                    <td><input type="text" size="20" name="email"></td>
                                    <td align='right'><input type="submit" value="Guardar" id="adddest"></td>
                                </tr>
                         </table>
                   </fieldset>
       	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Destinatarios informe</legend>
                         <table border="1" align="center" width="40%" id="listac" class="tablesorter">
                                <tr>
                                    <td>Nombre Destinatario</td>
                                    <td>Direccion de correo</td>
                                    <td>Acciones </td>
                                </tr>
                                <?php
                                     $conn = conexcion();
                                     $sql = "SELECT * FROM correos_informe ORDER BY nombre";
                                     $result = mysql_query($sql, $conn);
                                     while ($row = mysql_fetch_array($result)){
                                           print "<tr id='trnd-$row[0]'>
                                                      <td>$row[2]</td>
                                                      <td>$row[1]</td>
                                                      <td align='right'><input id='nd-$row[0]' type='button' value='Quitar' class='quitar'></td>
                                                 </tr>";
                                     }
                                     mysql_close($conn);
                                ?>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="adddes">
         </form>
    </div>
</body>
</html>

