<?php
  set_time_limit(0);

  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

  $conn = conexcion(true);


         $sql = "select id
from ordenes
where fservicio > now() and borrada and nombre like '%rondin%'";



$result = mysqli_query($conn, $sql);

  while ($row = mysqli_fetch_array($result))
  {
      $id = $row['id'];
      $curl = curl_init(); 
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips/".$id,
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
      print_r($json);
      print "<br>";
  }

?>

