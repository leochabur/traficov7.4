<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();


     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
  include_once ('../../modelsORM/controller.php');  
    include_once ('../../modelsORM/call.php');
 // $mod = getOrdenModificada(3806476);     


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     


?>
<style type="text/css">





.clase{
 font-family: Arial, Helvetica, sans-serif;
 font-size: 10px;
}

.table-condensed{
   font-family: Arial, Helvetica, sans-serif;
  font-size: 10px;
}

</style>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="/vista/js/MDB-Free_4/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="/vista/js/MDB-Free_4/css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="/vista/js/MDB-Free_4/css/style.css" rel="stylesheet">
  <link href="/vista/js/MDB-Free_4/css/addons/datatables.min.css" rel="stylesheet">


<BODY>
<?php
     menu();

     if (isset($_POST['cliente'])){
        try{
          $cliente = getCliente($_POST['cliente'], $_SESSION[structure]);
          $cronos = getCronogramas($cliente);        
        }
        catch(Exception $e){
                              die($e->getMessage());
        }
      }

?>
    <br>


    <div class="card bg-red">
        <div class="card-header bg-red">
          Servicios existentes
        </div>
        <div class="card-body">
            <form id="load" method="post" class="text-left border-light p-1">
                <div class="row">
                  <div class="col-4">
                      <select name="cliente" id="cliente" class="custom-select custom-select-sm">
                                      <?php
                                        print clientesOptions();
                                      ?>  
                      </select>                     
                  </div>
                  <div class="col-4">
                        <input type="submit" class="btn btn-danger mb-4 btn-sm btn-block" name="" value="Cargar Servicios">                
                  </div>    
                </div>
            </form>
        </div>
               <div id="container" class="clase">
                    <table id="dtBasicExample" class="table table-striped table-bordered table-sm table-condensed table-hover" cellspacing="0" width="100%">
                           <thead>
                  	            <tr class="clase">
                                      <th class="th-sm clase">Codigo Servicio</th>
                                      <th class="th-sm clase">Codigo Horario</th>                                      
                                      <th class="th-sm clase">Nombre</th>                                      
                                      <th class="th-sm clase">H. Citacion</th>
                                      <th class="th-sm clase">H. Salida</th>                                
                                      <th class="th-sm clase">H. Llegada</th>
                                      <th class="th-sm clase">H. Finalizacion</th>     
                                      <th class="th-sm clase">Activo</th>
                                      <th class="th-sm clase">KM</th>                                                                                                   
                                      <th class="th-sm clase">Cliente</th>
                                      <th class="th-sm clase">Tipo Servicio</th>                                      
                                      <th class="th-sm clase">Origen</th>
                                      <th class="th-sm clase">Destino</th>                     
                                      <th class="th-sm clase">Articulo Factura</th>                     
                                      <th class="th-sm clase">Peajes</th>                                                                                
                                                              
                                  </tr>
                           </thead>
                           <tbody class="table-condensed">
                            <?php
                            try{

                              foreach ($cronos as $cron) {
                                   $peajes = "";
                                          foreach ($cron->getEstacionesPeajes() as $estacionPeaje) {
                                            if ($peajes){
                                              $peajes.=" - $estacionPeaje";
                                            }
                                            else{
                                              $peajes = "$estacionPeaje";
                                            }
                                          }                                       
                                  foreach ($cron->getServicios() as $serv) {



                                              $art = $cron->getArticuloFacturacion();


                                          $tdclass = "table-condensed";
                                    print "
                                          <tr >
                                            <td class='$tdclass'>
                                            ".$cron->getId()."
                                            </td>                                          
                                            <td class='$tdclass'>
                                            ".$serv->getId()."
                                            </td>
                                            <td class='$tdclass'>
                                            $cron
                                            </td>
                                            <td class='$tdclass'>".
                                            $serv->getCitacion()->format('H:i')
                                            ."</td>
                                            <td class='$tdclass'>".
                                            $serv->getSalida()->format('H:i')
                                            ."</td>
                                            <td class='$tdclass'>".
                                            $serv->getLlegada()->format('H:i')
                                            ."</td>
                                            <td class='$tdclass'>
                                            ".
                                            $serv->getFinServicio()->format('H:i')
                                            ."
                                            </td>
                                            <td class='$tdclass'>
                                            ".
                                            ($serv->getActivo()?"SI":"NO")
                                            ."
                                            </td>     
                                            <td class='$tdclass'>
                                            ".
                                             $cron->getKm()
                                            ."
                                            </td>  
                                            <td class='$tdclass'>
                                            ".
                                             $cron->getCliente()
                                            ."
                                            </td>           
                                            <td class='$tdclass'>
                                            ".
                                             $serv->getTipo()
                                            ."
                                            </td>                                                 
                                            <td class='$tdclass'>
                                            ".
                                             $cron->getOrigen()
                                            ."
                                            </td>   
                                            <td class='$tdclass'>
                                            ".
                                             $cron->getDestino()
                                            ."
                                            </td>                                               
                                            <td class='$tdclass'>
                                            $art
                                            </td>   
                                            <td class='$tdclass'>
                                            $peajes
                                            </td>                                                                                                                                                                                                                                                                                                                                                                                  
                                          </tr>
                                          ";
                                  }
                              }
                              }
                              catch(Exception $e){die ($e->getMessage());}
                                  ?>
                           </tbody>
                    </table>
               </div>
        </div>
  </div>  

</BODY>
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/popper.min.js"></script>

  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  

  <script type="text/javascript">
    var v34 = $.noConflict(true);
    <?php
        if (isset($_POST['cliente'])){
          print "v34('#cliente').val('$_POST[cliente]');";
        }
    ?>
                    v34('#dtBasicExample').DataTable({
                                                        "paging": false,
                                                        "searching": false // false to disable pagination (or any other option)
                                                      });

  </script>


</HTML>