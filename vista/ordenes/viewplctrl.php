<?php
     session_start(); //modulo para dar de alta una provincia
     include('../main.php');
     include('../paneles/viewpanel.php');
     include_once('../../controlador/bdadmin.php');
     include_once('../../modelsORM/call.php');  
    // define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

#newepe .error{
	font-size:0.8em;
	color:#ff0000;

#vrfdgma .error{
	font-size:0.8em;
	color:#ff0000;
}

table tr td {
  padding: 10px 5px;

}

table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){

                                                       $('#pl').selectmenu({width: 350});
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('.prev').button().click(function(){
                                                                                    var fec = $('#fecha').val();
                                                                                    var cliente = $('#clientesPrev').val();
                                                                                    if (fec)
                                                                                    {
                                                                                      var btn = $(this);

                                                                                        $('#mje').html("<br><div align='center'><img  alt='cargando' src='../ajax-loader.gif' /><br></div>");
                                                                                        $.post("/vista/ordenes/genplctrl.php", 
                                                                                               $('#newgepe').serialize(), 
                                                                                               function(data){
                                                                                                              $('#mje').html(data);
                                                                                                            });
                                                                                      
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                          alert('Debe seleccionar una fecha!');
                                                                                    }

                                                                                                         
                                                                              });
                          });

</script>

<body>
<?php
     menu();

     $options = "";
     $conn = conexcion();
     $sql = "SELECT id, upper(razon_social)
             FROM clientes
             where id_estructura = $_SESSION[structure] and activo
             ORDER BY razon_social";
     $result = mysql_query($sql, $conn);
     while ($row = mysql_fetch_array($result)){
           $options.= "<option value='$row[0]'>$row[1]</option>";
     }

     $planillas = call('PlanillaDiaria','findAll');
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newgepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Generar planilla supervisor</legend>
		                 <div id="mensaje"></div>
                         <table border="0" class="td" align="center" name="tabla">
                                <tr >
                                    <td>Fecha Planilla</td>
                                    <td><input type='text' id="fecha" name="fecha" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                </tr>
                                <tr>
                                    <td>Imprimir completa</td>
                                    <td><input type='checkbox' name="complete" class="required ui-widget ui-widget-content  ui-corner-all"/></td>
                                </tr>
                                <tr> 
                                    <td>
                                        Planillas Generadas
                                    </td>        
                                    <td>
                                        <select id="pl" name="pl" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option>Seleccione una opcion</option>
                                                <?php
                                                    
                                                    foreach ($planillas as $value) {
                                                        print "<option value='".$value->getId()."'>$value</option>";
                                                    }
                                                ?>
                                        </select>
                                    </td>                       
                                    <td colspan="2" align="right">
                                        <input type="button" class="prev" data-send="gen" id="gen" value="Generar Planilla"/> 
                                      </td>
                                </tr>
                         </table>
            </fieldset>
            <div id="mje"></div>
            <input type="hidden" name="accion" value="genpl">
         </form>
    </div>
</body>
</html>

