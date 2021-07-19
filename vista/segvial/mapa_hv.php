<?php
    include ('../main.php');
   include_once ('../../controlador/ejecutar_sql.php');
   $sql = "select concat('Hecho Numero ',s.id, ' - Fecha: ',date_format(fecha, '%d/%m/%Y'), ' - Interno: ', interno) as detalle, latitud, longitud, s.id
FROM hechos_vandalicos s
inner join ciudades c on c.id = s.lugar_incidente
left join unidades u on u.id = s.id_micro
where (latitud is not null) and (longitud is not null) and fecha between '$_GET[ds]' and '$_GET[hs]'";
  // die($sql);
   $result = ejecutarSQL($sql);
encabezado("");
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <style type="text/css">
    #mapa { height: 500px; }
    </style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDv8b-qrzRkI9kvtJV6l3Lko-mCdrGh7oE&sensor=false"></script>
    <script type="text/javascript">
    function initialize() {
      var marcadores = [
      <?php
           $i=0;
           while ($row = mysql_fetch_array($result)){
                 if ($i == 0){
                    echo "['$row[0]', $row[1], $row[2], $row[3]]";
                 }
                 else{
                    echo ",['$row[0]', $row[1], $row[2], $row[3]]";
                 }
                 $i++;
        }
       ?>
      ];
      var map = new google.maps.Map(document.getElementById('mapa'), {
        zoom: 9,
        center: new google.maps.LatLng(-34.112051200000000, -59.058475499999986),
        mapTypeId: google.maps.MapTypeId.ROADMAP
      });
      var infowindow = new google.maps.InfoWindow();
      var marker, i;
      for (i = 0; i < marcadores.length; i++) {
        marker = new google.maps.Marker({
          position: new google.maps.LatLng(marcadores[i][1], marcadores[i][2]),
          map: map
        });
        google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
          return function() {
            infowindow.setContent(marcadores[i][0]);
            infowindow.open(map, marker);
          }
        })(marker, i));
        
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
           return function() {
                             $.post('/modelo/segvial/readhv.php', {accion: 'detsin', sin:marcadores[i][3]}, function(data){$('#sin').html(data);})
           }
        })(marker, i));
        
        
      }
    }
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>

    <div id="mapa"></div>
    <div id="sin"></div>
  </body>
</html>
