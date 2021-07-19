<?php
     function dateToMysql($date, $separator){
              if ($date){
                 $fecha = explode($separator, $date);
                 return "$fecha[2]-$fecha[1]-$fecha[0]";
              }
              else
                 return "";
     }
     
     function dateToJS($date, $separator){
              if ($date){
                 $fecha = explode($separator, $date);
                 return "$fecha[2]/$fecha[1]/$fecha[0]";
              }
              else
                 return "";
     }
?>
