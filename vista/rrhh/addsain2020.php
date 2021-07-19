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
                                                       $('#desde,#hasta').datepicker({dateFormat:'yy-mm-dd'});
                                                       $("#cargar").button().click(function(){
                                                                                              $.post("/modelo/rrhh/modvcemp.php", {accion:'salin', days: $('#days').val(), emple:$('#emples').val()}, function(data){
                                                                                                                                                                                                                     if (data == 1)
                                                                                                                                                                                                                        alert('Se actualizo el saldo inical correctamente');
                                                                                                                                                                                                                     else
                                                                                                                                                                                                                         alert('No se ha podido dar de alta el saldo inicial');
                                                                                                                                                                                                                               });
                                                       });
                                                       $('#emples').selectmenu({width: 350});
                                                       $('#detalle').selectmenu({width: 350});
                                                       $('#puesto').selectmenu({width: 350});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Ingresar Saldo Inicial</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all"></legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Empleados</td>
                                    <td>
                                        <select id="emples" name="emples" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     $sql="SELECT ch1.id_empleado, upper(if(em.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat(ch1.apellido, ', ',ch1.nombre, '(',em.razon_social,')'))) as empleado
                                                           FROM empleados ch1
                                                           inner join empleadores em on em.id = ch1.id_empleador
                                                           where (ch1.activo)and (not borrado) and (em.id = 1) and (em.activo) and (ch1.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid]))
                                                           order by apellido, nombre";
                                                     $result = ejecutarSQL($sql);
                                                     while($row = mysql_fetch_array($result)){
                                                                print "<option value='$row[id_empleado]'>".utf8_decode($row[empleado])."</option>";
                                                     }
                                                     mysql_free_result($result);
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Detalle</td>
                                    <td>
                                        <select id='detalle'>
                                                <?php
                                                    /* $sql="SELECT detalle FROM vacacionespersonal group by detalle";
                                                     $result = ejecutarSQL($sql);
                                                     while($row = mysql_fetch_array($result)){   */
                                                                print "<option value='Saldo Inicial'>Saldo Inicial</option>";
                                              /*       }
                                                     mysql_free_result($result);*/
                                                ?>
                                        </select>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td>Saldo Inicial</td>
                                        <td><input type="text" size="5" id="days" name="days"></td>
                                    </tr>
                                    <tr>
                                    <td></td>
                                    <td align="right">
                                        <input type="button" value="Cargar Saldo Inicial" id="cargar">
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

