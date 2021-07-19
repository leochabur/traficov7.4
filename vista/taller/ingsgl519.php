<?php
     session_start();
error_reporting(E_ALL & ~E_NOTICE);     
     include_once('../main.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';  
     include_once '../../modelsORM/src/AccionUnidad.php';     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

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

         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFF80;}
         .verde{background-color: #C0FFC0;}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('select').selectmenu({width: 200});
                                                       <?php 
                                                            if (in_array($_SESSION['userid'], array('25', '3', '17'))){
                                                              print "$('#fecha').datepicker({dateFormat:'dd/mm/yy'});";
                                                            }
                                                        ?>
                                                       
                                                       $(':submit').button().click(function(event){
                                                                                              event.preventDefault();
                                                                                              $('#send').hide();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post('/modelo/taller/gstcgl.php',
                                                                                                     $("#upuda").serialize(),
                                                                                                     function(data){
                                                                                                                    var response = $.parseJSON(data);
                                                                                                                    console.log(data);
                                                                                                                    if (response.status)
                                                                                                                      location.reload();
                                                                                                                    else{
                                                                                                                      $('#send').show();
                                                                                                                      alert(response.message);
                                                                                                                    }
                                                                                                                 
                                                                                                     });

                                                       });



                          });
</script>

<body>
<?php
     menu();
        

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
     //$optTurnos.="<option value='".$turno->getId()."'>$turno</option>";
?>
<br>
             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Registrar Ingreso de Combustible</legend>

                      <form id="upuda">
                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td>
                                      Fecha:
                                    </td>
                                    <td>
                                       <input type="text" name="fecha" id="fecha" class="ui-widget" 
                                              <?php 
                                                if (!in_array($_SESSION['userid'], array('25', '3', '17'))){
                                                  print " readonly ";
                                                }
                                              ?>
                                              value="<?php print date('d/m/Y'); ?>">
                                    </td>
                                </tr>       
                                <tr>
                                    <td>
                                      Concepto:
                                    </td>                                  
                                    <td>
                                       <input type="text" name="concepto" id="concepto" class="ui-widget"
                                              <?php 
                                                if (!in_array($_SESSION['userid'], array('25', '3', '17'))){
                                                  print " value='Ingreso de combustible' readonly";
                                                }
                                              ?>>
                                    </td>   
                                </tr>                                                   
                                <tr>
                                    <td>
                                      Proveedor:
                                    </td>
                                    <td>
                                       <input type="text" name="proveedor" id="proveedor" class="ui-widget">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                      Nro Factura:
                                    </td>                                  
                                    <td>
                                       <input type="text" name="factura" id="factura" class="ui-widget">
                                    </td>   
                                </tr>
                                <tr>         
                                    <td>
                                      Litros: 
                                    </td>                                  
                                    <td>
                                      <input type="text" name="litros" class="ui-widget" size="5">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                      Destino: 
                                    </td>                                  
                                    <td>
                                      <select name="destino">
                                                  <?php
                                                        $destinos = $entityManager->createQuery("SELECT t FROM Destino t")->getResult();
                                                        foreach ($destinos as $destino) {
                                                          print "<option value='".$destino->getId()."''>".$destino->getNombre()."</option>";
                                                        }                                                    
                                                  ?>
                                                </select>
                                    </td> 
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                      <td align="right">
                                        <input type="submit" value="Registrar ingreso" id="send">
                                    </td>                                    
                                </tr>
                         </table>
                         <input type="hidden" name="accion" value="sveing">
                    </form>
            </fieldset>
             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Ultimos Ingreso de Combustible</legend>

                         <table border="0" class="table table-zebra">
                            <thead>
                              <tr>
                                  <th>Fecha</th>
                                  <th>Concepto</th>         
                                  <th>Proveedor</th>         
                                  <th>Factura</th>   
                                  <th>Litros</th>
                                  <th>Destino</th>
                                  <th>Usuario</th>
                                  <th>Fecha/Hora Carga</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                                  $ingresos = $entityManager->createQuery("SELECT t FROM CargaCombustible t WHERE t.ingreso = :ingreso ORDER BY t.fecha")->setParameter('ingreso', true)->getResult();

                                  foreach ($ingresos as $ingreso) {
                                        print "<tr>
                                                  <td>".$ingreso->getFecha()->format('d/m/Y')."</td>
                                                  <td>".$ingreso->getConcepto()."</td>
                                                  <td>".$ingreso->getProveedor()."</td>     
                                                  <td>".$ingreso->getFactura()."</td>                                                                                                  
                                                  <td>".$ingreso->getLitros()."</td>
                                                  <td>".$ingreso->getDestino()."</td>
                                                  <td>".$ingreso->getUsuario()->getUsername()."</td>
                                                  <td>".$ingreso->getFechaAlta()->format('d/m/Y H:i')."</td>
                                               </tr>";
                                            
                                  }
                            ?>
                            </tbody>
                         </table>
            </fieldset>            
</body>
</html>

