<!DOCTYPE html>
<html lang="es">
<head>
<title>Obtener Coordenadas a partir de una direcci�n</title>
<meta charset="utf-8" />
<script type="text/javascript" src="https://maps.google.com/maps/api/js"></script>
<script>

mapa = {
 map : false,
 marker : false,

 initMap : function() {

 // Creamos un objeto mapa y especificamos el elemento DOM donde se va a mostrar.

 mapa.map = new google.maps.Map(document.getElementById('mapa{
   center: {lat: 43.2686751, lng: -2.9340005},
   scrollwheel: false,
   zoom: 14,
   zoomControl: true,
   rotateControl : false,
   mapTypeControl: true,
   streetViewControl: false,
 });

 // Creamos el marcador
 mapa.marker = new google.maps.Marker({
 position: {lat: 43.2686751, lng: -2.9340005},
 draggable: true
 });

 // Le asignamos el mapa a los marcadores.
  mapa.marker.setMap(mapa.map);

 },

// funci�n que se ejecuta al pulsar el bot�n buscar direcci�n
getCoords : function()
{
  // Creamos el objeto geodecoder
 var geocoder = new google.maps.Geocoder();

 address = document.getElementById('search').value;
 if(address!='')
 {
  // Llamamos a la funci�n geodecode pasandole la direcci�n que hemos introducido en la caja de texto.
 geocoder.geocode({ 'address': address}, function(results, status)
 {
   if (status == 'OK')
   {
// Mostramos las coordenadas obtenidas en el p con id coordenadas
   document.getElementById("coordenadas").innerHTML='Coordenadas:   '+results[0].geometry.location.lat()+', '+results[0].geometry.location.lng();
// Posicionamos el marcador en las coordenadas obtenidas
   mapa.marker.setPosition(results[0].geometry.location);
// Centramos el mapa en las coordenadas obtenidas
   mapa.map.setCenter(mapa.marker.getPosition());
   agendaForm.showMapaEventForm();
   }
  });
 }
 }
}
</script>
</head>
<body onload="mapa.initMap()">
 <h1>Obtener Coordenadas a partir de una direcci�n</h1>
 < div id="mapa" style="width: 450px; height: 350px;"> </div>
 < div><p id="coordenadas"></p></div>
 <input type="text" id="search"> <input type="button" value="Buscar Direcci�n" onClick="mapa.getCoords()">
</body>
</html>
