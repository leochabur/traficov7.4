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
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />
        <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>


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
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().click(function(){
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/rrhh/modelaccessos.php", 
                                                                                                    {accion:'reskm', 
                                                                                                     desde: $('#desde').val(), 
                                                                                                     hasta: $('#hasta').val(),
                                                                                                     sector: $('#sector').val()}, 
                                                                                                     function(data){  
                                                                                                                        $('#dats').html(data);
                                                                                                    });
                                                       });
                                                       $('#sector').selectmenu({width: 350});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Informe Accesos</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Desde</td>
                                    <td>Hasta</td>
                                </tr>
                                <tr>
                                    <td><input id="desde" name="desde" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td><input id="hasta" name="hasta" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td>Sector</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                            <select id="sector" name="sector">
                                                    <option value="0">Todos los sectores</option>
                                                    <?php

                                                        armarSelect("sector", "descripcion", "id", "descripcion", "activo");

                                                    ?>
                                            </select>
                                    </td>
                                </tr>
                                <td>
                                    <td>
                                        <input type="button" value="Cargar Registros" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
         </form>

</body>
</html>

