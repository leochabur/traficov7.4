<?php
     function nombremes($mes){
              setlocale(LC_TIME, 'spanish');
              $nombre=strftime("%B",mktime(0, 0, 0, $mes, 1, 2000));
              setlocale(LC_TIME, 'English_United_States');
              return $nombre;
     }
?>
