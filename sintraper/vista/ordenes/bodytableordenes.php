<?
     $con = mysql_connect('localhost', 'root', 'leo1979');
     mysql_select_db('trafico', $con);
     
                                 $query = mysql_query("SELECT id, finalizada, date_format(hcitacion, '%h:%i') as hcitacion, date_format(hsalida, '%h:%i') as hsalida, o.nombre, concat(apellido, ', ',ch1.nombre) as chofer1
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON ((ch1.id_empleado = o.id_chofer_1) and (ch1.id_estructura = o.id_estructura_chofer1))", $con);
                                 $tbody="";
                                 while($row = mysql_fetch_array($query)){
                                            $hora=""; $cond=""; $tdclass="fin"; $divclass="fin"; //inicializamos todas las clases correspondientes a los estilos a aplicar
                                            if (!$row['finalizada']){
                                               $tdclass=""; //para no aplicar el color como finalizada
                                               $divclass="menu"; //como no esta finalizada puede mostrar el menu
                                               $hora="hora"; //para poder modificar los horarios
                                               $cond="cond"; //para poder modificar los conductores
                                            }

					                        $id = $row['id'];
					                        $tbody.= "<tr>
                                                       <td class=\"$tdclass\"><div class=\"$hora $divclass\" id=\"hcitacion-$id\">$row[hcitacion]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$hora $divclass\" id=\"hsalida-$id\">$row[hsalida]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"nombre-$id\">$row[nombre]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$cond $divclass\" id=\"id_chofer_1-$id\">$row[chofer1]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$cond $divclass\" id=\"id_diagnostico-$id\">$row[chofer2]</div></td>
                                                     </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                             print $tbody;
?>

