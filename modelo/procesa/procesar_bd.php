<?
  session_start();
  //modulo que agrega un cliente junto con sus contactos a la BD
  include ('../../controlador/ejecutar_sql.php');
  //include('ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);//reemplazar por $_SESSION['structure']

  /*$conn = conexcion();

  $sql = "SET AUTOCOMMIT=0";
  $resultado = mysql_query($sql, $conn);

  $sql = "BEGIN";          //comienza la transaccion a la BD
  $resultado = mysql_query($sql, $conn);   */

  $razon = $_POST['razon'];
  $ciudad = $_POST['ciudad'];
  $resp_iva = $_POST['resp-iva'];
  $direccion = $_POST['dir'];
  $telefono = $_POST['telefono'];
  $estructura = STRUCTURED;
  
  $campos = "id, id_estructura, razon_social, direccion, telefono, activo, id_responsabilidadIva";
  $values = "$estructura, '$razon', '$direccion', '$telefono', 1, $resp_iva";
 /* $sql = "INSERT INTO clientes (id_estructura, razon_social, direccion, telefono, activo, id_responsabilidadIva)
          VALUES ($estructura, '$razon', '$direccion', '$telefono', 1, $resp_iva)";  */

  //$resultado = insert("clientes", $campos, $values);//mysql_query($sql, $conn) or die($sql.' - '.mysql_error($conn)); //agrega el cliente a la Tabla Clientes
  //ejecutar($sql);
  //if(!$resultado) //flag para verificar la correcta insercion
    //             $error=1;
  $cliente =  insert("clientes", $campos, $values);//mysql_insert_id($conn); //obtiene el ultimo id del cliente para ealizar el acople con los contactos del mismo
  

  ////////////////comienza la carga de los contactos del clientes//////////////
  $count = count($_POST); // cantidad de argumentos recibidos
  
  $count = ($count - 3) / 5; //recupero la cantidad de contactos que se agregaron al formulario  <_____----ACTUALIZAR CON LA CANTIDAD CORRECTA DE CAMPOS ENVIADOS EN EL POST
  $campos = "id, id_cliente, id_cliente_estructura, nombre, telefono,  celular,  mail,  cargo";
  for($i = 1; $i <= $count; $i++){              //id_cliente y id_cliente_estructura son la clave primaria de la tabla Clientes
         $values = "$cliente, $estructura, '".$_POST['name_contact_'.$i]."', '".$_POST['t_fijo_contact_'.$i]."', '".$_POST['t_movil_contact_'.$i]."', '".$_POST['email_contact_'.$i]."', '".$_POST['cargo_contact_'.$i]."'";
         insert("contactosxcliente", $campos, $values);
     /*    $sql = "INSERT INTO contactosxcliente (id_cliente,
                                                id_cliente_estructura,
                                                nombre,
                                                telefono,
                                                celular,
                                                mail,
                                                cargo)
                 VALUES($cliente, $estructura, '".$_POST['name_contact_'.$i]."',
                 '".$_POST['t_fijo_contact_'.$i]."',
                 '".$_POST['t_movil_contact_'.$i]."',
                 '".$_POST['email_contact_'.$i]."',
                 '".$_POST['cargo_contact_'.$i]."')";
         $resultado = mysql_query($sql, $conn) or die($sql.' - '.mysql_error($conn));
         ejecutar($sql);  */
         /*if(!$resultado) //flag para verificar la correcta insercion
                 $error=1;  */
  }
  
  /*if($error) {      //si se produjo algun error realiza el rollback
             $sql = "ROLLBACK";
             mysql_query($sql, $conn);
             echo "0";
  }
  else {
       $sql = "COMMIT";
       mysql_query($sql, $conn);
       echo "1";
} */




  

?>

