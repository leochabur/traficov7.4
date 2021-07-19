<?php
     session_start();
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     $route = ($_SESSION['structure'] == 2?'diagdiasur':'diagdiav2');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>    
    <link href="/vista/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />
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
                                                       $('#desde,#hasta').datepicker({dateFormat:'yy-mm-dd'});
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/trafico/<?php print $route;?>.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       $('select').selectmenu({width: 350});
                                                       $( 'body' ).mousemove(function( event ){
                                                                        var cy = event.pageY;
                                                                        $('#posy').val(cy);
                                                                      });


                          });
                          $(document).bind('keydown', disable_fresh);
                          
  function disable_fresh(e){
           if (e.which == 116){
              e.preventDefault();
              $('#data').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
              var datos = $("#upuda").serialize();
              $.post("/modelo/informes/trafico/<?php print $route;?>.php", datos, function(data){
                                                                                   $('#data').html(data);
                                                                                   $('body').animate({
                                                                                                      scrollTop: ($('#posy').val()-100)+'px'
                                                                                                      },
                                                                                                      0);

                                                                                   });
           }
  };
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Diagrama de Trabajo</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar por:</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Conductor</td>
                                    <td>
                                    <select id="cond" name="cond" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                                <?php
                                                     armarSelectCond(STRUCTURED);
                                                ?>
                                        </select>
                                    </td>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    <td>
                                        <input type="button" value="Cargar Diagrama" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data">
                              <?//include_once("../../../modelo/informes/trafico/diagdia.php");?>
                         </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type='hidden' id='posy' name='posy'>
         </form>

</body>
</html>

