<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();
     include ('../../controlador/bdadmin.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');



     
     if (isset($_POST['fecha'])){
        $fecha = $_POST['fecha'];
      //  $fec = date_create($fecha);
      //  @date('w',$fec);
        $cargar=1;
     }
     else{
         $fecha = "";//date("Y-m-d");
         $diasemana = "";//date('w');
         $cargar=0;
     }
     
     $dias = array(1 =>'DOMINGO', 2 =>'LUNES', 3 =>'MARTES', 4 =>'MIERCOLES', 5 =>'JUEVES', 6 =>'VIERNES', 7 =>'SABADO');

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="/vista/js/MDB-Free_4/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="/vista/js/MDB-Free_4/css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="/vista/js/MDB-Free_4/css/style.css" rel="stylesheet">
  <link href="/vista/js/MDB-Free_4/css/addons/datatables.min.css" rel="stylesheet">

  <link href="/vista/datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
  
 <script>
    var micros=new Array();
    micros.push(new Array(0,'A/D'));
    var choferes=new Array();
    choferes.push(new Array(0,'A Designar'));
    <?php
      $con = conexcion();
      
      $res_cc = mysql_query("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]", $con);
      if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
      }
      ////// si no esta finalizado el diagrama setea una variable de session para no enviar el mail
      $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
      $result = mysql_query($sql, $con);
      if ($data = mysql_fetch_array($result)){
         $_SESSION['senmail'] = 1;
      }
      else{
          $_SESSION['senmail'] = 0;
      }
      //////////////////////////////////////////////////////////////////////////////////////////
      $result = mysql_query('SELECT id, interno FROM unidades WHERE (id_estructura = '.STRUCTURED.') and (activo) ORDER BY interno', $con);
      $i=0;

      while ($data = mysql_fetch_array($result)){?>
            micros.push(new Array(<?php echo $data['id'];?>,  <?php echo $data['interno'];?>));
      <?php
           $i++;
      }
      $result = mysql_query("SELECT id_empleado, if(em.id = 1,upper(concat(legajo,' - ',e.apellido, ', ',e.nombre)), 
                                    upper(concat(legajo,' - (',em.razon_social,') ', e.apellido, ', ',e.nombre)))as name 
                             FROM empleados e 
                             inner join empleadores em on em.id = e.id_empleador 
                             WHERE (e.id_estructura = $_SESSION[structure]) and (e.activo) and (id_cargo in (0,1)) and (not borrado) 
                             ORDER BY apellido", $con);
      $i=0;
      while ($data = mysql_fetch_array($result)){?>
            choferes.push(new Array(<?php echo $data['id_empleado'];?>,  '<?php echo  htmlentities($data[name]);?>'));
      <?php
           $i++;
      }
      
      mysql_close($con);

