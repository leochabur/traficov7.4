<?php
     set_time_limit(0);
     error_reporting(E_ALL);
     session_start();


     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
  include_once ('../../modelsORM/controller.php');  
 // $mod = getOrdenModificada(3806476);     


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
    $todas="";
	$notodas="";

	if (isset($_POST['mostrar'])){
       $_SESSION['todas'] = $_POST['mostrar'];
    }

     
     if (isset($_POST['fecha'])){
        $fecha = $_POST['fecha'];
     //   $fecha = explode("/", $fec);
      //  $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_POST['fecha'];
     }
     elseif(isset($_GET['fecha'])){
        $fec = $_GET['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_GET['fecha'];
     }
     else{
         $fecha = date("Y-m-d");
         $fechaVis = date("d/m/Y");
     }
     
     $sort = 'ASC';
     $orden="hcitacion";
     if (isset($_POST['order'])){
        $sort = (strpos($_POST['sort'], 'desc')?'DESC':'ASC');

        $orden = (($_POST['order'] != '')?$_POST['order']:$orden);
        if ($orden == 'interno')
          $orden = "CAST(interno AS SIGNED)";
     }

     $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));//date("Y")."-".date("m")."-".(date("d")+1);
     $maniana= date("Y-m-d", $tomorrow);
     //$maniana= date("Y")."-".date("m")."-".(date("d")+1);

?>
<style type="text/css">





.clase{
 font-family: Arial, Helvetica, sans-serif;
 font-size: 12px;
}

.table-condensed{
  font-size: 10px;
}

