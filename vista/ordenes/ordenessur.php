<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();


     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
    $todas="";
	$notodas="";

	if (isset($_POST['mostrar'])){
       $_SESSION['todas'] = $_POST['mostrar'];
    }

     
     if (isset($_POST['fecha']))
     {
        $fecha = $_POST['fecha'];
        $fechaVis = $_POST['fecha'];
        if ($_POST['direction'])
        {
          $fechaDes = DateTime::createFromFormat("Y-m-d", $_POST['fecha']);
          if ($_POST['direction'] == 'b')
          {
            $fechaDes->sub(new DateInterval('P1D'));
          }
          else
          {
            $fechaDes->add(new DateInterval('P1D'));
          }
          $fecha = $fechaDes->format('Y-m-d');
          $fechaVis = $fechaDes->format('Y-m-d');
        }
     }
     elseif(isset($_GET['fecha'])){
        $fec = $_GET['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_GET['fecha'];
     }
     else{
         $fecha = date("Y-m-d");
         $fechaVis = date("Y-m-d");
     }
     
     $orden="hcitacion";
     if (isset($_POST['order']) && ($_POST['order'] != '')){
        $orden = $_POST['order'];
     }

?>
<style type="text/css">





.clase{
 font-family: Arial, Helvetica, sans-serif;
 font-size: 10px;
}

.table-condensed{
  font-size: 10px;
}

.tdhour {
    width:10px;
    max-width:10px;
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
     $con = conexcion();  
     $ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer,if (date(citacion) < '$fecha', '00:00:00', citacion) as cita,
                        if (date(salida) < '$fecha', '00:00:00', salida) as sale,
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion) as fina,
                        if (date(llegada) > '$fecha', '23:59:59', llegada) as llega, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden, 1 as chofer_1, 0 as idtxo,finalizada, checkeada, id_estructura_ciudad_origen, id_estructura_ciudad_destino, id_ciudad_origen, id_ciudad_destino, id_cliente_vacio, id_estructura_cliente_vacio, vacio,
                        if (date(citacion_real) < '$fecha', '00:00:00', citacion_real) as citacion_real,
                        if (date(salida_real) < '$fecha', '00:00:00', salida_real) as salida_real,
                        if (date(finalizacion_real) > '$fecha', '23:59:59', finalizacion_real) as finalizacion_real,
                        if (date(llegada_real) > '$fecha', '23:59:59', llegada_real) as llegada_real, cod_servicio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura in (2, 11))
                  UNION ALL
                  SELECT o.id, o.id_chofer_2,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:59', llegada), id_cliente, id_estructura_cliente, id_micro, nombre, 2 as chofer_1, 0 as idtxo,
                        finalizada, checkeada, id_estructura_ciudad_origen, id_estructura_ciudad_destino, id_ciudad_origen, id_ciudad_destino, id_cliente_vacio, id_estructura_cliente_vacio, vacio,
                        if (date(citacion_real) < '$fecha', '00:00:00', citacion_real) as citacion_real,
                        if (date(salida_real) < '$fecha', '00:00:00', salida_real) as salida_real,
                        if (date(finalizacion_real) > '$fecha', '23:59:59', finalizacion_real) as finalizacion_real,
                        if (date(llegada_real) > '$fecha', '23:59:59', llegada_real) as llegada_real, cod_servicio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura in (2, 11))
                  union all
                  SELECT o.id, id_empleado,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:59', llegada), id_cliente, id_estructura_cliente, id_micro, nombre, 3 as chofer_1, txo.id as idtxo,
                        finalizada, checkeada, id_estructura_ciudad_origen, id_estructura_ciudad_destino, id_ciudad_origen, id_ciudad_destino, id_cliente_vacio, id_estructura_cliente_vacio, vacio,
                        if (date(citacion_real) < '$fecha', '00:00:00', citacion_real) as citacion_real,
                        if (date(salida_real) < '$fecha', '00:00:00', salida_real) as salida_real,
                        if (date(finalizacion_real) > '$fecha', '23:59:59', finalizacion_real) as finalizacion_real,
                        if (date(llegada_real) > '$fecha', '23:59:59', llegada_real) as llegada_real, cod_servicio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura in (2, 11))";


