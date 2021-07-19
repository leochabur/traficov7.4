<?
  session_start();
  include_once ('../../controlador/bdadmin.php');
  
  $accion= $_POST['accion'];

  if($accion == 'lintsvto'){
       $conn = conexcion();
       $sql = "SELECT e.id_empleado,
                      legajo,
                      upper(concat(apellido, ', ',nombre)) as apenom,
                      nrodoc,
                      upper(razon_social) as empleador,
                      u.apenom as user,
                      date_format(fecha_alta, '%d/%m/%Y - %H:%i') as hsalida
              FROM empleados e
              left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
              left join usuarios u on u.id = e.usuario_alta_provisoria
              where (e.activo) and (not e.procesado)
              order by empleador, apellido";

       $result = mysql_query($sql, $conn);
       $asignadas = "<fieldset>
                    <legend>Codncutores pendientes de confirmacion</legend>
                    <table id='ordasig' class='tablesorter'>
                            <thead>
                            <tr>
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Documento</th>
                                <th>Empleador</th>
                                <th>Usuario Alt</th>
                                <th>Fecha - Hora Alta</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr>
                              <td>$data[1]</td>
                              <td>$data[2]</td>
                              <td>$data[3]</td>
                              <td>$data[4]</td>
                              <td>$data[5]</td>
                              <td>$data[6]</td>
                          </tr>";
       }
       $asignadas.="</tbody>
                    </table>
                    </fieldset>";
       mysql_free_result($result);
       mysql_close($conn);
       print $asignadas;
  }
  elseif($accion == 'consva'){
       $conn = conexcion();
       $sql = "SELECT e.id_empleado,
                      legajo,
                      upper(concat(apellido, ', ',nombre)) as apenom,
                      nrodoc,
                      upper(razon_social) as empleador
              FROM empleados e
              left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
              left join usuarios u on u.id = e.usuario_alta_provisoria
              where (id_cargo = 1) and (e.activo) and (not e.borrado) and (not e.borrado) and (em.activo) and (id_empleado not in (SELECT id_conductor FROM licenciasxconductor where id_conductor = id_empleado))
              order by apellido";

       $result = mysql_query($sql, $conn);
       $asignadas = "<fieldset>
                    <legend>Codncutores sin vencimientos asignados</legend>
                    <table id='ordasig' class='tablesorter'>
                            <thead>
                            <tr>
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Documento</th>
                                <th>Empleador</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr>
                              <td>$data[1]</td>
                              <td>$data[2]</td>
                              <td>$data[3]</td>
                              <td>$data[4]</td>
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

