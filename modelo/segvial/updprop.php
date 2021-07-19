<?
  session_start();
  ////////////////// modulo para manejar la estructura a la cual se le afecta al coche /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'load'){ ///codigo para cargar a q empleador esta afectado el interno ////
     $ok = "";
     $conn = conexcion();
     $sql = "SELECT upper(razon_social), date_format(propietario_desde, '%d/%m/%Y - %H:%i'), apenom
             FROM propietariounidad pu
             inner join empleadores p on p.id = pu.id_propietario
             inner join unidades u on u.id = pu.id_unidad
             inner join usuarios us on us.id = pu.usuario_alta
             where u.id = $_POST[interno]";
     $result = mysql_query($sql, $conn);
     if (mysql_num_rows($result)){
        $ok = "<table border='0' align='center' width='100%' name='tabla' class='ui-widget ui-widget-content'>
                                <tr class='ui-widget-header'>
                                    <td>Propitario</td>
                                    <td>Desde el:</td>
                                    <td>Cargado por:</td>
                                    <td>Eliminar</td>
                                </tr>
                                <tr>";

        while ($data = mysql_fetch_array($result)){
              $ok.="<tr>
                        <td>".htmlentities($data[0])."</td>
                        <td>$data[1]</td>
                        <td>$data[2]</td>
                        <td></td>
                   </tr>";
        }
        $ok1.=" <tr><td colspan='4'></td></tr>
              </table>
              <br>
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
     }
     cerrarconexcion($conn);
     print ($ok);
  }
  elseif($accion == 'sendotherstr'){
          print update("unidades", "id_estructura", "$_POST[struct]", "(id = $_POST[interno])");
  }
  elseif($accion == 'updprop'){
          $prop = $_POST['emp'];
          $coche = $_POST['int'];
          $fecha = dateToMysql($_POST['des'], '/');
          $hora = $_POST['hor'];
          print insert("propietariounidad", "id, id_propietario, id_unidad, propietario_desde, fecha_alta, usuario_alta", "$prop, $coche, '$fecha $hora:00', now(), $_SESSION[userid]");
  }
  
?>

