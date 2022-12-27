<?php
  set_time_limit(0);

  //$payload = json_encode($export);

  $data =
                         array("busID" => 2023,"tripID" => 'CALETA',"wptID" => 'Plaza ppal',
                              "lat" =>  '-35.17170789075491',"lon" => '-58.22501465230616',"duration" => 1643459718);

  
              $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://master.local/api/v1/rest.php/guardar");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $response = curl_exec($curl);
            curl_close($curl);/*
  //print json_encode($data);
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://master.local/api/v1/rest.php/guardar",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => '{
  "busID": 2023,
  "tripID": "CALETA",
  "wptID": "Plaza ppal",
  "lat": "-35.17170789075491",
  "lon": "-58.22501465230616",
  "arrival": 1643459718,
  "duration": 1643459718
}',
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_HTTPHEADER => array(
          "Content-Type: application/json"
        )
  ));
  $response = curl_exec($curl);

  $json = json_decode($response, true);
  curl_close($curl);     */
  print_r($response);

?>

