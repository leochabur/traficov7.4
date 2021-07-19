<?php
     session_start();
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
      error_reporting(0);
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
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
                                                                                              $.post("/modelo/informes/rrhh/kmxcond.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       $('#cond').selectmenu({width: 350});
                                                       $('#emp').selectmenu({width: 250});
                                                       $('#emp').change(function(){
                                                                                  $.post('/modelo/informes/rrhh/kmxcond.php',
                                                                                        {accion: 'loadcnd', emp: $(this).val()},
                                                                                        function(data){
                                                                                                    $('#cond').empty();
                                                                                                    $('#cond').html(data);
                                                                                                    $('#cond').selectmenu({width: 250});
                                                                                        });
                                                       });

                          });
</script>

<body>
<?php
     menu();
     
                                                    $sql = 'select id, upper(razon_social) as razon_social
FROM empleadores
WHERE id_estructura = '.STRUCTURED.'
UNION
select e.id, upper(razon_social) as razon_social
FROM empleadores e
INNER JOIN empleadoresporestructura exe ON exe.id_estructura = '.STRUCTURED.' AND exe.id_empleador = e.id
WHERE exe.id_estructura = '.STRUCTURED.'
ORDER BY razon_social';

$conn = conexcion(true);

                                                    $result = mysqli_query($conn, $sql) or die("error ".mysql_error($conn));

?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Kilometros por conductor</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar por:</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Empleador</td>
                                    <td>
                                      <select id="emp" name="emp" class="ui-widget ui-widget-content  ui-corner-all">
                                        <option value='0'>Seleccione un empleador</option>
                                      <?php

                                                    while ($row = mysqli_fetch_array($result)){
                                                      print "<option value='$row[0]'>".htmlentities($row[1])."</option>";
                                                    }
                                      ?>
                                    </select>
                                    </td>

                                    <td>Conductor</td>
                                    <td>
                                    <select id="cond" name="cond" class="ui-widget ui-widget-content  ui-corner-all">

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
         </form>

</body>
</html>

