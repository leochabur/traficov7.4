<?php

function getLtLgInt($num_int, &$lat, &$lon, &$velocidad){

     $pos = file_get_contents('http://190.105.224.81/w3/sistemaneo/QMOVILCOMMCAR.ASP?ACCION=LOGIN&usuario=trafico2013&clave=trafico2013');

      $ini = strpos($pos, "[");
      $fin = strpos($pos, "]");

      $internos = explode("},{", substr($pos, ($ini+2), ($fin-$ini-3)));
      $cant = count($internos);
      
     $count = strlen($num_int);
     if ($count == 1){
        $int = "000$num_int";
     }
     elseif($count == 2){
        $int = "00$num_int";
     }
     elseif($count == 3){
        $int = "0$num_int";
     }
     else{
          $int = $num_int;
     }
     
     $i = 0;
     $ok = true;
     while (($i < $cant) && ($ok)){
           $str = $internos[$i];
           if (strpos($str, $int)){
              $key = $i;
              $ok = false;
           }
           $i++;
     }
     if (!$ok){
        $ll = explode(",", $internos[$key]);
        $lat = substr($ll[1], 10);
        $lon = substr($ll[2], 11);
        $velocidad = substr($ll[4], 12);
        date_default_timezone_set('UTC');
        $fecha_hora = date('d/m/Y - H:i:s');
      //  die ($lati." ".$long);
      //  exit();
       // die("la pos es $key ");
     }
     else{
          //die("no se encontro al interno");
     }
}

