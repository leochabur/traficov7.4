<?php
     session_start();
      include ('../../controlador/bdadmin.php');
     include_once('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
       include ('../../vista/segvial/ltlgint.php');


$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="/vista/js/validate-form/jquery.validate.min.js"></script>

  
  
<script type="text/javascript" src="/vista/js/validate-form/jquery.metadata.js"></script>
<style type="text/css">

table.order thead tr th {
background-color: #e6EEEE;
border: 1px solid #FFF;
font-size: 8pt;
padding: 4px;}
table.order tbody tr td {
background-color: #e6EEEE;
border: 1px solid #FFF;
font-size: 8pt;
padding: 4px;}

</style>';

$lat=0;
$long=0;
$vel=0;

$orden = explode("-", $_POST[orden]);
$conn = conexcion();
$sql = "SELECT p.id, r.valor, r.observaciones, upper(apenom) as usu, date_format(hora_chequeo, '%d/%m/%Y - %H:%i') as fecha, latitud, longitud
       FROM preguntas_chequeo p
       inner join respuestas_chequeo r on r.id_pregunta = p.id
       inner join chequeo_ordenes ch on ch.id = r.id_chequeo
       inner join usuarios u on u.id = ch.id_user
        inner join (select id, id_estructura from ordenes where id = $orden[1] and id_estructura = $_SESSION[structure]) o on o.id = ch.id_orden and o.id_estructura = ch.id_estructura_orden";
//die($sql);
$result = ejecutarSQL($sql, $conn);
$respuestas = array();
$readonly="";
$ok=true;
while ($row = mysql_fetch_array($result)){
   $lat = $row[latitud];
   $long = $row[longitud];
   $usuario = $row[usu];
   $fecha = $row[fecha];
   $respuestas[$row[id]] = array($row[1], $row[2]);
   $readonly="readonly";
   $ok = false;
}

if ($_POST[cche]){
   if ($ok){
      try {
        //   getLtLgInt($_POST[cche], $lat, $long, $vel);
        $lat=0;
        $long=0;
        $vel=0;
      }
      catch (Exception $e) {
                           //die($e->getMessage());
                           }
   }
}

$tabla.="<form id='form1'>
               <input type='hidden' name='coche' id='coche' value='$_POST[cche]'/>
               <input type='hidden' name='lati' id='lati' value='$lat'/>
               <input type='hidden' name='longi' id='longi' value='$long'/>
               <input type='hidden' name='velo' id='velo' value='$vel'/>
               <table width='100%' class='order' id='rptas'>
                <thead>
                    <tr>
                        <th colspan='3' align='center'>Chequeo de orden: <u><b><i><h2>$_POST[srv]</h2></i></b></u></th>
                    </tr>
                <tr>
                    <th align='center'>Opcion</th>
                    <th align='center'>Rpta</th>
                    <th align='center'>Observaciones</th>
                </tr>
                </thead>
                <tbody>";
                
$sql="SELECT id, pregunta
      FROM preguntas_chequeo
      where activa";
    //  die($sql);
$result = ejecutarSQL($sql, $conn);
$row = mysql_fetch_array($result);
while ($row){
      $complete="";
      if ($row[id] == 1)
         $complete = "<font size='3'>$_POST[cond]</font>";
      if ($row[id] == 3)
         $complete = "<font size='2'>$_POST[srv]</font>";
      if ($row[id] == 6)
         $complete = "<font size='3'>  ($_POST[sale])</font>";
      if ($row[id] == 7)
         $complete = "<font size='3'>  ($_POST[llega])</font>";


      $si = ($respuestas[$row[id]][0])?"checked":"";
      $no = (isset($respuestas[$row[id]][0])&&($respuestas[$row[id]][0]==0))?"checked":"";
      $obs = $respuestas[$row[id]][1];
      $tabla.="<tr id='$row[id]'>
                   <td>$row[pregunta]  <b>$complete</b></td>
                   <td align='center'>Si<input type='radio' name='chk$row[id]' required value='1' $si>No<input type='radio' name='chk$row[id]' value='0' $no> </td>
                   <td align='center'><input type='text' size='45' class='ui-widget ui-widget-content  ui-corner-all' id='obs$row[id]' value='$obs' $readonly></td>
      </tr>";
      $row = mysql_fetch_array($result);

}
$tabla.="</table>";
if ($usuario){
   $tabla.="<table class='order' width='100%'><tr><td colspan='3'>La orden ya ha sido chequeada por <font color='#FF0000'><b><i><u>$usuario el $fecha</u></i></b></font></td></tr></tbody></table>";
}
else{
     $tabla.="<table class='order' width='100%'><tr><td colspan='3' align='right'><input type='submit' value='Chequear Orden' id='btchk'></td></tr></tbody></table>
         <input type='hidden' name='orden' id='orden' value='$orden[1]'/>";
}
$tabla.="</form>
         <div id='map_canvas' style='width:100%; height:50%'></div>
<script>
        $('#btchk').button();
        $('#form1').validate({
                              submitHandler: function(form) {
                                                             var preg_rpta = new Array();
                                                             $('#btchk').hide();
                                                             $('#rptas tbody tr').each(function(i){
                                                                                                   var preg = $(this).attr('id');
                                                                                                   var rpta = $('input:radio[name=chk'+preg+']:checked').val();
                                                                                                   var obs = $('#obs'+preg).val();
                                                                                                   var obj = {};
                                                                                                   var data_rpta = {};
                                                                                                   data_rpta[0] = rpta;
                                                                                                   data_rpta[1] = obs;
                                                                                                   obj[preg] = data_rpta;
                                                                                                   preg_rpta.push(obj);
                                                                                                   });
                                                                                                   var respuestas = JSON.stringify(preg_rpta);
                                                                                                   console.log(respuestas);
                                                                                                   
                                                                                                   
                                                                                                   $.ajax({
                                                                                                           type: 'POST',
                                                                                                           dataType: 'json',
                                                                                                           data: {data:respuestas, orden: $('#orden').val(), coche:$('#coche').val(), lat:$('#lati').val(), long:$('#longi').val()},
                                                                                                           url: '/modelo/ordenes/checkord.php'
                                                                                                   }).done( function(data) {
                                                                                                                           if (data.estado){
                                                                                                                              $('#$orden[0]-$orden[1]').attr('src','../check_si.png');
                                                                                                                              $('#trchk'+$orden[1]).each(function () {
                                                                                                                                                                      $(this).removeClass('fin susp scas');
                                                                                                                                                                      $(this).addClass('check');
                                                                                                                                                                      $(this).css('background-color', '#C0FFFF');
                                                                                                                                                                      });
                                                                                                                              $('#dialog').dialog('close');
                                                                                                                           }
                                                                                                                           else{
                                                                                                                                alert(data.sql);
                                                                                                                                $('#btchk').show();
                                                                                                                           }
                                                                                                                           });

                                                            }
                              });

                              var myLatlng = new google.maps.LatLng($lat,$long);
                              var mapOptions = {
                                               zoom: 16,
                                               center: myLatlng,
                                               mapTypeId: google.maps.MapTypeId.ROADMAP
                                               }
                              var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

                              var marker = new google.maps.Marker({
                                                                   position: myLatlng,
                                                                   map: map,
                                                                   title: '$_POST[cche]',
                                                                   icon: '/vista/bus-gps.png'
                                                                   });

</script>";

             @mysql_free_result($query);
             @mysql_close($con);
print $tabla;
?>

