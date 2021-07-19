<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     $vacio = getOpcion('empleador-default', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>

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
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
table {
	font-family:arial;
	background-color: #CDCDCD;

	font-size: 8pt;
	text-align: left;
}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#empleador option[value=<?print $vacio;?>]").attr("selected",true);
                                                       $('#empleador').selectmenu({width: 350});
                                                       $('#desde, #hasta').datepicker({dateFormat:'dd/mm/yy'});

                                                       $('#cargar').button().click(function(){
                                                                                              $.post("/modelo/rrhh/diagfer.php", {accion: 'sbana', desde: $('#desde').val(), hasta: $('#hasta').val(), emple: $('#empleador').val()}, function(data){

                                                                                                                                                                                                                                $('#sbana').html(data);
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
         <form class="cmxform" id="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Diagramar Feriados</legend>
                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleador</td>
                                    <td>
                                        <select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     $sql = "SELECT e.id, razon_social
                                                             FROM empleadores e
                                                             inner join empleadoresporestructura epe on (epe.id_empleador = e.id) and (epe.id_estructura = ".STRUCTURED.")";
                                                     $result = ejecutarSQL($sql);
                                                     while ($data = mysql_fetch_array($result)){
                                                           print "<option value='$data[id]'>$data[razon_social]</option>";
                                                     }
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                     Diagramar Desde el:
                                    </td>
                                    <td><input id="desde" name="desde" size="20" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td>Hasta el:</td>
                                    <td><input id="hasta" name="hasta" size="20" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td><input type="button" id="cargar" value="Cargar Diagrama"/> </td>
                                </tr>
                         </table>
                         <div id="sbana">
                         </div>
            </fieldset>
         </form>
</body>
</html>

