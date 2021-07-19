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


        <link rel="stylesheet" href="/vista/css/estilos.css" />

<script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>

    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>

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
                                                       window.cambio = false;
                                                       $('#fecha, #hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('#tipos').selectmenu({width: 350});

                                                       $("#load").button().click(function(event){
                                                                                              event.preventDefault();
                                                                                               $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>"); 
                                                                                              var tf = $(this).data('tf');
                                                                                              if (tf == 'f')
                                                                                              {
                                                                                                      var data = $("#addord").serialize();                                                                                             
                                                                                                      $.post("/modelo/informes/cria/factvta.php", 
                                                                                                             data, 
                                                                                                             function(data){
                                                                                                                            $('#dats').html(data);
                                                                                                             });
                                                                                              }
                                                                                              else
                                                                                              {                                                                                   
                                                                                                  var data = $("#addord").serialize();
                                                                                             
                                                                                                  $.post("/modelo/informes/cria/factvta.php", 
                                                                                                         data, 
                                                                                                         function(data){
                                                                                                                        $('#dats').html(data);
                                                                                                         });
                                                                                                }
                                                                                             
                                                       });                                                     
                                                       <?php
                                                              if ($fechaLoad)
                                                                print '$("#load").click();';
                                                       ?>
                      $('.detItem').button().click(function(event){

                                                              var btn = $(this);
                                                              $('#detap').remove();
                                                              var dialogDet = $('<div id=\"detaItem\"></div>').appendTo('body');
                                                              dialogDet.dialog({
                                                                                title: 'Detalle item',
                                                                                width:900,
                                                                                height:400,
                                                                                modal:true,
                                                                                autoOpen: false,
                                                                                close : function(){
                                                                                                  $(this).dialog('destroy').remove();
                                                                                                  if (window.cambio)
                                                                                                      location.reload();
                                                                                }  
                                                                            });
                                                              dialogDet.load('/modelo/informes/cria/factvta.php',
                                                                          {accion: 'detIt', fv: btn.data('fact'), art: btn.data('art')},
                                                                          function (){ 
                                                                                       });
                                                              dialogDet.dialog('open');
                                                            });

                          });
</script>

<body>
<?php
     menu();
     try{
          $factura = find('FacturaVenta', $_GET['fv']); 
          //$cliente = $factura->getCliente();
          $factCliente = facturacionCliente($factura->getCliente()->getId());
        }    catch(Exception $e){
                            die($e->getMessage());
                        }


    

?>
    <br><br>
 
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Agregar Items</legend>
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
                                          <td>Observacion:
                                          </td>

                                          <td><?php print $factura->getDescripcion();?></td>
                                      </tr>                                       
                                      <tr>
                                          <td>Importe Actual:
                                          </td>

                                          <td>$ <?php print number_format($factura->getMontoFactura(),2);?></td>
                                      </tr>                                      
                               </table>
                        <fieldset class="ui-widget ui-widget-content ui-corner-all">
                              <legend class="ui-widget ui-widget-header ui-corner-all">Items Factura</legend>
                              <table class='table table-zebra'>
                                                    <thead>
                                                      <tr>
                                                          <th>Articulo</th>
                                                          <th>Cantidad</th>
                                                          <th>Precio Unitario</th>
                                                          <th>Total</th>
                                                          <th>Ver Detalle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    try{
                                                      $items = getResumenFacturaPorArticulo($factura);
                                                    }catch(Exception $e){die($e->getMessage());}
                                                      foreach ($items as $item) {
                                                          print "<tr>
                                                                    <td>$item[des]</td>
                                                                    <td align='right'>$item[cant]</td>
                                                                    <td align='right'>$ ".number_format($item[unit],2)."</td>
                                                                    <td align='right'>$ ".number_format($item[tot],2)."</td>
                                                                    <td><input type='button' class='detItem' value='Ver Detalle' data-fact='$item[idFact]' data-art='$item[art]'</td>
                                                                 </tr>";
                                                      }
                                                    ?>
                                                    </tbody>
                    </table>
                               
                         </fieldset>                               
                         </fieldset>
                          <fieldset class="ui-widget ui-widget-content ui-corner-all">                              
                                <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes a Incluir</legend>
                                <form id='addord'>
                                 <table border="0" align="center" width="100%" name="tabla">
                                        <tr>
                                            <td>
                                                Tipo Servicio
                                                <select id="tipos" name="tipos" >
                                                    <?php
                                                      if ($factCliente->getTipoFacturacion() == 't') {
                                                    ?>
                                                        <option value="0">Todos</option>
                                                        <option value="1">Adm. / Prod. / Mant.</option>
                                                        <option value="2">Over Time / Especiales</option>
                                                    <?php
                                                      }
                                                      else{
                                                     ?>
                                                        <option value="0">Todos</option>
                                                     <?php   
                                                      }
                                                      ?>

                                                </select>
                                            </td>                                          
                                            <td>Desde: <input type="text" name="fecha" id="fecha" value='<?php print $fechaLoad; ?>'></td>
                                            <td>Hasta: <input type="text" name="hasta" id="hasta" value='<?php print $fechaHasta; ?>'></td>

                                            <td align="left">
                                                            <input type="submit" 
                                                                   data-tf="<?php print $factCliente->getTipoFacturacion();?>"
                                                                   value="<?php if ($factCliente->getTipoFacturacion() == 't') 
                                                                                      print "Cargar Ordenes";
                                                                                else
                                                                                      print "Facturar Periodo"; ?>" 
                                                                    id="load"></td>                                            
                                        </tr>
                                 </table>
                                 <br>
                                 <div id='dats'></div>
                                <input type="hidden" name="accion" value="<?php print ($factCliente->getTipoFacturacion() == 't'?'addsrv':'factFjo'); ?>">
                                <input type="hidden" name="cliente" value="<?php print $factura->getCliente()->getId();?>">   
                                <input type="hidden" name="factura" value="<?php print $factura->getId();?>">   
                                <input type="hidden" name="tipofact" value="<?php print $factCliente->getTipoFacturacion();?>">                                                                      
                               </form>
                           </fieldset>                         

            </fieldset>



</body>
</html>

