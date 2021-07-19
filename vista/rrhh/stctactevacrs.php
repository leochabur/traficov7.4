<?php
     session_start();
     include('../main.php');
     include_once('../../controlador/bdadmin.php');
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

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#detalle').selectmenu({width: 250});
                                                       $('#cargar').button().click(function(){
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/rrhh/stctactevacrs.php", {accion: 'load', det:$('#detalle').val()}, function(data){$('#data').html(data);});
                                                                                              });


                          });

</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Vacaciones Personal</legend>
                         <div align="center">
                         <select id="detalle">
                           <option value="99">Todos</option>
                         <?php
                              $conn=conexcion();
                              $result = mysql_query("SELECT anio, detalle FROM vacacionespersonal group by anio order by detalle", $conn);
                              while ($data = mysql_fetch_array($result))
                                    print "<option value='$data[0]'>$data[1]</option>";
                         ?>
                         </select>
                         <input type="button" value="Cargar" id="cargar">
                         </div>
                         <br>
                         <div id="data"> </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="load">
         </form>
</body>
</html>

