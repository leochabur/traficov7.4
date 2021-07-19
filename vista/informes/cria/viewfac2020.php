<?php
     session_start();
     error_reporting(E_ALL & ~E_NOTICE);
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     include_once('../../../modelsORM/controller.php');
     include_once ('../../../modelo/utils/dateutils.php');     
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     $fechaLoad = isset($_GET['fec'])?$_GET['fec']:'';
     $fechaHasta = isset($_GET['has'])?$_GET['has']:'';     

?>
<link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />

  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
        <link rel="stylesheet" href="/vista/css/estilos.css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
<script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>

    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
        <script src="/vista/js/jquery.treetable.js"></script>

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
                                                       $('.items').dataTable({
                                                                              "sScrollY": "600px",
                                                                              "bPaginate": false,
                                                                              "bScrollCollapse": true,
                                                                              "bJQueryUI": true,
                                                                              "oLanguage": {
                                                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                                                     "sInfo": "",
                                                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
                                                                               });

                      $('.detItem').button().click(function(event){

                                                              var btn = $(this);
                                                              $('#detit').empty();
                                                              $('.title').html('Item # '+ btn.data('num'));
                                                              $('#detit').load('/modelo/informes/cria/factvta.php',
                                                                          {accion: 'detof', of: btn.data('of')});
                                                              $('.title').focus();

                                                            });

                          });
</script>

<body>
<?php
     menu();
     try{
          $factura = find('FacturaVenta', $_GET['fv']); 
        }    catch(Exception $e){
                            die($e->getMessage());
                        }


    

?>
    <br><br>
 
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Factura de Venta</legend>
		                    <fieldset class="ui-widget ui-widget-content ui-corner-all">
    		                      <legend class="ui-widget ui-widget-header ui-corner-all">Datos factura</legend>
                               <table border="0" align="center" width="75%" name="tabla">
                                      <tr>
                                          <td>Cliente:
                                            <?php print $factura->getCliente();?>
                                          </td>

                                          <td>Periodo Facturado:<?php print $factura->getDesde()->format('d/m/Y').' - '.$factura->getHasta()->format('d/m/Y');?></td>
                                      </tr>
                                      <tr>
                                          <td>Importe Actual:
                                          </td>

                                          <td>$ <?php print number_format($factura->getMontoFactura(),2);?></td>
                                      </tr>                                      
                               </table>
                        <fieldset class="ui-widget ui-widget-content ui-corner-all">
                              <legend class="ui-widget ui-widget-header ui-corner-all">Items Factura</legend>
                              <table class='table items' width="75%">
                                                    <thead>
                                                      <tr>
                                                          <th>Item #</th>
                                                          <th>Tarifa Aplicada</th>
                                                          <th>Articulo Facturado</th>
                                                          <th>Cantidad Items</th>
                                                          <th>Precio Unitario</th>
                                                          <th>Precio Total</th>
                                                          <th>Ver Detalle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                      $i = 1;
                                                      foreach ($factura->getOrdenesFacturadas() as $item) {
                                                          print "<tr>
                                                                    <td>".$i++."</td>
                                                                    <td>".$item->getTarifa()."</td>
                                                                    <td>".$item->getArticulo()."</td>
                                                                    <td>".count($item->getOrdenes())."</td>
                                                                    <td>".number_format($item->getImporteUnitario(),2,',','.')."</td>
                                                                    <td>".number_format(($item->getImporteUnitario()*count($item->getOrdenes())),2,',','.')."</td>
                                                                    <td><input type='button' data-num='$i' data-of='".$item->getId()."' class='detItem' value='Ver Detalle'/></td>
                                                                 </tr>";
                                                      }
                                                    ?>
                                                    </tbody>
                    </table>
                               
                         </fieldset>                               
                         </fieldset>
                          <fieldset class="ui-widget ui-widget-content ui-corner-all">                              
                                <legend class="ui-widget ui-widget-header ui-corner-all title">Item</legend>
                                <div id="detit">
                                  
                                </div>
                           </fieldset>                         

            </fieldset>



</body>
</html>

