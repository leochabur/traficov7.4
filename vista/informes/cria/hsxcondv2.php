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
      <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
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
                                                                                              $.post("/modelo/informes/cria/hsxcond_v1.0.php", {accion:'hscond', desde: $('#desde').val(), hasta: $('#hasta').val(), emple:$('#emples').val()}, function(data){
                                                                                                                                                                                                                               $('#dats').html(data);
                                                                                                                                                                                                                               });
                                                       });
                                                       $("#cargar-r1").button().click(function(){
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/cria/ultimo_hs.php", {accion:'hscond', desde: $('#desde').val(), hasta: $('#hasta').val(), emple:$('#emples').val()}, function(data){
                                                                                                                                                                                                                               $('#dats').html(data);
                                                                                                                                                                                                                               });
                                                       });
                                                       $('#emples').selectmenu({width: 350});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Hs por conductor</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar ordenes</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Empleados</td>
                                    <td>
                                        <select id="emples" name="emples" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todos</option>
                                                <?php
                                                     $sql="SELECT ch1.id_empleado, upper(if(em.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat(ch1.apellido, ', ',ch1.nombre, '(',em.razon_social,')'))) as empleado
                                                           FROM empleados ch1
                                                           inner join empleadores em on em.id = ch1.id_empleador
                                                           where (ch1.activo) and (em.id = 1) and (em.activo) and (ch1.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid]))
                                                           order by apellido, nombre";
                                                     $result = ejecutarSQL($sql);
                                                     while($row = mysql_fetch_array($result)){
                                                                print "<option value='$row[id_empleado]'>".htmlentities($row[empleado])."</option>";
                                                     }
                                                     mysql_free_result($result);
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    <td>
                                        <input type="button" value="Cargar Diagrama" id="cargar">
                                    </td>
                                    <td>
                                        <input type="button" value="Release 1" id="cargar-r1">
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

