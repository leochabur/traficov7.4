<?php
     session_start();
     include('../../controlador/bdadmin.php');
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>

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
                          <?
                                                if (isset($_GET['emp'])){
                                                        print "$('#emples option[value=$_GET[emple]]').attr('selected', 'selected');
                                                               $('#emples, #diag').selectmenu({width: 450});";
                                                }
                                                else{
                                                     print "$('#emples, #diag').selectmenu({width: 450});";
                                                }

                          ?>
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#igual").button().click(function(){$("#hasta").val($("#desde").val());});
                                                       $("#cargar").button().click(function(){
                                                                                              var des = $("#desde").val();
                                                                                              var has = $("#hasta").val();
                                                                                              var emp = $("#emples").val();
                                                                                              var dia = $("#diag").val();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/rrhh/certlist.php", {accion:'lcert', emple: emp, desde:des, hasta:has, diag:dia}, function(data){$('#data').html(data);});
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Listado de Certficados</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar certificados:</legend>
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
                                                           where (ch1.activo) and (em.activo) and (ch1.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid]))
                                                           order by apellido, nombre";
                                                     $result = ejecutarSQL($sql);
                                                     while($row = mysql_fetch_array($result)){
                                                                print "<option value='$row[id_empleado]'>".htmlentities($row[empleado])."</option>";
                                                     }
                                                     mysql_free_result($result);
                                                ?>
                                        </select>
                                    </td>
                                    <td></td><td></td>
                                    </tr>
                                    <tr>
                                        <td>Diagnosticos</td>
                                        <td>
                                            <select id="diag" name="diag" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                            <?php
                                              armarSelect("diagnosticos", 'diagnostico', 'id', 'diagnostico', '0');
                                            ?>
                                            </select>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td><input type="button" id="igual" value="<==>"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    <td>
                                        <input type="button" value="Cargar Certificados" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data">

                         </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
         </form>

</body>
</html>

