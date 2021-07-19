<?php
  set_time_limit(0);

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://traficonuevo.masterbus.net/api/v1/index.php",
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_RETURNTRANSFER => 1
  ));
  $response = curl_exec($curl);

  $json = json_decode($response, true);
  curl_close($curl);
  print_r($response);

?>

