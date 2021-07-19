<?
  session_start();
  include_once ('../../controlador/bdadmin.php');
  
  $accion= $_POST['accion'];

  if($accion == 'lintsvto'){
       $conn = conexcion();
       $sql = "SELECT u.id, u.interno, u.patente, upper(razon_social) as razon_social
               FROM unidades u
               inner join empleadores e on e.id = u.id_propietario
               where (u.id not in (SELECT idunidad FROM tipovencimientoporinterno group by idunidad)) and (u.activo) and (e.activo)
               order by interno";

       $result = mysql_query($sql, $conn);
       $asignadas = "<fieldset>
                    <legend>Internos sin Vencimientos Asignados </legend>
                    <table id='ordasig' class='tablesorter' width='75%'>
                            <thead>
                            <tr>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Propietario</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr id='$data[id]'>
                              <td>$data[interno]</td>
                              <td>$data[patente]</td>
                              <td>$data[razon_social]</td>
                          </tr>";
       }
       $asignadas.="</tbody>
                    </table>
                    </fieldset>";
       mysql_free_result($result);
       mysql_close($conn);
       print $asignadas;
  }
  
?>

