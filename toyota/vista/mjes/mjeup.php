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
                                                                                                                 $.post("/modelo/bd/tablas/cityup.php", datos, function(data){
                                                                                                                                                                               if (data != 1)
                                                                                                                                                                                  alert(data);
                                                                                                                                                                             $('#mensaje').html(data);
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
</body>
</html>

