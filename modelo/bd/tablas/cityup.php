<?
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, id_estructura, id_provincia, ciudad";
          $values = "$_SESSION[structure], $_POST[pcia], '$_POST[city]'";
          $ok = insert('ciudades', $campos, $values);
          print $ok;
  }
  elseif ($accion == 'load'){ ///codigo para guardar ////
          $sql = "SELECT ciudad, c.lati, c.long FROM ciudades c where id = $_POST[city] and id_estructura = $_SESSION[structure]";
          $data = ejecutarSQL($sql);
          $result = array();
          if ($row = mysql_fetch_array($data)){
             $array[status] = true;
             $array[lati] = $row[1];
             $array[long] = $row[2];
             $array[city] = $row[0];
          }
          else{
               $array[status] = false;
          }
          print (json_encode($array));
  }
  elseif ($accion == 'save'){ ///codigo para guardar ////
         $sql = "update ciudades set lati = $_, ciudades.long = 3 where id = 155555 ";
          $sql = "SELECT ciudad, c.lati, c.long FROM ciudades c where id = $_POST[city] and id_estructura = $_SESSION[structure]";
          $data = ejecutarSQL($sql);
          $result = array();
          if ($row = mysql_fetch_array($data)){
             $array[status] = true;
             $array[lati] = $row[1];
             $array[long] = $row[2];
             $array[city] = $row[0];
          }
          else{
               $array[status] = false;
          }
          print (json_encode($array));
  }
  elseif ($accion == 'upd'){ ///codigo para guardar ////
         $sql = "update ciudades set lati = $_POST[lati], ciudades.long = $_POST[long] where id = $_POST[citys] and id_estructura = $_SESSION[structure]";
       //   $sql = "SELECT ciudad, c.lati, c.long FROM ciudades c where id = $_POST[city] and id_estructura = $_SESSION[structure]";
          $data = ejecutarSQL($sql);
          print $data;
          /*
          
          $result = array();
          if ($row = mysql_fetch_array($data)){
             $array[status] = true;
             $array[lati] = $row[1];
             $array[long] = $row[2];
             $array[city] = $row[0];
          }
          else{
               $array[status] = false;
          }
          print (json_encode($array));   */
  }
?>

