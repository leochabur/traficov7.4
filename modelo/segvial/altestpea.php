<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar estaciones y precios de peajes  /////////////////////


  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'sve'){ ///codigo para guardar ////
          $ok = "";
          $campos = "id, nombre, lugar, id_estructura";
          $values = "'$_POST[nombre]', '$_POST[lugar]', $_SESSION[structure]";
          $ok = insert('estacionespeaje', $campos, $values);
          print json_encode($ok);
  }
  elseif($accion == 'spp'){
          $conn = conexcion();
          while (list($key, $value) = each($_POST)){
                if ($key != 'accion'){ //para no procesar el campo hidden con la accion
                   if ($value){           // sino se introdujo ningun valor no se procesa
                      $reg = explode('-',$key);  // la key esta formada por el id de la estacion de peaje concat con el id del tipo de unidad con el id del precio si existe
                      if ($reg[3] == 'PN')
                      {                     
                        $campos = "id, id_estructura, id_estacionpeaje, id_estructura_estacionpeaje, id_tipounidad, id_estructura_tipounidad, precio_peaje";
                        $valores= "$_SESSION[structure], $reg[0], $_SESSION[structure], $reg[1], $_SESSION[structure], $value";
                        insertOrReplace('preciopeajeunidad', $campos, $valores, "precio_peaje=$value", $reg[2]);
                      }
                      elseif($reg[3] == 'PT'){
                          $campos = "id, id_estructura, id_estacionpeaje, id_estructura_estacionpeaje, id_tipounidad, id_estructura_tipounidad, precio_telepase";
                          $valores= "$_SESSION[structure], $reg[0], $_SESSION[structure], $reg[1], $_SESSION[structure], $value";
                          insertOrReplace('preciopeajeunidad', $campos, $valores, "precio_telepase=$value", $reg[2]);
                      }
                   }
                }
          }
  }
?>

