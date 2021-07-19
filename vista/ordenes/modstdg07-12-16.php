<?php
     session_start(); //modulo para dar de alta una provincia
     include('../main.php');
     include('../paneles/viewpanel.php');
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
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#save').button();
                                                       $('select').selectmenu({style:'popup', width: 350});
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('#newepe').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#newepe").serialize();
                                                                                                                 $("#save").hide();
                                                                                                                 $('#result').html("<br><div align='center'><img  alt='cargando' src='../ajax-loader.gif' /><br><font color='#CB292E'><i>Modificando estado / Guardando contexto</i></font></div>");
                                                                                                                 $.post("/modelo/ordenes/modstdg.php", {accion:'sve', fecha:$('#fecha').val(), estados: $('#estados').val()}, function(data){
                                                                                                                                                                                var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                        "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                        "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                        "<strong>Se ha modificado con exito el Diagrama en la Base de Datos!</strong></p>"+
                                                                                                                                                                                        "</div>"+
                                                                                                                                                                                        "<div>";
                                                                                                                                                                                $('#mensaje').html(mje);
                                                                                                                                                                                var obj = JSON.parse(data);
                                                                                                                                                                                if (data == 1){
                                                                                                                                                                                   $('#result').html("<br><div align='center'><br><font color='#CB292E'><i>Contexto almacenado con exito</i></font></div>");
                                                                                                                                                                                   $(location).attr('href','/vista/ordenes/diagdiapdf.php?fec='+$('#fecha').val());
                                                                                                                                                                                }
                                                                                                                                                                                $( "#fecha").val('');
                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Estado del Diagrama</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Estado</td>
                                    <td><select id="estados" name="estados" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('estadosDiagrama', 'estado', 'id', 'estado', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Fecha Diagrama</td>
                                    <td><input id="fecha" name="fecha" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right" id="result"><input type="submit" id="save" value="Modificar Estado"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="sve">
         </form>
</body>
</html>

