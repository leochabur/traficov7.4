<?php
  session_start();

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }

  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/controller.php');  

  $accion= $_POST['accion'];
  
  if ($accion == 'resumen'){

    $desde = DateTime::createFromFormat('d/m/Y', ($_POST['desde']));
    $hasta = DateTime::createFromFormat('d/m/Y', ($_POST['hasta']));  

   // die($desde->format('d/m/Y').' '.$hasta->format('d/m/Y'));
    $detalles = movimientosDeCuenta($_POST['clientes'], $desde, $hasta);    

    $tabla = "<table class='table table-zebra' id='detctacte' width='100%'>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Detalle</th>
                  <th>Debito</th>
                  <th>Credito</th>
                  <th>Saldo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>";
    $saldo = 0;
    foreach ($detalles as $mov) {
      $saldo = $mov->getSaldo($saldo);
      $tabla.="<tr>
                <td>".$mov->getFecha()->format('d/m/Y')."</td>
                <td>".$mov->getDescripcion()."</td>
                <td align='right'>$ ".number_format($mov->getDebitos(),2)."</td>
                <td align='right'>$".number_format($mov->getCredito(),2)."</td>                
                <td align='right'>$ ".number_format($saldo,2)."</td>
                <td align='center'><a href='' class='view' id='".$mov->getIdent()."' title='Ver/Cargar Pago'><i class='fas fa-donate fa-lg'></a></i></td>
               </tr>";
    }
    $tabla.="</tbody>
             </table>
             <script>
                    $('#detctacte .view').click(function(event){
                                                                  event.preventDefault();
                                                                  var p = $(this).attr('id');
                                                                  $.post('/modelo/turismo/rctctt.php',
                                                                        {accion: 'detpgo', id: p },
                                                                         function(data){
                                                                          $('#detap').remove();
                                                                          var dialog = $('<div id=\'detap\'></div>').appendTo('body');
                                                                            
                                                                       
                                                                          dialog.dialog({
                                                                                       title: 'Detalle pagos efectuados',
                                                                                       width:400,
                                                                                       height:350,
                                                                                       modal:true,
                                                                                       autoOpen: false
                                                                                       });
                                                                          dialog.load('/modelo/turismo/rctctt.php',
                                                                                      {accion: 'detpgo', id: p},
                                                                                      function (){ 
                                                                                      });
                                                                          dialog.dialog('open');
                                                                  });
                    });
             </script>";   
    print $tabla; 

  }
  elseif ($accion == 'detpgo'){
    $movimiento = find('MovimientoDebito', $_POST['id']);

    $tabla.="<table class='table table-zebra'>
            <thead>
              <tr>
                  <th>Fecha</th>
                  <th>Concepto</th>
                  <th>Importe</th>
              </tr>
            </thead>
            <tbody>";
    foreach ($movimiento->getPagos() as $pago) {
        $tabla.="<tr>
                    <td>".$pago->getFecha()->format('d/m/Y')."</td>
                    <td>".$pago->getDescripcion()."</td>
                    <td>".$pago->getImporte()."</td>
                  </tr>";

    }
    $options = mediosPagosOptions();
    $tabla.="</tbody>
             </table>
             <br>
             <form id='cgpgo'>
                <p><input type='text' class='fec ui-widget ui-widget-content ui-corner-all required' name='fpago' placeholder='Fecha Pago'></p>
                <p><input type='text' class='ui-widget ui-widget-content ui-corner-all required' name='mtopgo' placeholder='Importe'></p>
                <p><select id='medios' name='medios'>$options</select></p>
                <p><input type='text' class='ui-widget ui-widget-content ui-corner-all' name='descripc'placeholder='Descripcion'/></p>
                <input type='hidden' name='accion' id='accion' value='savepgo'>
                <input type='hidden' name='debito' id='debito' value='$_POST[id]'>
                <input type='submit' value='Cargar Pago'/><input type ='button' value='Cerrar ventana' id='closeame'/>
             </form> 

             
             <script>
                    $('.fec').datepicker({dateFormat:'dd/mm/yy'});
                    $('#medios').selectmenu({width: 250});
                            $('#cgpgo').validate({
                                  submitHandler: function(){
                                                            var data = $('#cgpgo').serialize();
                                                            $.post('/modelo/turismo/rctctt.php',
                                                                  data,
                                                                  function(data){
                                                                                $('#detap').dialog('close');
                                                                                $('#cargar').click();
                                                                  });
                                                            
                                  }
                                });


                    $('#cgpgo :submit').button();
                    $('#closeame').button().click(function(){
                                  $('#detap').dialog('close');
                    });
             </script>";
    print$tabla;


  }
  elseif ($accion == 'savepgo'){
    $movimiento = find('MovimientoDebito', $_POST['debito']);
    $medioPago = find('MedioPago', $_POST['medios']);
    $credito = new MovimientoCredito();
    $credito->setDebito($movimiento);
    $credito->setImporte($_POST[mtopgo]);
    $credito->setMedioPago($medioPago);
    $credito->setDescripcion('Pago a cuenta '.$movimiento->getDescripcion());
    $credito->setCliente($movimiento->getCliente());
    $credito->setFecha(DateTime::createFromFormat('d/m/Y', $_POST[fpago]));
    $movimiento->addPago($credito);
    $entityManager->persist($credito);
    $entityManager->flush();    

  }

  ?>