?>
    var element;
    var campo;
	$(function() {

                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#FF8080");

                 $('#cargar').button();

                 $('#fecha').datepicker({dateFormat:'yy-mm-dd'});
                 $('#send-order').datepicker({dateFormat:'yy-mm-dd', showOn: 'both'});
                 $('input:submit').button();
                 $('#sendorderatdate').button();

                 $('#delall').button().click(function(){
                                                        if (confirm("Seguro eliminar todas las ordenes del "+$('#fecha').val()+"?")) {
                                                           if (confirm("Las ordenes eliminadas no podran ser recuperadas. Eliminar de todas maneras?")) {
                                                              $.post("/modelo/ordenes/delall.php",{fecha: $('#fecha').val()}, function(data){$('#load').submit();});
                                                           }
                                                        }
                                                        });

                 $('.selcnd').on('change', function(){
                                                      var sel = $(this);
                                                      var nroCh = sel.data('nroch'); /// 1 chofer_1, 2 chofer_2, 3 tripulacion por orden
                                                      var orden = sel.data('orden');
                                                      var trip = sel.data('trip'); /// id tripulacion por oroden
                                                      var cond = sel.val();
                                                      change(nroCh, orden, trip, cond);
                                                    });

                 $('.chvac').on('change', function(){
                                                      var sel = $(this);
                                                      var orden = sel.data('orden');
                                                      $.post("/modelo/procesa/modordressur.php",
                                                            {accion: 'chclem', id:sel.data('orden'), cli: sel.val()},
                                                            function(data){
                                                            });
                                                    });
                 $('.selcch').on('change', function(){
                                                      var sel = $(this);
                                                      var orden = sel.data('orden');
                                                      var cche = sel.val();
                                                      changeOmnibus(orden, cche);
                                                    });  
                 $('.km').on('keypress', function(e){
                                                    if(e.which == 13) {
                                                          var inp = $(this);
                                                          inp.siblings('div').html('');
                                                          $.post("/modelo/procesa/modordressur.php",
                                                                  {accion: inp.data('acc'), id:inp.data('orden'), km:inp.val()},
                                                                  function(data){
                                                                                  var result = $.parseJSON(data);
                                                                                  if (!result.ok)
                                                                                  {
                                                                                     inp.siblings('div').html('<i class="fas fa-times fa-2x"></i>');
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                    inp.siblings('div').html('<i class="fas fa-check fa-2x"></i>');
                                                                                  }
                                                                  });
                                                    }   
                                                    }); 
                 $('.hora').on('keypress', function(e){
                                                    if(e.which == 13) {
                                                          var inp = $(this);
                                                          inp.siblings('div').html('');
                                                          $.post("/modelo/procesa/modordressur.php",
                                                                  {accion:'chor', 
                                                                   id: inp.data('hhs'), 
                                                                   sl: inp.data('sl'),
                                                                   val :inp.val()},
                                                                  function(data){
                                                                                  var result = $.parseJSON(data);
                                                                                  if (!result.ok)
                                                                                  {
                                                                                     inp.siblings('div').html('<i class="fas fa-times fa-2x"></i>');
                                                                                  }
                                                                                  else
                                                                                  {
                                                                                    inp.siblings('div').html('<i class="fas fa-check fa-2x"></i>');
                                                                                  }
                                                                  });
                                                    }   
                                                    }); 
                  $('.active').on('click', function(data){
                                                            var chk = $(this);
                                                            var ok = 0;
                                                            if (chk.is(':checked'))
                                                              ok = 1;
                                                            $.post("/modelo/procesa/modordressur.php",

                                                                  {accion:'stt', id: chk.data('orden'), st:ok},
                                                                  function(data){
                                                                                  var result = $.parseJSON(data);
                                                                                  if (!result.ok)
                                                                                    alert(result.message);
                                                                  });                                                            

                  });           

	});
      function change(nroCh, orden, trip, cond){
              console.log('trip '+trip+'    numroCH '+nroCh);
               $.post("/modelo/procesa/modordressur.php",{accion:'cnd', id:orden, nc:nroCh, txo:trip, ch:cond}, function(data){
                                                                                                                                var result = $.parseJSON(data);
                                                                                                                                if (!result.ok)
                                                                                                                                {  
                                                                                                                                  alert('Se han producido errores');
                                                                                                                                  alert(result.msge);
                                                                                                                                }
                                                                                                                              });
      }

      function changeOmnibus(orden, interno){

               $.post("/modelo/procesa/modordressur.php",{accion:'cche', id:orden, int:interno}, function(data){

                                                                                                                var result = $.parseJSON(data);
                                                                                                                if (!result.ok)
                                                                                                                  alert('Se han producido errores');
                                                                                                                });
      }      

	
     function cargarComboMicros(obj){
               var i;
               if (obj.options.length < 5)
               for (i = 1; i <= micros.length; i++){
                   var elOptNew = document.createElement('option');
                   elOptNew.text = micros[i-1][1];
                   elOptNew.value = micros[i-1][0];
                   obj.options.add(elOptNew, i);
               }
      }
      
      function cargarComboChoferes(obj){
               var i;
               if (obj.options.length < 5)
               for (i = 1; i <= choferes.length; i++){
                   var elOptNew = document.createElement('option');
                   elOptNew.text = choferes[i-1][1];
                   elOptNew.value = choferes[i-1][0];
                   obj.options.add(elOptNew, i);
               }
      }
      

      function delend(val, obj){
               $.post("/modelo/procesa/modordres.php",{id:obj, value:val}, function(data){
                                                                                          if (data == "1"){
                                                                                             if (val == 1){
                                                                                                var elem = obj.split("-");
                                                                                                $.post("/modelo/enviomail/sendordbja.php",{orden: elem[1]},function(data){});
                                                                                             }
                                                                                          }
                                                                                          });
      }
      
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

