<?php
  set_time_limit(0);

      $payload = array('bus' => 234, 'fecha' => '2020-11-27T16:40:00');
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://traficonuevo.masterbus.net/api/v1/close".'?'.http_build_query($payload),
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_HTTPHEADER => array(
          "Authorization: 3d524a53c110e4c22463b10ed32cef9d",
          "Content-Type: text/plain"
        ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      
      print $response;
     /* $error = 'REPORTADO';
      $message = '';
      if (!$json['success'])
      {
        $error = 'NO REPORTADO';
        $message = ' - Desc. Error ';
        foreach ($json['errors'] as $e)
        {
          $message.= "$e,";
        }
      }

      print "Numero orden $row[idOrden] => Fecha: $row[fservicio] - Servicio: $row[nombre]  -  Estado: $error $message<br>";
      print "</br>";
  }
  
  //die(print_r($export));
 // mysqli_free_result($result);
  
//print_r($export);
  //print_r($export);
  //print "<br>";
  //https://admtickets.masterbus.net/api/integrations/traffic/trips
  //http://paxtracker.mspivak.com/api/integrations/traffic/trips
//  $payload = json_encode($export);

  //print_r($payload);

  $i=0;

 /* foreach ($export as $orden)
  {
    if (!$orden[])
    print_r($orden);
    print "<br><br>";
 /*     $payload = json_encode(array($orden));
    
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://paxtracker.mspivak.com/api/integrations/traffic/trips",
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"{'trips':$payload}",
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
          "Content-Type: text/plain"
        ),
      ));
      $response = curl_exec($curl);

      $json = json_decode($response, true);
      curl_close($curl);
      print "Numero procesamiento $i - Numero orden $orden[idOrden] => ".$response."<br>";*/
 // }
 // $i++;
 // }


?>

