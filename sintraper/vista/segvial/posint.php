<?php
     $link = mssql_connect('190.105.232.111', 'master', 'master2014') or die('error al conectar');
     if (!$link) {
        die('No pudo conectar al servidor GPS');
     }
     mssql_select_db('dbremota', $link);
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
     
     //,
     $result = mssql_query ("select mo_lat as latitud, mo_lon as longitud, mo_velocidad as velocidad,
                                   convert(VARCHAR, mo_fecpos, 103) as fec, convert(VARCHAR, mo_fecpos, 108) as hora
                             from movilesmaster where mo_idcliente = 'int - $int'", $link) or die (mssql_get_last_message());

     if ($data = mssql_fetch_array($result)){
        $lati=$data['latitud'];
        $long=$data['longitud'];
     }
     mssql_close($conn);
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
  Interno: <?php print ($_GET['int']);?><br>
  Velocidad: <?php print ($data['velocidad']);?> <br>
  Fecha - Hora: <?php print ("$data[fec] - $data[hora]");?><br>
  </div>
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
