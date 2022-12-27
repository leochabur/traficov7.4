<?php
  include ('../../controlador/bdadmin.php');

  function armarSelect ($tabla, $orden, $key, $valor, $estructura){
           $conn = conexcion(true);
           if ($estructura)
              $cond = "WHERE $estructura";
           $sql = "SELECT * FROM $tabla $cond ORDER BY $orden";
           $result = mysql_query($sql, $conn);
           $option="";
           while ($data = mysql_fetch_array($result)){
                 $option.="<option value=\"$data[$key]\">$data[$valor]</option>";
           }
           print $option;
  }
?>