?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all clase">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post" class="container">
              <div align="center" class="clase row">
                  <button id="back" class="col-1">Un dia atras.</button>
                  <input id="fecha" name="fecha" value="<?php echo $fechaVis;?>" type="date" size="20" class="col-3">
                  <button id="next" class="col-1">Un dia adelante.</button>
                  <input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes" class="col-2">
              </div>
              <input type="hidden" id="posx" name="posx">
              <input type="hidden" id="posy" name="posy" <?php if (isset($_POST['posy'])) print "value='$_POST[posy]'";?>>
              <input type="hidden" id="order" name="order" <?php if (isset($_POST['order']) && ($_POST['order'] != '')) print "value='$_POST[order]'";?>>
              <input type="hidden" id ="direction" name="direction">
         </form>
         </div>
         <div id='mjw'></div>
         <hr align="tr">
         <div class="row clase">
              <div class="col-2 bg-warning text-white">&Oacute;rdenes finalizadas</div>
              <div class="col-2 bg-primary text-white">&Oacute;rdenes checkeadas</div>
              <div class="col-2 bg-secondary text-white">&Oacute;rdenes suspendidas</div>
              <div class="col-2 bg-danger text-white">&Oacute;rdenes sin conductores</div>
         </div>
         <div id="container" class="clase">
              <table id="dtBasicExample" class="table table-striped table-bordered table-sm table-condensed table-hover">
                     <thead>
            	            <tr class="clase">
                                <th class="th-sm clase tdhour mx-0"></th>
                                <th class="th-sm clase">H. Citacion</th>
                                <th class="th-sm clase">H. Salida</th>
                                <th class="th-sm clase">H. Llegada</th>
                                <th class="th-sm clase">Servicio</th>
                                <th class="th-sm clase">Origen - Destino</th>
                                <th class="th-sm clase">Interno</th>
                                <?php
                                     for ($i = 1; $i <= $cantTripulacion; $i++){
                                        print "<th class='th-sm clase'>Conductor $i</th>";
                                     }
                                ?>
                                <th class="th-sm clase">Cliente</th>
                                <th class="th-sm clase">Codigo</th>
                            </tr>
                     </thead>
                     <tbody class="table-condensed">
                            <?php

                                $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha 
                                            FROM estadoDiagramasDiarios 
                                            where (fecha = '$fecha') and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
                                $result = mysql_query($sql, $con);

                                $finalizado = count($result);

                                $fieldCita = 'cita';
                                $fieldSale = 'sale';
                                $fieldLlega = 'llega';

                                if ($finalizado)
                                {
                                    $fieldCita = 'citacion_real';
                                    $fieldSale = 'salida_real';
                                    $fieldLlega = 'llegada_real';
                                }


                                $ordenes = "SELECT time_format(o.llega, '%H:%i') as llega, orden, nomOrden, time_format(o.cita, '%H:%i') as cita, time_format(o.sale, '%H:%i') as sale, interno, c.razon_social, concat(apellido,', ',nombre) as chofer, id_empleado, chofer_1, idtxo, finalizada, checkeada, id_chofer, upper(orig.ciudad) as origen, upper(dest.ciudad) as destino, cv.razon_social as cliVac, vacio, m.id_propietario as color,
                                    time_format(o.citacion_real, '%H:%i') as citacion_real, time_format(o.salida_real, '%H:%i') as salida_real, time_format(o.llegada_real, '%H:%i') as llegada_real, if (cod_servicio is not null, cod_servicio, '') as cod_servicio
                                             FROM ($ordenes) o
                                             LEFT JOIN empleados ch ON (ch.id_empleado = o.id_chofer)
                                             LEFT JOIN unidades m ON (m.id = o.id_micro)
                                             LEFT JOIN obsSupervisores os ON os.id_orden = o.orden
                                             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                             LEFT JOIN clientes cv ON ((cv.id = o.id_cliente_vacio) and (cv.id_estructura = o.id_estructura_cliente_vacio))
                                             INNER JOIN ciudades orig ON orig.id = id_ciudad_origen AND orig.id_estructura = id_estructura_ciudad_origen
                                             INNER JOIN ciudades dest ON dest.id = id_ciudad_destino AND dest.id_estructura = id_estructura_ciudad_destino
                                             order by orden, chofer_1
                                             ";
                                //  die($ordenes);
                                 $query = mysql_query($ordenes, $con) or die(mysql_error($con));
				                 $row = mysql_fetch_array($query);
                         $divclass = '';
                         while($row)
                         {                  
                            $id = $row['orden'];
                            $tdclass = "table-condensed";
                            if ($row['finalizada']){
                              $tdclass = "bg-warning table-condensed";
                            }
                            elseif ($row['checkeada']) {
                              $tdclass = "bg-primary table-condensed";
                            }

                                           $orden = $row['orden'];
                                           $color = '';
                                           if ($row['color'] != 1)
                                           {
                                              $color = 'text-danger';
                                           }
                                           else
                                           {
                                              $color = $tdclass;
                                           }
                                            
                                            $fila = "<td class='text-center'>
                                                                            <i class='far fa-edit' data-id='$id'></i>
                                                                            <a href='#' class='checked' data-servicio='$row[nomOrden]' data-orden='$row[orden]'><i class='fa fa-check-square ml-2' aria-hidden='true'></i></a>
                                                      </td>
                                                      <td class='$tdclass tdhour'>$row[$fieldCita]</td>
                                                       <td class='$tdclass'>$row[$fieldSale]</td>
                                                       <td class='$tdclass'>$row[$fieldLlega]</td>
                                                       <td class='$tdclass'>$row[nomOrden]</td>
                                                       <td class='$tdclass'>$row[origen] - $row[destino]</td>
                                                       <td class='$color'>$row[interno]</td>";
                                            $j = 0;
                                            $conductor = false;
                                            while ($orden == $row['orden'])
                                            {
                                                if ($row['id_chofer'])
                                                    $conductor = true;
                                                if ($row['chofer_1'] == 1)
                                                {
                                                  $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_1-$id' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);'>".($row['chofer'])."</div></td>";
                                                }
                                                elseif ($row['chofer_1'] == 2)
                                                {
                                                   $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_2-$id' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);'>".($row['chofer'])."</div></td>";
                                                }
                                                elseif ($row['chofer_1'] == 3)
                                                {
                                                   $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_3-$row[idtxo]' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);'>".($row['chofer'])."</div></td>";
                                                }

                                                $j++;
                                                $ant = $row;
                                                $row = mysql_fetch_array($query);
                                                $last = $row;
                                            }
                                            $row = $ant;
                                            for ($i=$j; $i < $cantTripulacion; $i++) { 
                                                $p = $i + 1;
                                                $fila.="<td class='$tdclass'><div class='$divclass' id='add_chofer_tripulacion-$id' style='cursor: pointer;'></div></td>";
                                            }
                                            $razon_social = $row['razon_social'];
                                            if (($row['vacio']) && ($row['cliVac']) && ($row['razon_social'] != $row['cliVac']))
                                            {
                                                $razon_social.=" ($row[cliVac])";
                                            }
                                            $fila.= "<td class='$tdclass'>$razon_social</td>
                                                     <td class='$tdclass'>$row[cod_servicio]</td>
                                                   </tr>";
                                            if (!$conductor)
                                                $tdclass = "bg-danger text-white";
                                            $tr = "<tr class='clase $tdclass'>";
                                            print $tr.$fila;
                                            $row = $last;
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>

</BODY>
<div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detalle de la orden</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/popper.min.js"></script>

  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  

  <script type="text/javascript">
    var v34 = $.noConflict(true);
</script>

 <script>

  v34(function() {

                    v34('.checked').click(function(event) {
                                                            event.preventDefault();
                                                            var a = $(this);
                                                            if (confirm('Seguro chequear la orden '+ a.data('servicio') +'?'))
                                                            {
                                                                $.post('/modelo/ordenes/ordenessur.php', 
                                                                      {
                                                                            accion:'check', 
                                                                            ord: a.data('orden')
                                                                       },
                                                                       function(data){
                                                                                        var response = $.parseJSON(data);
                                                                                        if (response.ok)
                                                                                        {
                                                                                            a.parent().parent().addClass('bg-primary ');
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            alert(a.msge);
                                                                                        }
                                                                       });
                                                            }
                    });

                    v34('.fa-edit').on('click', function (e) {
                                                            v34('#exampleModalLabel').html('Detalle de la orden '+ $(this).data('id'));
                                                            v34('.modal-body').load('/modelo/ordenes/ordenessur.php', {accion:'load', orden: $(this).data('id')});
                                                            v34('#basicExampleModal').modal({show:true})
                    });
                    v34('#dtBasicExample').DataTable({
                                                        "paging": false,
                                                        "searching": false // false to disable pagination (or any other option)
                                                      });
                    v34('.dataTables_length').addClass('bs-select');

                   v34(".ocultar").hide();

                  $("#back").button({icons: {primary: "ui-icon-circle-triangle-w"},text: false}).click(function(event){
                                                                                                                  event.preventDefault();
                                                                                                                  $("#direction").val('b');
                                                                                                                  $('#cargar').trigger('click');
                                                                                                                  });
                  $("#next").button({icons: {primary: "ui-icon-circle-triangle-e"},text: false}).click(function(event){
                                                                                                                  event.preventDefault();
                                                                                                                  $("#direction").val('n');
                                                                                                                  $('#cargar').trigger('click');
                                                                                                                  });
                  $('#cargar').button();
});
  </script>


</HTML>