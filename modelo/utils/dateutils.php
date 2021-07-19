<?php
     function dateToMysql($date, $separator){
              if ($date){
                 $fecha = explode($separator, $date);
                 return "$fecha[2]-$fecha[1]-$fecha[0]";
              }
              else
                 return "";
     }
     function dateTimeToMysql($dateTime, $separator){
              if ($dateTime){
                 $fechaHora = explode(" ", $dateTime);
                 return dateToMysql($fechaHora[0], $separator)." ".$fechaHora[1];
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
     
     function dateTimeToMonthYearMysql($date, $separator){
              if ($date){
                 $fecha = explode($separator, $date);
                 return "$fecha[1]-$fecha[0]";
              }
              else
                 return "";
     }
     
    function daysOfMonth($my="mm yyyy", $separator){
             $fecha = explode($separator, $my);
             $numDays = cal_days_in_month (CAL_GREGORIAN, $fecha[0],$fecha[1]);
             return $numDays;
    }
    
    function monthBetweenDate($from, $to){
             date_default_timezone_set('America/Argentina/Buenos_Aires');
             $from = new DateTime("$from 00:00:00");
             $to = new DateTime("$to 23:59:59");
             $diff = $from->diff($to);
             $monts = ( $diff->y * 12 ) + $diff->m;
             return $monts;
    }
?>
