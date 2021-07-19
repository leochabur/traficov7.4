<?php
     session_start();
     set_time_limit(0);
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include '../../../modelsORM/manager.php';
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>
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
                                                       $("#cargar").button().click(function(){
                                                                                              var dats = $("#upuda").serialize();
                                                                                              alert(dats);
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/cria/auscnd.php", {accion:'reskm', desde: $('#desde').val(), hasta: $('#hasta').val(), str: $('#str').val()}, function(data){  $('#dats').html(data);
                                                                                                                                                                           });
                                                       });
                                                       $('#gpo').selectmenu({width: 350});
                                                       $("#gpo").change(function(){
                                                                                    var id = $(this).val();
                                                                                    $.post("/modelo/informes/cria/auscnd.php", {accion:'ldnv', gpn:id}, function(data){
                                                                                                                                                                       $('#novs').html(data);
                                                                                                                                                                       $('#noves').selectmenu({width: 350});
                                                                                                                                                                           });
                                                                                    });
                                                                                    
                                                       $("#svgpo").button().click(function(){
                                                                                              var dats = $("#upuda").serialize();
                                                                                              $.post("/modelo/informes/cria/auscnd.php",
                                                                                                     dats,
                                                                                                     function(data){
                                                                                                                    var response = $.parseJSON(data);
                                                                                                                    if (response.error){
                                                                                                                       alert(response.message);
                                                                                                                    }
                                                                                                                    addElementSelect('gpo', response.key, response.value, 350);
                                                                                                                    });
                                                       });
                                                       
                                                        $.post("/modelo/informes/cria/auscnd.php", {accion: 'ldnvtxt'}, function(data){
                                                                                                                                       $('#novsd').html(data);
                                                                                                                                       $('#novesd').selectmenu({width: 350});
                                                                                                                                       });
                                                       $("#delgpo").button().click(function(){
                                                                                              var id = $(this).attr('id');
                                                                                              $.post("/modelo/informes/cria/auscnd.php",
                                                                                                     {accion: id, gpo: $("#gpo").val()},
                                                                                                     function(data){
                                                                                                                    alert(data);
                                                                                                                    }
                                                                                                     );
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Nuevo Grupo de informe</legend>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Descripcion</legend>
                         <table border="0" align="center" width="65%" name="tabla">
                                <tr>
                                    <td>Nuevo Grupo informe</td>
                                    <td>
                                        <input type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" name="grpo" id="grpo">
                                        <input type="button" value="Guardar grupo" id="svgpo">
                                    </td>
                                </tr>
                         </table>
                                                  <div id="resss"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="svgpo">
         </form>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Configurar informes</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Agregar/Quitar novedades al grupo</legend>
                         <table border="0" align="center" width="65%" name="tabla">
                                <tr>
                                    <td>Grupos informe</td>

                                    <?php  ///    die("ok :".$entityManager);

                                                   //  die("ok");
                                        //            $productRepository = $entityManager->getRepository('GrupoNovedad');
                                          //          $products = $productRepository->findAll();
                                                   // die("prodddddddddddd ".$products);
                                            //        foreach ($products as $product) {
                                                //            print $product->getNombre();
                                              //      }

                                    ?>

                                    <td>
                                        <select id="gpo" name="gpo" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Seleccione una opcion</option>
                                                <?php
                                                   //  die("ok");
                                                   die("manager $entityManager");
                                                    $productRepository = $entityManager->getRepository('GrupoNovedad');
                                                    
                                                    $products = $productRepository->findAll();
                                                   // die("prodddddddddddd ".$products);
                                                    foreach ($products as $product) {
                                                            print "<option value=".$product->getId().">".$product->getNombre()."</option>";
                                                    }
                                                ?>
                                        </select>
                                        <input type="button" value="Eliminar Grupo" id="delgpo">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Novedades incluidas en el grupo</td>
                                    <td id="novs"></td>
                                </tr>
                                <tr>
                                    <td>Novedades disponibles</td>
                                    <td id="novsd"></td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="reskm">
            <input type="hidden" name="order" id="order">
         </form>

</body>
</html>