.Estilo2 {	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;

}

.Estilo1 {
	font-size: 18px;
	font-weight: bold;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	background-color:#B0B0B0;
}
.Estilo3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	font-weight: bold;
	font-size:11px;
	background-color:#FFFFC0;
}
.Estilo4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color:#FFFFC0;
	font-size:11px;
}
.celda{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color:#FFFFC0;
	font-size:11px;
}
.celda:hover{
   background-color:#B0B0B0;
}

</style>
<BODY>
<?php
     menu();

     /*$ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer,if (date(citacion) < '$fecha', '00:00:00', citacion) as cita,
                        if (date(salida) < '$fecha', '00:00:00', salida) as sale,
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion) as fina,
                        if (date(llegada) > '$fecha', '23:59:00', llegada) as llega, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden,
                        id_ciudad_destino, id_estructura_ciudad_destino, 1 as conOrd, 0 as trip, o.km, o.borrada,

                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  UNION
                  SELECT o.id, o.id_chofer_2,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:00', llegada), id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 2 as conOrd, 0 as trip, o.km, o.borrada
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  union
                  SELECT o.id, id_empleado,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:00', llegada), id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 3 as conOrd, txo.id as trip, o.km, o.borrada
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)";*/     

     $ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden,
                        id_ciudad_destino, id_estructura_ciudad_destino, 1 as conOrd, 0 as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega, hhs.id as idHhs,
                        o.vacio, cl.id as idClienteVacio, cl.razon_social as razonSocialClieneVacio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  LEFT JOIN clientes cl ON cl.id = o.id_cliente_vacio AND cl.id_estructura = o.id_estructura_cliente_vacio
                  WHERE ('$fecha' between date(citacion) and date(llegada)) and (not suspendida) and (o.id_estructura = 2)                  
                  UNION
                  SELECT o.id, o.id_chofer_2, id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 2 as conOrd, 0 as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega, hhs.id as idHhs,
                        o.vacio, '' as idClienteVacio, '' as razonSocialClieneVacio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(llegada)) and (not suspendida) and (o.id_estructura = 2)
                  union
                  SELECT o.id, id_empleado, id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 3 as conOrd, txo.id as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega, hhs.id as idHhs,
                        o.vacio, '' as idClienteVacio, '' as razonSocialClieneVacio
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(llegada)) and (not suspendida) and (o.id_estructura = 2)";  

      $optionClientes = armarSelect('clientes', 'razon_social', 'id', 'razon_social', 'id_estructura = '.$_SESSION[structure], true);

