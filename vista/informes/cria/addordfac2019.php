<?php
     session_start();
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

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
        <link rel="stylesheet" href="/vista/css/estilos.css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
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
                                                       $('#fecha, #hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#load").button().click(function(event){
                                                                                              event.preventDefault();
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");                                                                                      
                                                                                              var data = $("#addord").serialize();
                                                                                         
                                                                                              $.post("/modelo/informes/cria/factvta.php", 
                                                                                                     data, 
                                                                                                     function(data){
                                                                                                                    $('#dats').html(data);
                                                                                                     });
                                                                                             
                                                       });                                                     
                                                       <?php
                                                              if ($fechaLoad)
                                                                print '$("#load").click()';
                                                       ?>


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
                                 <table border="0" align="center" width="75%" name="tabla">
                                        <tr>
                                            <td>Desde: <input type="text" name="fecha" id="fecha" value='<?php print $fechaLoad; ?>'></td>
                                            <td>Hasta: <input type="text" name="hasta" id="hasta" value='<?php print $fechaHasta; ?>'></td>
                                            <td align="left"><input type="submit" value="Cargar Ordenes" id="load"></td>                                            
                                        </tr>
                                 </table>
                                 <br>
                                 <div id='dats'></div>
                                <input type="hidden" name="accion" value="addsrv">
                                <input type="hidden" name="cliente" value="<?php print $factura->getCliente()->getId();?>">   
                                <input type="hidden" name="factura" value="<?php print $factura->getId();?>">                                                                            
                               </form>
                           </fieldset>                         

            </fieldset>



</body>
</html>

