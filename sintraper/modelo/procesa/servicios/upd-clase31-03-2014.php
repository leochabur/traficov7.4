<?
  session_start();
  include ('../../../controlador/bdadmin.php');
  define(STRUCTURED, $_SESSION['structure']);
  $conn = conexcion();
  $data  = explode("-",$_POST['id']);

  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar


  $query = mysql_query("UPDATE cronogramas SET claseServicio_id = $value, claseServicio_id_estructura = $_SESSION[structure] WHERE (id = $id) and (id_estructura = $_SESSION[structure])");

  $sql = "SELECT clase FROM claseservicio where id = $value and id_estructura = $_SESSION[structure]";
  $resu = mysql_query($sql, $conn);
  $value='';
  if ($data = mysql_fetch_array($resu)){
     $value = $data[0];
  }

  mysql_close($conn);
  echo $value;
?>