.cursor-pointer{
  cursor: pointer;
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
     $where = "(fservicio = '$fecha')";  
     $cita = "o.hcitacionreal"; 
     $sale = "o.hsalidaplantareal"; 
     $fina = "o.hfinservicioreal";
     $llega = "o.hllegadaplantareal";

     $sql = "SELECT o.id, finalizada, time_format($cita, '%H:%i') as hcitacion, time_format($sale, '%H:%i') as hsalida, 
                    time_format($fina, '%H:%i') as hfinserv, o.nombre, 
                    if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, 
                    upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, os.comentario as comentario, interno, 
                    ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada, emp.color, date_format($llega, '%H:%i') as hllegada, ot.id as or_tur, 
                    cantpax, i_v, 1 as is_orden, date_format(o.hllegadaplantareal, '%H:%i') as hllegadaReal, date_format(o.hsalidaplantareal, '%H:%i') as hsalidaReal, 
                    op.id as cliVac $camposTripulacion
             FROM ordenes o
             LEFT JOIN servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
             LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
             LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
             LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             LEFT JOIN unidades m ON (m.id = o.id_micro)
             LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
             LEFT JOIN ordenes_turismo ot on ot.id_orden = o.id and ot.id_estructura_orden = o.id_estructura
             LEFT JOIN obsSupervisores os ON os.id_orden = o.id
             LEFT JOIN (SELECT * FROM opciones WHERE opcion = 'cliente-vacio') op ON op.id_estructura = o.id_estructura and op.valor = o.id_cliente
             WHERE $where and (not borrada) and (not suspendida) and (o.id_estructura = $_SESSION[structure]) and (finalizada in ($_SESSION[todas]))
             UNION
             SELECT n.id as id, 0 as finalizada, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, cn.nov_text as nombre, concat(e.apellido, ', ',e.nombre) as chofer1, '' as razon_social, '' as chofer2, '' as comentario, '' as interno, e.id_empleado as id_empleado, 0 as id_empleado2, 0, 0, '' as color, '00:00' as hllegada, 0 as or_tur, '' as cantpax, 'n' as i_v, 0 as is_orden, '00:00' as llegadareal, '00:00' as salildareal, 0 as cliVac $camposNovedades
             FROM novedades n
             inner join cod_novedades cn on cn.id = n.id_novedad
             inner join empleados e on e.id_empleado = n.id_empleado
             where ('$fecha' between desde and hasta) and (e.id_estructura = $_SESSION[structure]) and (e.id_cargo = 1) and (n.activa)
             union
             SELECT o.id, finalizada, time_format($cita, '%H:%i') as hcitacion, time_format($sale, '%H:%i') as hsalida, 
                   time_format($fina, '%H:%i') as hfinserv, concat(o.nombre,' (', upper(es.nombre),')') as nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, os.comentario, interno, ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada, emp.color, date_format($llega, '%H:%i') as hllegada, 0 as or_tur, '' as cantpax, i_v, 1 as is_orden, date_format(o.hllegadaplantareal, '%H:%i') as hllegadaReal, date_format(o.hsalidaplantareal, '%H:%i') as hsalidaReal, op.id as cliVac $camposNovedades
             from ordenes o
             LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
             LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
             LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             LEFT JOIN unidades m ON (m.id = o.id_micro)
             LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
             inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
             inner join controlexternoservicios ce on ce.id_servicio = s.id and ce.id_estructura_servicio = s.id_estructura
             INNER JOIN estructuras es ON es.id =  ce.id_estructura_servicio
             LEFT JOIN obsSupervisores os ON os.id_orden = o.id
             LEFT JOIN (SELECT * FROM opciones WHERE opcion = 'cliente-vacio' AND id_estructura = $_SESSION[structure]) op ON op.id_estructura = o.id_estructura and op.valor = o.id_cliente
             where vigente and id_estructua_destino = $_SESSION[structure] and $where
             order by $orden $sort";

?>
    <br>

    <div class="container">
      
    </div>

            <form id="load" method="post" class="text-left border-light p-1">
                <div class="row">
                  <div class="col-2 offset-md-2">
                    <p class="font-weight-bolder">Fecha</p>                    
                  </div>
                  <div class="col-2">
                        <input type="date" name="fecha" class="form-control form-control-sm" value="<?php print $fecha; ?>">             
                  </div>
                  <div class="col-4">
                        <input type="submit" class="btn btn-danger mb-4 btn-sm btn-block" id="loadOrdenes" value="Cargar Ordenes">                
                  </div>    
                </div>
                <input type="hidden" name="order" id="order" value="<?php print $_POST[order]; ?>">
                <input type="hidden" name="sort" id="sort" value="<?php print $sort; ?>">
                <input type="hidden" name="position" id="position">
            </form>
          

               <div class="row clase p-2">
                    <div class="col-2 amber lighten-2">&Oacute;rden finalizada</div>
                    <div class="col-2 blue lighten-4">&Oacute;rden chequeada</div>
                    <div class="col-2 bg-secondary text-white">&Oacute;rden suspendida</div>
                    <div class="col-2 deep-orange">&Oacute;rden sin conductor</div>
               </div>
         
               <div id="container" class="clase">
                    <table id="dtBasicExample" class="table table-striped table-bordered table-sm table-condensed table-hover" cellspacing="0" width="100%">
                           <thead>
                  	            <tr class="clase">
                                      <th class="th-sm clase" data-order="hcitacion"></th>
                                      <th class="th-sm clase" data-order="hsalida">H. Citacion</th>
                                      <th class="th-sm clase" data-order="hllegada">H. Salida</th>
                                      <th class="th-sm clase" data-order="nombre">Servicio</th>
                                      <th class="th-sm clase" data-order="interno">Interno</th>                                
                                      <th class="th-sm clase" data-order="chofer1">Conductor 1</th>
                                      <th class="th-sm clase" data-order="chofer2">Conductor 2</th>                                                                
                                      <th class="th-sm clase" data-order="razon_social">Cliente</th>
                                      <th class="th-sm clase" data-order="comentario">Observaciones</th>
                                      <th class="th-sm clase" data-order="cantpax">Pax</th>          
                                      <th class="th-sm clase" data-order="hllegada">H. Llegada</th>                                                                                         
                                  </tr>
                           </thead>
                           <tbody class="table-condensed">
                                  <?php
                                       $query = mysql_query($sql, $con) or die(($sql));
                               $body = "";
                               while($row = mysql_fetch_array($query))
                               {                  
                                    $id = $row['id'];
                                    $tdclass = "table-condensed";
                                    if (!($row['id_empleado']) && !($row['id_empleado2'])){
                                      $tdclass = "deep-orange table-condensed";
                                    }
                                    elseif ($row['suspendida']){
                                      $tdclass = "bg-secondary text-white table-condensed";
                                    }
                                    elseif ($row['finalizada']){
                                      $tdclass = "amber lighten-2 table-condensed";
                                    }
                                    elseif ($row['checkeada']) {
                                      $tdclass = "blue lighten-4 table-condensed";
                                    }

                                     $orden = $row['id'];
                                      $fila = "<tr id='$orden'>
                                                 <td class='text-center $tdclass'><i class='far fa-caret-square-right fa-1x fa-edit' data-id='$id'></i>  <i class='far fa-check-square fa-1x fa-check' data-id='$id'></i></td>
                                                 <td class='$tdclass'>$row[hcitacion]</td>
                                                 <td class='$tdclass'>$row[hsalida]</td>
                                                 <td class='$tdclass'>$row[nombre]</td>                                           
                                                 <td class='$tdclass viewint' data-interno='$row[interno]' >$row[interno]</td>
                                                 <td class='$tdclass viewdiag cursor-pointer' data-apenom='$row[chofer1]' data-conductor='$row[id_empleado]'>$row[chofer1]</td>
                                                 <td class='$tdclass viewdiag cursor-pointer' data-apenom='$row[chofer2]' data-conductor='$row[id_empleado2]'>$row[chofer2]</td>                                                                                      
                                                 <td class='$tdclass'>$row[razon_social]</td>
                                                 <td class='$tdclass'>$row[comentario]</td>
                                                 <td class='$tdclass'>$row[cantpax]</td>      
                                                 <td class='$tdclass'>$row[hllegada]</td>                                                                                  
                                                </tr>
                                                 ";
                                      $body.=$fila;
                                }
                                       mysql_free_result($query);
                                       mysql_close($con);
                                       print $body;
                                  ?>
                           </tbody>
                    </table>
               </div>

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

<div class="modal fade" id="viewDiagrama" tabindex="-1" role="dialog" aria-labelledby="bodyDiagrama"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bodyDiagrama">Diagrama de trabajo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body-view">
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

  v34(function() {
                    v34('.viewdiag').on('click', function (e) {
                                                               loadDiagrama($(this).data('apenom'), $(this).data('conductor'));
                    });

                    v34('.viewint').on('click', function (e) {
                                                            
                                                           var interno = $(this).data('interno');
                                                            v34('.modal-body-view').load('/vista/segvial/posint.php?int='+interno);
                                                            v34('#viewDiagrama').modal({show:true})
                    });

                    v34('.fa-edit').on('click', function (e) {
                                                            openOrder($(this));
                    });

                    v34('.fa-check').on('click', function (e) {
                                                            checkOrder($(this));
                    });                

                    v34('#dtBasicExample').DataTable({
                                                        "paging": false,
                                                        "searching": false // false to disable pagination (or any other option)
                                                      });
                    v34('.dataTables_length').addClass('bs-select');

                   v34(".ocultar").hide();
                   v34('table thead tr th').click(function(event){
                                                          var col = $(this).data('order');//parent().children().index($(this));
                                                          v34("#order").val(col);
                                                          v34("#sort").val($(this).attr('class'));
                                                        });
                  <?php
                      if (isset($_POST['position'])) 
                        if ($_POST['position'])                 
                            print "v34('html,body').animate({scrollTop:".$_POST['position']."},{duration:'slow'});";    
                  ?>               

        
});

function loadDiagrama(apenom, id)
{
    v34('#bodyDiagrama').html('Diagrama de trabajo de:  '+ apenom);
    v34('.modal-body-view').html('<div class="progress"><div class="progress-bar progress-bar-striped bg-danger progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>');
    v34('.modal-body-view').load('/modelo/ordenes/ordenesV2.php', {accion:'diagrama', cond: id});
    v34('#viewDiagrama').modal({show:true})
}

function openOrder(element)
{
        v34("#position").val(element.offset().top);
        v34('#exampleModalLabel').html('Detalle de la orden '+ element.data('id'));
        v34('.modal-body').load('/modelo/ordenes/ordenesV2.php', {accion:'load', orden: element.data('id')});
        v34('#basicExampleModal').modal({show:true})
}

function checkOrder(element){
                                                              var i = element;
                                                              bootbox.confirm({
                                                              message: "Seguro chequear la orden de trabajo?",
                                                              buttons: {
                                                                  confirm: {
                                                                      label: "Si",
                                                                      className: "btn-success"
                                                                  },
                                                                  cancel: {
                                                                      label: "No",
                                                                      className: "btn-danger"
                                                                  }
                                                              },
                                                              callback: function (result) {
                                                                  if (result){
                                                                        $.post("/modelo/ordenes/ordenesV2.php",
                                                                               {accion:'check', orden: i.data('id')},
                                                                               function(data){
                                                                                           var response = v34.parseJSON(data);
                                                                                           if (response.ok){
                                                                                              i.parent().removeClass().addClass('bg-primary text-white table-condensed');
                                                                                              i.parent().siblings().removeClass().addClass('bg-primary text-white table-condensed');
                                                                                           }
                                                                                           else{
                                                                                              alert(response.msge);
                                                                                           }
                                                                                                                                                                           
                                                                               });

                                                                  }
                                                              }
                                                          }); 
}


  </script>


</HTML>