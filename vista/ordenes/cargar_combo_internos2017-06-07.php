<?
  session_start();
  include ('../../controlador/bdadmin.php');
       set_time_limit(0);
     error_reporting(0);
  $conn = conexcion();

  $est = $_SESSION['structure'];
  $orden = $_POST['orden']; //para levantar el cronograma asociado a la orden y obtener las restricciones de las licencias de conductores
  $sql = "SELECT o.id_cliente, o.id_estructura_cliente FROM ordenes o WHERE o.id = $orden";
  $result=mysql_query($sql, $conn);
  if ($data = mysql_fetch_array($result)){
     $cliente = $data[id_cliente];
     $str = $data[id_estructura_cliente];
  }
 // if ($_SESSION['structure'] == 1){
  $sql = "select id_coche, interno, 1 as disponible
          from (
          SELECT u.id as id_coche, interno, id_tipounidad, id_estructura_tipounidad, r.id_cliente, r.id_estructuracliente
          FROM restcochesclientes r
          inner join (select * from unidades where id_estructura = $str) u on u.id = r.id_coche
          left join restricciongralcliente rgc on rgc.id_cliente = r.id_cliente and rgc.id_estructuraCliente = r.id_estructuracliente
          where permitido and r.id_cliente = $cliente and (if (ant_max is null, true ,(year(now()) - anio) <= ant_max)) and activo ) o
          inner join (select * from restclientetipounidad where id_tipovto is null) rctu on rctu.id_cliente = o.id_cliente and o.id_estructuracliente = rctu.id_estructuracliente and rctu.id_tipounidad = o.id_tipounidad and rctu.id_estructura_tipounidad = o.id_estructura_tipounidad
          union all
          select id, interno, 0 as disponible
          from unidades u
          where activo and id_estructura = $str
          order by disponible DESC, interno";
 // die($sql);
 // }
 // else{
 // $sql = "SELECT id as id_coche, interno, 1 as disponible FROM unidades where (activo) and (id_estructura = $_SESSION[structure]) order by interno";
 // }

   $result = mysql_query($sql, $conn);
   $data = mysql_fetch_array($result);
   $coches = array();
   $option="<option value='0'>Ninguno</option>";
   while ($data){
         $disp = $data[disponible];
         if ($disp){
            $back="#D8FCF8";
            $option.= "<optgroup label='Unidades Habilitadas'>";
         }
         else{
              $back="#FFC0C0";
              $option.= "<optgroup label='Unidades No Habilitadas'>";
         }
         while (($data) && ($disp == $data[disponible])){
               if ($disp){
                  $option.="<option style='background-color: $back' value='$data[id_coche]'>$data[interno]</option>";
                  array_push($coches, $data[id_coche]);
               }
               else{
                    if (!in_array($data[id_coche], $coches)){
                       $option.="<option style='background-color: $back' value='$data[id_coche]'>$data[interno]</option>";
                    }
               }
               $data = mysql_fetch_array($result);
         }
   		$option.="</optgroup>";
   }
  
  //consulta para recuperar las licencias q estan habilitadas a realizar e recorrido
 /* $micros = "SELECT id as id_micro, interno, 1 as ok FROM unidades m where (activo) and (id_estructura = $_SESSION[structure]) order by interno";

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
  }  */
  mysql_close($conn);
  print $option;
  
?>
