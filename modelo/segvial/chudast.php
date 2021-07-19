<?
  session_start();
  ////////////////// modulo para manejar la estructura a la cual se le afecta al coche /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'load'){ ///codigo para cargar a q estructura esta afectado el interno ////
     $ok = "";
     $conn = conexcion();
     $sql = "SELECT upper(nombre) as str
             FROM (Select id_estructura from unidades where id = $_POST[interno]) u
             inner join estructuras e on e.id = u.id_estructura";
     $result = mysql_query($sql, $conn);
     if (mysql_num_rows($result)){
        $data = mysql_fetch_array($result);
        $ok = "<table border='0' align='center' width='50%' name='tabla'>
                                <tr>
                                    <td WIDTH='20%'>Afectado actualmente a:</td>
                                    <td><b>$data[str]</b></td>
                                </tr>
                                <tr>
                                    <td WIDTH='20%'>Enviar a:</td>
                                    <td><select id='newstr'>";
        $sql = "SELECT upper(nombre) as str, id
                FROM estructuras
                order by str";
        $result = mysql_query($sql, $conn);
        while ($data = mysql_fetch_array($result)){
              $ok.="<option value='$data[id]'>$data[str]</option>";
        }
        $ok.="</select>
              </tr>
              <tr>
                  <td colspan='2' align='center'><input id='send'type='button' value='Modificar Interno '></td>
              </tr>
              </table>
              <script type='text/javascript'>
                      $('#newstr').selectmenu({width: 300});
                      $('#send').button().click(function(){
                                                           var int = $('#interno').val();
                                                           var str = $('#newstr').val();
                                                           $.post('/modelo/segvial/chudast.php', {accion: 'sendotherstr', interno: int, struct: str}, function(data){
                                                                                                                                                                     if (data == 1){
                                                                                                                                                                         $('#data').html('<b>Interno modificado con exito</b>');
                                                                                                                                                                      }
                                                                                                                                                                      else{
                                                                                                                                                                           alert('No se ha podido modificar el interno');
                                                                                                                                                                      }
                                                                                                                                                                      });
                                                           });
              </script>";
        $result = mysql_query($sql, $conn);
     }
     cerrarconexcion($conn);
     print ($ok);
  }
  elseif($accion == 'sendotherstr'){
          print update("unidades", "id_estructura", "$_POST[struct]", "(id = $_POST[interno])");
  }
?>

