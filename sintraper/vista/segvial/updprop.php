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
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/segvial/updprop.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       
                                                       $("#cambiar").button().click(function(){
                                                                                              var empl = $('#emples').val();
                                                                                              var cch = $('#interno').val();
                                                                                              var desde = $('#desde').val();
                                                                                              var hora = $('#hora').val();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/segvial/updprop.php", {accion: 'updprop', emp: empl, int: cch, des: desde, hor: hora}, function(data){$('#data').html(data);});
                                                       });
                                                       
                                                       $.mask.definitions['H']='[012]';
                                                       $.mask.definitions['N']='[012345]';
                                                       $.mask.definitions['n']='[0123456789]';
                                                       $(".hora").mask("Hn:Nn");
                                                       $('#emples').selectmenu({width: 350});
                                                       $('#interno').selectmenu({width: 100});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cambiar Propietario Unidad</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Datos</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Numero de Interno</td>
                                    <td><select id="interno" name="interno" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Internos</option>
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "((activo) and (id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid])))");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="button" value="Cargar" id="cargar">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"><div id="data"></div></td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td>Nuevo Empleador</td>
                                    <td>
                                        <select id="emples" name="emples" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED);
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                    <tr>
                                    <td>Aplicar desde el:</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>A las:</td>
                                    <td><input id="hora" name="hora" class="hora" type="text" size="6"></td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3"><input type="button" value="Cambiar Propietario" id="cambiar"></td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="load">
         </form>

</body>
</html>

