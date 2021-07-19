<?
  session_start();
  include ('../../controlador/bdadmin.php');
  
  $conn = conexcion();

  $est = $_SESSION['structure'];
  $orden = $_POST['orden']; //para levantar el cronograma asociado a la orden y obtener las restricciones de las licencias de conductores
  
  //consulta para recuperar las licencias q estan habilitadas a realizar e recorrido
  $micros = "SELECT id as id_micro, interno, 1 as ok FROM unidades m where (activo) and (id_estructura = $_SESSION[structure]) order by interno";

  $result = mysql_query($micros, $conn);
  $data = mysql_fetch_array($result);
  $option="<option value='0'>Ninguno</option>";
  while ($data){
        $ok = $data['ok'];
        if (!$ok){
           $back="#D8FCF8";
           $option.= "<optgroup label='Unidades Habilitadas'>";
        }
        else{
             $back="#FFC0C0";
            $option.= "<optgroup label='Unidades No Habilitadas'>";
            }
        while ($data && ($ok == $data['ok'])){
              $option.="<option style='background-color: $back' class='$ok' value='$data[id_micro]'>$data[interno]</option>";
              $data = mysql_fetch_array($result);
        }
		$option.="</optgroup>";
  }
  mysql_close($conn);
  print $option;
  
?>
