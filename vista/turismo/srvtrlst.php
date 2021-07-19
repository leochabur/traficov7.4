<?php
     session_start();

     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include_once('../../modelsORM/controller.php');

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
  <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>


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
                                                        $('#clientes').selectmenu({width: 350});
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().on( "click", function(){
                                                                                              var form = $('.myform');
                                                                                              var url = form.attr('id');
                                                                                              var data = form.serialize();
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");

                                                                                              $.post(url,
                                                                                                     data,
                                                                                                     function(response){
                                                                                                        $('#dats').html(response);
                                                                                                      
                                                                                                     }).fail(function(data) {
                                                                                                                      
                                                                                                                            alert( "error" );
                                                                                                                          });
                                                                                          });

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="/modelo/turismo/nwprs.php" class="myform">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Lista de Presupuestos</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Presupuestos</legend>
                         <table border="0" align="center" width="100%" name="tabla">
                                <tr>
                                    <td>
                                        <select id="clientes" name="clientes" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todos los clientes</option>

                                                <?php
                                                     print clientesOptions();
                                                ?>
                                        </select>
                                    </td>
                                    <td> <span class="ui-widget">Pendiente Facturar <input type="checkbox" class="ui-widget ui-widget-content ui-corner-all" name="pendiente"> </span> </td>
                                    <td><input id="desde" name="desde"  type="text" size="20" placeholder="Fecha desde" class="ui-widget ui-widget-content ui-corner-all"></td>
                                    <td></td>
                                    <td><input id="hasta" name="hasta" type="text" size="20" placeholder="Fecha hasta" class="ui-widget ui-widget-content ui-corner-all"></td>
                                    <td><input id="numero" name="numero" type="text" size="20" placeholder="Numero presupuesto" class="ui-widget ui-widget-content ui-corner-all"></td>         
                                    <td colspan="4" align="right">
                                        <input type="button" value="Cargar Presupuestos" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="listp">
         </form>
</body>
</html>
