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
     print "<br>";
     try{

          $certificado = array();
          $dias = array();
          $factura = find('FacturaVenta', $_GET['fv']); 
          foreach ($factura->getOrdenesFacturadas() as $ordFacturada) 
          {
              $linea = getLineaPlanilla($entityManager, $ordFacturada->getArticulo());
              if (!array_key_exists($ordFacturada->getArticulo()->getDescripcion(), $certificado))
              {
                    $certificado[$ordFacturada->getArticulo()->getDescripcion()] = array();
              }
              foreach ($ordFacturada->getOrdenes() as $orden) 
              {
                  if (!$ordFacturada->getImporteUnitario())
                    die("no funca   ".$ordFacturada->getId());
                  $dias[] = $orden->getFservicio()->format('m-d');
                  if(!array_key_exists($orden->getFservicio()->format('m-d'), $certificado[$ordFacturada->getArticulo()->getDescripcion()]))
                  {
                    $certificado[$ordFacturada->getArticulo()->getDescripcion()][$orden->getFservicio()->format('m-d')] = array('ln' => $linea, 'tf' => $ordFacturada->getTarifa(), 'cant' => 0, 'unit' => $ordFacturada->getImporteUnitario());
                  }
                  $certificado[$ordFacturada->getArticulo()->getDescripcion()][$orden->getFservicio()->format('m-d')]['cant']++;
              }
          }

          $dias = array_unique($dias);
          sort($dias);
          $totalDia = array();
          $tabla = "<table class='table table-zebra'>
                      <thead>
                      <tr>
                          <th>Localidad</th>
                          <th>Articulo</th>";
          foreach ($dias as $value) {
            $totalDia[$value] = 0;
            $tabla.="<th>".explode('-', $value)[1]."</th>";
          }

          $tabla.="<th>Cant.</th><th>Tarifa</th><th>Total</th></tr>
                  </thead>
                  <tbody>";
          $total = 0;
          $cantTotal = 0;
          foreach ($certificado as $key => $value) {
              $tabla.="<tr>
                          <td>$key</td>";
              $cant = 0;
              $unit = 0;
              $print = 0;
              foreach ($dias as $day) {
                  if (!$print){
                    $tabla.="<td>".($value[$day]['ln']?$value[$day]['ln']->getLocalidad().'':'')."</td>";
                    $print = 1;
                  }
                  $cant+=$value[$day]['cant'];
                  $tabla.="<td align='right'>".$value[$day]['cant']."</td>";
                  if (!$unit){
                    $unit = $value[$day]['unit'];
                  }

                  $totalDia[$day]+= $value[$day]['cant'];
              }
              $cantTotal+=$cant;
              $total+=($cant*$unit);
              $tabla.="<td align='right'>$cant</td>
                      <td align='right'>$ ".number_format($unit,2,',','.')."</td>
                      <td align='right'>$ ".number_format(($cant*$unit),2,',','.')."</td>
                      </tr>";
          }
          $tabla.="<tr>
                    <td colspan='2'>Total</td>";
          foreach ($totalDia as $value) {
              $tabla.="<td align='right'>$value</td>";
          }
          $tabla.="<td align='right'>$cantTotal</td><td></td><td align='right'>$ ".number_format($total,2,',','.')."</td>
                    </tr></tbody><table>";

          
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
                              <legend class="ui-widget ui-widget-header ui-corner-all">Certificado venta</legend>
                                <?php 
                                  print $tabla;
                                ?>
                               
                         </fieldset>                               
                         </fieldset>                       

            </fieldset>



</body>
</html>

<?php

      function getLineaPlanilla($em, $art){

          $dql = "SELECT lp
                  FROM LineaPlanilla lp
                  WHERE lp.articulo = :articulo";
          $q = $em->createQuery($dql);
           $q->setParameter('articulo',  $art);
           $q->setMaxResults(1);
        $linea = $q->getOneOrNullResult();
        return $linea;

      }
