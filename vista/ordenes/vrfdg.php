<?php
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />
    
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
#example{
	font-size:0.8em;
	color:#000000;
}
#example tbody tr:nth-child(odd){
    background: #D0D0D0;

}

#example tbody tr:nth-child(even){
    background: #FFFFFF;

}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#load").button().click(function(){
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              var ret = 0;
                                                                                              if ($('#tavuel').is(":checked")){
                                                                                                 ret = 1;
                                                                                              }
                                                                                              $.post("/modelo/ordenes/vrfdg.php", {accion:'load', txtde: $("#nov_dest option:selected").text(), txtor: $("#nov_orig option:selected").text(), desde: $('#desde').val(), nde: $('#nov_dest').val(), nor: $('#nov_orig').val(), vta: ret}, function(data){
                                                                                                                                                                                                                                 $('#dats').html(data);
                                                                                                                                                                                                                                 });
                                                       });
                                                       $('select').selectmenu({width: 350});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Verificar Diagrama</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar datos</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Fecha Diagrama</td>
                                    <td colspan="3"><input id="desde" name="desde"  class="ui-widget-content  ui-corner-all"  type="text" size="20"></td>
                                </tr>
                                <tr>
                                    <td>Transformar:</td>
                                    <td>
                                        <select id="nov_orig" name="nov_orig" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('cod_novedades', 'nov_text', 'id', 'nov_text', "(id_estructura = $_SESSION[structure]) and (cod_origen)");
                                                ?>
                                        </select>
                                    </td>
                                    <td>En:</td>
                                    <td>
                                        <select id="nov_dest" name="nov_dest" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('cod_novedades', 'nov_text', 'id', 'nov_text', "(id_estructura = $_SESSION[structure]) and (cod_destino)");
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <!--tr>
                                    <td>Verificar a la inversa</td>
                                    <td colspan="3"><input type="checkbox" class="ui-widget-content  ui-corner-all" id="tavuel"></td>
                                </tr-->
                                <tr>
                                    <td colspan="4" align="right"><input id="load" type="button" value="Cargar ordenes"></td>
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

