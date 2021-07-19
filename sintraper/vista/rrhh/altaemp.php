<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '/');
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
#commentForm .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#cuit").mask("99-99999999-9"),
                                                       $('#create').button();
                                                       $('.fecha').datepicker();
                                                       $('.fechanaci').datepicker({changeMonth: true,
                                                                                   changeYear: true
                                                                                   });
                                                       $('select').selectmenu({style:'popup', width: 350});
                                                        $("#commentForm").validate({
                                                                                    submitHandler: function(e){
                                                                                                               var datos = $("#commentForm").serialize();
                                                                                                               $.post("/modelo/rrhh/altaemp.php", datos, function(data) {

                                                                                                                                                                        var obj = JSON.parse(data);
                                                                                                                                                                        var mjetxt = "No se ha podido guardar el empleado en la Base de Datos";
                                                                                                                                                                        if (obj > 0){
                                                                                                                                                                                       mjetxt = "Se ha guardado con exito el empleado en la Base de Datos";
                                                                                                                                                                        }
                                                                                                                                                                                    var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                               "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                               "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                               "<strong>"+mjetxt+"!</strong></p>"+
                                                                                                                                                                                               "</div>"+
                                                                                                                                                                                               "<div>";
                                                                                                                                                                                    $("#result").html(mje);
                                                                                                                                                                        });
                                                                                                               $( "#contacts tbody").html('');
                                                                                                               num = 1;
                                                                                                               }
                                                                                    });

                                                       });
</script>

<body>
<?php
     menu();
?>
    <br><br>
    <div id="result"></div>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta de Personal</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleador</td>
                                    <td><select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED);
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <?
                                  if ($_SESSION['modaf'] == 1){
                                     print'<tr>
                                            <td WIDTH="20%"><font color="#FF0000"><b>Afectar a estructura...</b></font></td>
                                            <td><select id="struct" name="struct" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">';
                                            armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                     print '</select>
                                              </td>
                                              <td>
                                              </td>
                                              </tr>';
                                  }
                                ?>
                                <tr>
                                    <td WIDTH="20%">Puesto</td>
                                    <td><select id="puesto" name="puesto" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     if ($_SESSION['modaf'] == 1){
                                                        armarSelect('cargo', 'descripcion', 'id', 'upper(descripcion)', "(id_estructura = ".STRUCTURED.")");
                                                     }
                                                     else{
                                                          armarSelect('cargo', 'descripcion', 'id', 'upper(descripcion)', "(id_estructura = ".STRUCTURED.") and (id = 1)");
                                                     }
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Legajo</label></td>
                                    <td><input id="legajo" name="legajo" size="8" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Apellido</td>
                                    <td><input id="ape" name="ape" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td><input id="nom" name="nom" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <?php
                                if ($_SESSION['modaf'] == 1){
                                print '<tr>
                                    <td WIDTH="20%">Nacionalidad</td>
                                    <td><select id="nacion" name="nacion" title="Please select something!"  class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">';

                                                     armarSelect('nacionalidades', 'nacionalidad', 'id_nacionalidad', 'upper(nacionalidad)', "");

                                  print '</select>
                                    </td></tr>';
                                print'<tr>
                                    <td WIDTH="20%">Sexo</td>
                                    <td>Masculino<input checked value="m" type="radio" name="sexo"> Femenino <input value="f" type="radio" name="sexo"></td>
                                    <td></td>
                                </tr>
                                </tr>
                                      <tr>
                                    <td WIDTH="20%">Tipo Doc</td>
                                    <td><select name="tipodoc">
                                                <option value="DNI">DNI</option>
                                                <option value="LC">LC</option>
                                                <option value="LE">LE</option>
                                                <option value="CED">CED</option>
                                         </select></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Num Doc</label></td>
                                    <td><input id="nrodoc" name="nrodoc" size="8" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                      <tr>
                                    <td WIDTH="20%">Direccion</td>
                                    <td><input id="dire" name="dire" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td><input id="tele" name="tele" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Localidad</td>
                                    <td><select id="ciudad" name="ciudad" title="Please select something!"  class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">';

                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");

                                  print '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Nacimiento</td>
                                    <td><input id="fnac" name="fnac" class="required ui-widget ui-widget-content  ui-corner-all fechanaci" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Inicio Relacion Laboral</td>
                                    <td><input id="fini" name="fini" class="required ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">CUIL</td>
                                    <td><input id="cuit" name="cuit" class="{required:true} ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>';}
                                ?>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="create" value="Guardar Empleado"/> </td>
                                </tr>
                         </table>
            </fieldset>
         </form>
</body>
</html>

