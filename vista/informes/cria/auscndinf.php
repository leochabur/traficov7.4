<?php
     session_start();
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>

<link href="/vista/css/jquery.treetable.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
<script src="/vista/js/treetable/javascripts/src/jquery.treetable.js"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#cargar").button().click(function(){
                                                                                              var dats = $("#upuda").serialize();
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/cria/auscndinf.php", dats, function(data){
                                                                                                                                                                 $('#dats').html(data);
                                                                                                                                                                 });
                                                       });

                                                       $("#str option[value=1]").attr("selected", "selected");
                                                       $('#str, #type').selectmenu({width: 350});
                                                       
                                                       $.mask.definitions['%']='[01]';
                                                       $(".range").mask("%9/2099");

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Resumen de Novedades</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Estructura</td>
                                    <td colspan="3">
                                        <select id="str" name="str" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                                ?>
                                        </select>
                                    </td>
                                    </td>
                                    </tr>
                                    <!--tr>
                                        <td>Organizar Datos</td>
                                        <td>X Conductor<input type="radio" name="tpo" value="c"></td>
                                        <td></td>
                                        <td>X Novedad<input type="radio" name="tpo" value="n" checked></td>
                                    </tr-->
                                    <tr>
                                        <td>Incluir...</td>
                                        <td>Solo Conductores<input type="radio" name="inc" value="cn" checked></td>
                                        <td></td>
                                        <td>Todo el personal<input type="radio" name="inc" value="tp"></td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="6" class="ui-widget ui-widget-content  ui-corner-all range" placeholder="mm/yyyy"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="6" class="ui-widget ui-widget-content  ui-corner-all range" placeholder="mm/yyyy"></td>
                                    </tr>
                                    <tr>
                                    <td colspan="4" align="right">
                                        <input type="button" value="Cargar informe" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="reskm">
            <input type="hidden" name="order" id="order">
         </form>

</body>
</html>

