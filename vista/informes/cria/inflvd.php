<?php
     session_start();
     set_time_limit(0);
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include '../../../modelsORM/call.php';
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.css" rel="stylesheet" />

  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/utils.js"></script>

<link href="/vista/css/jquery.treetable.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
<script src="/vista/js/treetable/javascripts/src/jquery.treetable.js"></script>


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
                                                       $('#personal, #acciones').selectmenu({width: 350});
                                                       $('#internos').selectmenu({width: 85});
                                                       $("#load").button().click(function(){
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              var dats = $("#upuda").serialize();
                                                                                              $.post("/modelo/informes/cria/inflvd.php",
                                                                                                     dats,
                                                                                                     function(data){
                                                                                                                    $("#dats").html(data);

                                                                                                                    });
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Configurar informe</legend>
		                 <div id="mensaje"> </div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Desde</td>
                                    <td><input type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" id="desde" name="desde"></td>
                                    <td>Hasta</td>
                                    <td><input type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" id="hasta" name="hasta"></td>
                                </tr>
                                <tr>
                                    <td>Personal</td>
                                    <td>
                                        <select id="personal" name="personal" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todo el personal</option>
                                                <?php
                                                     $query = $entityManager->createQuery("SELECT e FROM Empleado e JOIN e.categoria c WHERE c.categoria LIKE :foo and e.activo = :activo ORDER BY e.apellido");
                                                     $query->setParameter('foo', '%Lavador%');
                                                     $query->setParameter('activo', true);
                                                     $lavadores = $query->getResult();
                                                     $options = "";
                                                     foreach ($lavadores as $lavador) {
                                                             $options.= "<option value='".$lavador->getId()."'>$lavador</option>";
                                                     }
                                                     print $options;
                                                ?>
                                        </select>
                                    </td>
                                    <td>Unidades</td>
                                    <td>
                                        <select id="internos" name="internos" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todas</option>
                                                <?php
                                                     $internos = $entityManager->createQuery('SELECT u FROM Unidad u  WHERE u.activo = :activo ORDER BY u.interno');
                                                     $internos->setParameter('activo', true);
                                                     $results = $internos->getResult();
                                                     $options = "";
                                                     foreach ($results as $interno) {
                                                             $options.= "<option value='".$interno->getId()."'>$interno</option>";
                                                     }
                                                     print $options;
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tipo de accion</td>
                                    <td>
                                        <select id="acciones" name="acciones" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todas las acciones</option>
                                                <?php
                                                     $acciones = call('TipoAccionUnidad', 'findAll');
                                                     $options = "";
                                                     foreach ($acciones as $accion) {
                                                             $options.= "<option value='".$accion->getId()."'>$accion</option>";
                                                     }
                                                     print $options;
                                                ?>
                                        </select>


                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right"><input type="button" value="Cargar Informes" id="load"></td>
                                </tr>
                         </table>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" value="loadinf">
         </form>

</body>
</html>

