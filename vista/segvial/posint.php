<?php
//header('Content-type: application/json; charset=utf-8');

   /*  $pos = file_get_contents('http://190.105.224.81/w3/sistemaneo/QMOVILCOMMCAR.ASP?ACCION=LOGIN&usuario=trafico2013&clave=trafico2013');

      $ini = strpos($pos, "[");
      $fin = strpos($pos, "]");
  //    print_r(preg_split("[{ }]", $pos));
      $internos = explode("},{", substr($pos, ($ini+2), ($fin-$ini-3)));
      $cant = count($internos);
      
     $count = strlen($_GET['int']);
     if ($count == 1){
        $int = "000$_GET[int]";
     }
     elseif($count == 2){
        $int = "00$_GET[int]";
     }
     elseif($count == 3){
        $int = "0$_GET[int]";
     }
     else{
          $int = $_GET['int'];
     }
     
     $i = 0;
     $ok = true;
     while (($i < $cant) && ($ok)){
           $str = $internos[$i];
           if (strpos($str, $int)){
              $key = $i;
              $ok = false;
           }
           $i++;
     }
     if (!$ok){
        $ll = explode(",", $internos[$key]);
        $lati = substr($ll[1], 10);
        $long = substr($ll[2], 11);
        $velocidad = substr($ll[4], 12);
        date_default_timezone_set('UTC');
        $fecha_hora = date('d/m/Y - H:i:s');
      //  die ($lati." ".$long);
      //  exit();
       // die("la pos es $key ");
     }
     else{
          die("no se encontro al interno");
     }
      
      
  //print "$ini $fin ".substr($pos, ($ini+2), ($fin-$ini-3));
     // echo "popo $pos".json_decode($pos);
   //   exit();

   /* echo (json_encode($pos));
    exit();
     $json = json_encode($pos);
     die($json);
     die("position ".strpos($pos, '[')." ". strrpos($pos,']'));

     $coches = explode("{", $pagina_inicio);

     die(print_r($coches[2]));  */
     



  /*   $link = mssql_connect('190.105.224.81', 'trafico2013', 'trafico2013') or die('error al conectar');
     if (!$link) {
        die('No pudo conectar al servidor GPS');
     }
     
     mssql_select_db('dbremota', $link);

     
     //,
     $result = mssql_query ("select mo_lat as latitud, mo_lon as longitud, mo_velocidad as velocidad,
                                   convert(VARCHAR, mo_fecpos, 103) as fec, convert(VARCHAR, mo_fecpos, 108) as hora
                             from movilesmaster where mo_idcliente = 'int - $int'", $link) or die (mssql_get_last_message());

     if ($data = mssql_fetch_array($result)){
        $lati=$data['latitud'];
        $long=$data['longitud'];
     }
     mssql_close($conn);   */
require_once ('lib/nusoap.php'); // Libreria SOAP

function wsGetSoapCli(){
	return new nusoap_client('https://app.urbetrack.com/App_services/Operation.asmx?wsdl', true);
}

		$oSoapSClient = wsGetSoapCli();
        $params = array();
        $params['usuario'] = 'masterbus_trafico';
        $params['hash'] = '85CF3EC9C355539B74F36AB7D03BBC1C';
        $params['interno'] = "$_GET[int]";
		$resultado = $oSoapSClient->call('ApiGetLocationByVehicle', $params );
        $lati =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Latitud'];
        $long =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Longitud'];
        $int =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Interno']." (".$resultado['ApiGetLocationByVehicleResult']['Resultado']['Patente'].")";
        $velocidad = $resultado['ApiGetLocationByVehicleResult']['Resultado']['Velocidad'];
        $fecha_hora =  $resultado['ApiGetLocationByVehicleResult']['Resultado']['Fecha'];
?>
<HTML>
<HEAD>
 <TITLE>New Document</TITLE>
                         <script type="text/javascript" src="/vista/js/jquery-1.7.2.min.js"></script>
                        <script type="text/javascript" src="/vista/js/jquery-ui-1.8.22.custom.min.js"></script>
    <link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100% }
    </style>
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDv8b-qrzRkI9kvtJV6l3Lko-mCdrGh7oE&sensor=false">
    </script>
    <script type="text/javascript">
function initialize() {
  var myLatlng = new google.maps.LatLng(<?php print $lati;?>, <?php print $long;?>);
  var mapOptions = {
    zoom: 16,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: '<?php print $_GET['int'];?>',
       icon: '/vista/bus-gps.png'
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body onload="initialize()">
  <div>
  Interno: <?php print ($int);?><br>
  Velocidad: <?php print ($velocidad);?> <br>
  Fecha - Hora: <?php print ($fecha_hora);?><br>
  </div>
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
