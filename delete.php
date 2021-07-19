<?php

  $sql = "";

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://paxtracker.mspivak.com/api/integrations/traffic/trips/3157",
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
      "Content-Type: text/plain"
    ),
  ));

  $response = curl_exec($curl);

  $json = json_decode($response, true);
  curl_close($curl);

  print_r($response);
?>

