<?
  session_start();
  //modulo que agrega restricciones al cliente
  include ('../../controlador/ejecutar_sql.php');
  $cli = $_POST['cliente'];

  foreach($_POST as $campo => $valor){
      $campo = explode('-', $campo);
      if ($campo[0] == 'restipo'){
         if ($valor == '1'){
            $campos = "id, id_estructura, id_cliente, id_estructuracliente, id_tipounidad, id_estructura_tipounidad";
            $values = "$_SESSION[structure], $_POST[cliente], $_SESSION[structure],  $campo[1], $_SESSION[structure]";
            $ok. = insert("restclienteunidad", $campos, $values);

         }
         else{
              $campos = "id_estructura, id_cliente, id_estructuracliente, id_tipounidad, id_estructura_tipounidad";
              $values = "$_SESSION[structure], $_POST[cliente], $_SESSION[structure],  $campo[1], $_SESSION[structure]";
              $ok. = delete("restclienteunidad", $campos, $values);
         }
      }
  }
  print $cli.$sql;


?>

