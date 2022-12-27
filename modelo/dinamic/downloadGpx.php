<?php


header('Content-disposition: attachment; filename='.$_GET['fn'].'.gpx');
header('Content-type: application/gpx');





    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://67.207.88.180:3000/recorridos/gpx/$_GET[ord]",
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_RETURNTRANSFER => 1
    ));

    $response = curl_exec($curl);
    curl_close($curl); 

    $array = json_decode($response, true);

    $file = $array['gpxFile'];

    $decode = base64_decode($file);

    $basedata =  $decode;

    $name = "./".uniqid().".zip";

    $f = fopen ($name,"x+");

    fwrite($f, $basedata);

    fclose($f);

    $zip = new ZipArchive();

    $res = $zip->open($name);

    $zip->extractTo('./');

    $zip->close();

    @unlink($name);
    

    readfile ('./file.gpx',"r");


  //  die(pathinfo($decode));

?>