?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
               <?php if ($_SESSION['userid'] == 18) print "OJO DONDE METES LOS GARFIOS!!!!<br>";?>
              <div align="center"><input id="fecha" name="fecha" value="<?php echo $fecha;?>" type="text" size="30"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">

         <div id="tablaordenes">
                            <?php
                            if ($cargar)
                            {
                                 $ordenes = "SELECT llega, orden, nomOrden, sale, 
                                                    interno, razon_social, concat(apellido,', ',nombre) as chofer, id_empleado,
                                                    c.id as id_cli, des.id as id_city, des.ciudad, o.orden as id, o.id_micro, ch.id_empleado, 
                                                    o.conOrd, o.trip, o.km, o.borrada, idHhs, vacio, idClienteVacio, razonSocialClieneVacio
                                             FROM ($ordenes) o
                                             LEFT JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
                                             LEFT JOIN empleados ch ON (ch.id_empleado = o.id_chofer)
                                             LEFT JOIN unidades m ON (m.id = o.id_micro)
                                             LEFT JOIN obsSupervisores os ON os.id_orden = o.orden
                                             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                             order by orden, conOrd";

                                 $con = conexcion(true);
                                 $query = mysqli_query($con, $ordenes) or die ($ordenes);

                                 print "<table class='table table-striped table-bordered'>";
                                 $data = array();
                                 while($row = mysqli_fetch_array($query, MYSQLI_ASSOC))
                                 {
                                    if (!array_key_exists($row['razon_social'], $data))
                                    {
                                      $data[$row['razon_social']] = array();
                                    }
                                    $data[$row['razon_social']][] = $row;
                                 }
                                 print "<table class='table table-bordered'>";
                                 $i = 0;
                                 foreach ($data as $key => $value) 
                                 {
                                    uasort($value,
                                           function($a, $b) {
                                                                    if($a['orden'] == $b['orden']) {
                                                                        return ($a['conOrd'] < $b['conOrd']) ? -1 : 1;
                                                                    }
                                                                    return ($a['orden'] < $b['orden']) ? -1 : 1;
                                                                });
                                    $lastId = null;
                                    $count = 0;
                                    $print2 = $print1 = $print3 = $print4 = false;
                                    
                                 //   $color = 'grey lighten-1';
                                    print "<thead><tr><th colspan='5' class='h5 text-center'>$key</th></tr></thead>";
                                    foreach ($value as $v) 
                                    {
                                      if ($lastId)//ha procesado al menos una orden
                                      {
                                        if ($lastId['orden'] == $v['orden']) //todabia esta procesadno un mismo grupo de ordenes
                                        {
                                          if (!$print2)//todavia no imprimio al conductor 2
                                          {
                                            $tr.="<td class='text-muted'>Conductor 2</td>
                                                  <td>
                                                          <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$v[id]' data-nroch='$v[conOrd]' data-trip='$v[trip]'>
                                                              <option value='$v[id_empleado]'>".htmlentities($v[chofer])."</option>
                                                          </select>
                                                  </td>
                                                  </tr>";
                                            $print2 = true;
                                          }
                                          elseif (!$print3) 
                                          {
                                            $tr.= "<tr class='$color'>
                                                      <td class='text-muted'>Conductor 3</td>
                                                      <td>
                                                            <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$v[id]' data-nroch='$v[conOrd]' data-trip='$v[trip]'>
                                                              <option value='$v[id_empleado]'>".htmlentities($v[chofer])."</option>
                                                            </select>
                                                      </td>";
                                            $print3 = true;
                                          }
                                          elseif(!$print4)
                                          {
                                            $tr.= "<td class='text-muted'>Conductor 4</td>
                                                      <td>
                                                            <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$v[id]' data-nroch='$v[conOrd]' data-trip='$v[trip]'>
                                                              <option value='$v[id_empleado]'>".htmlentities($v[chofer])."</option>
                                                            </select>
                                                      </td>
                                                      </tr>";
                                            $print4 = true;
                                          }
                                        }
                                        else
                                        {

                                          if (!$print3) 
                                          {
                                            $tr.= "<tr class='$color'>
                                                      <td class='text-muted'>Conductor 3</td>
                                                      <td>
                                                            <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$lastId[id]' data-nroch='3' data-trip='0'>

                                                            </select>
                                                      </td>";
                                            $print3 = true;
                                          }
                                          if (!$print4)
                                          {
                                            $tr.= " <td class='text-muted'>Conductor 4</td>
                                                    <td>
                                                          <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$lastId[id]' data-nroch='3' data-trip='0'></select>
                                                    </td>
                                                    </tr>";
                                            $print4 = true;
                                          }
                                          print $tr;
                                          $lastId = null;
                                          $print2 = $print1 = $print3 = $print4 = false;
                                          $i++;
                                        }
                                      }

                                      if (!$lastId)///primer registro del grupo de ordenes
                                      {
                                        if (($i % 2) == 0)
                                        {
                                          $color = 'grey lighten-1 ';
                                        }
                                        else
                                        {
                                          $color = 'grey lighten-2 mb-0 mt-0';
                                        }
                                        $check = '';
                                        if ($v['borrada'] == 1)
                                        {
                                              $check = 'checked';
                                        }
                                        $select = "";
                                        if ($v['vacio'])//es una orden de un vacio, debe generar la opcion para cambiar a cual cliente se le afecta
                                        {
                                            $select = "<p>
                                                          Afectar vacio a...
                                                          <select class='chvac form-control form-control-sm' data-orden='$v[id]'>
                                                            <option value='$v[idClienteVacio]'>$v[razonSocialClieneVacio]</option>
                                                            $optionClientes
                                                          </select>
                                                       </p>";
                                        }
                                        $tr = "<tr class='$color'>
                                                    <td rowspan='4' class='align-middle'>
                
                                                            <p>Servicio: 
                                                              <div class='row'>
                                                                <input type='text' title='Presione enter para realizar el cambio' value="."'".$v[nomOrden]."'"." data-orden='$v[id]' data-acc='cname' class='ml-2 col-10 km form-control form-control-sm'/>
                                                                <div class='col'></div>
                                                              </div>
                                                            </p>
                                                            <p>Cliente: $key</p>
                                                            $select
                                                            <p>
                                                              <div class='form-check'>
                                                                <input type='checkbox' class='form-check-input active'  $check data-orden='$v[id]'>
                                                                <label class='form-check-label' for='exampleCheck1'>Eliminada</label>
                                                              </div>
                                                            </p>
                                                    </td>
                                                    <td class='text-muted mb-0 mt-0'>Hora Salida</td>
                                                    <td>
                                                        <div class='row'>
                                                          <input title='Presione enter para realizar el cambio' class='hora ml-3 col-6 form_datetime form-control form-control-sm' size='16' data-sl='s' data-hhs='$v[idHhs]' type='text' value='$v[sale]'>
                                                          <div class='col'></div>            
                                                        </div>                               
                                                    </td>
                                                    <td class='text-muted mb-0 mt-0'>Hora Llegada</td>
                                                    <td>
                                                        <div class='row'>
                                                          <input title='Presione enter para realizar el cambio' class='hora col-6 form_datetime form-control form-control-sm ml-3' size='16' data-sl='l' data-hhs='$v[idHhs]' type='text' value='$v[llega]'>
                                                          <div class='col'></div>
                                                        </div>
                                                    </td>
                                               </tr>
                                               <tr class='$color'>
                                                  <td class='text-muted'>Interno</td>
                                                  <td>
                                                        <select class='selcch col-6 form-control form-control-sm' align='center' onFocus='cargarComboMicros(this)' data-orden='$v[id]'>
                                                                    <option value='$v[id_micro]'><div align='center'>$v[interno]</div></option>
                                                        </select>
                                                  </td>
                                                  <td class='text-muted'>KM</td>
                                                  <td>
                                                      <div class='row'>
                                                        <input title='Presione enter para realizar el cambio' class='km col-2 ml-3 form-control form-control-sm' size='5' data-acc='ckm' type='text' value='$v[km]' data-orden='$v[id]'>
                                                        <div class='col'></div>
                                                      </div>
                                                  </td>
                                               </tr>
                                               <tr class='$color'>
                                                    <td class='text-muted'>Conductor 1</td>
                                                    <td>
                                                          <select class='selcnd form-control form-control-sm' onFocus='cargarComboChoferes(this)' data-orden='$v[id]' data-nroch='$v[conOrd]' data-trip='$v[trip]'>
                                                              <option value='$v[id_empleado]'>".htmlentities($v[chofer])."</option>
                                                          </select>
                                                    </td>";
                                        
                                      }                                    
                                      $lastId = $v;
                                    }                                                                
                                  }
     
                                 print "</table>";
                                 mysqli_free_result($query);
                                 mysqli_close($con);
                                 }
                            ?>
         </div>
</BODY>
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/popper.min.js"></script>

  <!-- MDB core JavaScript --
    v34(".form_datetime").datetimepicker(
                                        {
                                          format: 'dd/mm/yyyy hh:ii',
                                          autoclose: true,
                                          todayBtn: true,
                                          minuteStep: 5

                                        });
  -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  
  <script src="/vista/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
  <script type='text/javascript' src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
  <script type="text/javascript">
    var v34 = $.noConflict(true);

    v34( ".form_datetime" ).inputmask({
                            mask: '99/99/9999 99:99',
                            showMaskOnHover: true,
                            showMaskOnFocus: true
                            });
  </script>
</HTML>