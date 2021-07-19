<?php
  $dsn = 'DRIVER={SQL Server};SERVER=190.105.232.111,1433;Database=CONSAT;';
  $conn = odbc_connect($dsn,'master','master2013') or die('ODBC Error:: '.odbc_error().' :: '.odbc_errormsg().' :: '.$dsn);
  $result = odbc_exec($conn, "select * from movilesMaster where movil = 'int - 0210'");

  if ($data = odbc_fetch_array($result)){
                     $lati=$data['latitud'];
                     $long=$data['longitud'];
  }
  odbc_close($conn);
  phpinfo();
  die();
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
    zoom: 18,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: '<?php print $data['movil'];?>',
       icon: '/vista/bus-gps.png'
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
