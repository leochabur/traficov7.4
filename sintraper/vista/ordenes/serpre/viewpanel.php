<?
  //define(RAIZ, '/nuevotrafico');
  //include ('/nuevotrafico/controlador/bdadmin.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');

  function armarSelect ($tabla, $orden, $key, $valor, $estructura, $return = 0){

           $conn = conexcion();

           if ($estructura)
              $cond = "WHERE $estructura";
           $sql = "SELECT $key, UPPER($valor) as $valor FROM $tabla $cond ORDER BY $orden";
           $result = mysql_query($sql, $conn);

           $option="";
           while ($data = mysql_fetch_array($result)){
                 $option.="<option value=\"$data[$key]\">".htmlentities($data[$valor])."</option>";
           }

           mysql_free_result($result);
           mysql_close($conn);
           if ($return)          //parche muy chancho deberia hacer return siempre
              return $option;
           else
               print $option;
  }
  
  function armarSelectCond ($estructura){
           $conn = conexcion();

           $sql = "SELECT id_empleado, concat(apellido,', ', nombre) as apenom FROM empleados WHERE (id_estructura = $estructura) and (id_cargo = 1) and (activo) ORDER BY apellido, nombre";
           $result = mysql_query($sql, $conn);

           $option="";
           while ($data = mysql_fetch_array($result)){
                 $option.="<option value=\"$data[id_empleado]\">".htmlentities($data['apenom'])."</option>";
           }
           mysql_free_result($result);
           mysql_close($conn);
           print $option;
  }
?>

