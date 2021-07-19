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

#newepe .error, #updcty .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#save, #updc').button();
                                                       $('select').selectmenu({style:'popup', width: 350});
                                                       $('#newepe').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#newepe").serialize();
                                                                                                                 $.post("/modelo/bd/tablas/cityup.php", datos, function(data){
                                                                                                                                                                                var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                        "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                        "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                        "<strong>Se ha grabado con exito la Ciudad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                        "</div>"+
                                                                                                                                                                                        "<div>";
                                                                                                                                                                                $('#mensaje').html(mje);
                                                                                                                                                                                $( "#city").val('');
                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                                                                              
                                                       $('#updcty').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#updcty").serialize();

                                                                                                                 $.post("/modelo/bd/tablas/cityup.php", datos, function(data){

                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                                                        $('#citys').change(function(){
                                                                                      var c = $(this).val();
                                                                                      $.post("/modelo/bd/tablas/cityup.php",
                                                                                             {accion:'load', city: c},
                                                                                             function(data){
                                                                                                            var response = $.parseJSON(data);
                                                                                                            if (response.status){
                                                                                                               $('#lati').val(response.lati);
                                                                                                               $('#long').val(response.long);
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
         <form id="newepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta de Ciudad</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Provincia</td>
                                    <td><select id="pcia" name="pcia" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('provincias', 'provincia', 'id', 'provincia', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Ciudad</td>
                                    <td><input id="city" name="city" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Ciudad"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="sve">
            </form>
            <form id="updcty">
            <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar datos Ciudad</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Ciudades</td>
                                    <td><select id="citys" name="citys" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "id_estructura = $_SESSION[structure]");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                <tr>
                                    <td WIDTH="20%">Latitud</td>
                                    <td><input id="lati" name="lati" class="required ui-widget ui-widget-content  ui-corner-all"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Longitud</td>
                                    <td><input id="long" name="long" class="required ui-widget ui-widget-content  ui-corner-all"/></td>
                                    <td></td>
                                </tr>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="updc" value="Modificar Ciudad"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="upd">
            </form>

</body>
</html>

