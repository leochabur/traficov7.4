<?php
     session_start();
     include('../main.php');
     include('../paneles/viewpanel.php');
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
.button-small{font-size: 62.5%;}
div#users-contain {margin: 20px 0; font-size: 62.5%;}
div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#dominio").mask("aaa-999");
                                                       $("#anio").mask("9999");
                                                       $("#interno").mask("9999");
                                                       $("#cantas").mask("99");
                                                       $("#consumo").mask("99");
                                                       $("#save").button();
                                                       $('select').selectmenu({width: 350});
                                                       $('#newuda').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#newuda").serialize();
                                                                                                                 $.post("/modelo/segvial/altauda.php", datos, function(data){
                                                                                                                                                                             obj = JSON.parse(data);

                                                                                                                                                                             if (obj === "iebd"){
                                                                                                                                                                                var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                          "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                          "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                          "<strong>Numero de Interno existente en la Base de Datos!</strong></p>"+
                                                                                                                                                                                          "</div>"+
                                                                                                                                                                                          "<div>";
                                                                                                                                                                                $('#mensaje').html(mje);
                                                                                                                                                                             }
                                                                                                                                                                             else{
                                                                                                                                                                                  if (obj == 0){
                                                                                                                                                                                     var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                               "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                               "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                               "<strong>Se han producido errores al intentar guardar la unidad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                               "</div>"+
                                                                                                                                                                                               "<div>";
                                                                                                                                                                                     $('#mensaje').html(mje);
                                                                                                                                                                                  }
                                                                                                                                                                                  else{
                                                                                                                                                                                   alert(obj);
                                                                                                                                                                                  }
                                                                                                                                                                             }
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
         <form id="newuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta de Unidad</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Propietario</td>
                                    <td><select id="propietario" name="propietario" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED);
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Interno</label></td>
                                    <td><input id="interno" name="interno" size="4" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Dominio</td>
                                    <td><input id="dominio" name="dominio" size="8" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <?
                                  if ($_SESSION['permisos'][4] > 2){
                                     print '<tr>
                                                <td WIDTH="20%">Marca</td>
                                                <td><input id="marca" name="marca" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                                <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">Modelo</td>
                                               <td><input id="modelo" name="modelo" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">Motor</td>
                                               <td><input id="motor" name="motor" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">'.htmlentities('Año').'</td>
                                               <td><input id="anio" name="anio" size="4" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Cant. Asientos</label></td>
                                               <td><input id="cantas" name="cantas" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Consumo c/ 100 Km</label></td>
                                               <td><input id="consumo" name="consumo" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Servicios</label></td>
                                               <td>Video<input name="video" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Bar<input name="bar" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Ba&ntilde;o<input name="banio" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                               <td></td>
                                           </tr>
                                           <td WIDTH="20%">Tipo Unidad</td>
                                               <td><select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">'.
                                                     armarSelect('tipounidad', 'tipo', 'id', 'tipo', "(id_estructura = ".STRUCTURED.")",1).'
                                                   </select>
                                               </td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                           <td WIDTH="20%">Calidad</td>
                                           <td><select id="calidad" name="calidad" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">'.
                                                     armarSelect('calidadcoche', 'calidad', 'id', 'calidad', "(id_estructura = ".STRUCTURED.")",1).'
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Unidad"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="sve">
         </form>
</body>
</html>

