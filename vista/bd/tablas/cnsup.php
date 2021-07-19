<?php
     session_start(); //modulo para dar de alta una provincia
     include('../../main.php');
     include('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
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
                                                       $('#newepe').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#newepe").serialize();
                                                                                                                 $.post("/modelo/bd/tablas/cnsup.php", datos, function(data){
                                                                                                                                                                               var mje;
                                                                                                                                                                               if (data == 0){
                                                                                                                                                                                  mje = "No se ha podido agregar la novedad. Puede que el codigo ya exista!";
                                                                                                                                                                               }
                                                                                                                                                                               else{
                                                                                                                                                                                    mje = "Se ha agregado con exito la novedad en la Base de Datos!";
                                                                                                                                                                               }
                                                                                                                                                                               var mjeex = "<div class=\"ui-widget\">"+
                                                                                                                                                                                           "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                           "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                           "<strong>"+mje+"</strong></p>"+
                                                                                                                                                                                           "</div>"+
                                                                                                                                                                                           "<div>";


                                                                                                                                                                             $('#mensaje').html(mjeex);
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta Codigo de Novedad</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Codigo Novedad</td>
                                    <td><input id="type" name="type" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Novedad"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="sve">
         </form>
</body>
</html>

