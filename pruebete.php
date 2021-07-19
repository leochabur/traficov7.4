<HTML>
<head>
<title>Obtener Coordenadas a partir de una dirección</title>
<meta charset="utf-8" />
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDv8b-qrzRkI9kvtJV6l3Lko-mCdrGh7oE&sensor=false">
    </script>
<script>

var myLatlng = new google.maps.LatLng(40.65599461,-4.69373720);
var mapOptions = {
  zoom: 12,
  center: myLatlng,
  mapTypeId: google.maps.MapTypeId.ROADMAP
};

map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
</script>
</head>
<body onload="mapa.initMap()">
 <h1>Obtener Coordenadas a partir de una dirección</h1>
 <div id="mapa" style="width: 450px; height: 350px;"> </div>
 <div><p id="map_canvas"></p></div>
 <input type="text" id="search"> <input type="button" value="Buscar Dirección" onClick="mapa.getCoords()">
</body>
</html>
