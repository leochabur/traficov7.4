<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
     include ('../../controlador/bdadmin.php');
     $accion = $_POST['accion'];

     if ($accion == 'load'){//carga los datos del cliente con el id recibido en el POST
        $conn = conexcion();
        
        $sql="SELECT id, upper(responsabilidad) as resp FROM responsabilidadiva r ORDER BY resp"; //Para editar la responsabilidad se crea un select en el jeditable
        $result = mysql_query($sql, $conn);
        while ($data = mysql_fetch_array($result)){
              $array[$data['id']] = $data['resp'];
        }
        
        $cliente = $_POST['cli'];
        $sql="SELECT c.id, upper(razon_social) as razon_social, direccion, telefono, activo, cuit, upper(responsabilidad) as responsabilidad
              FROM clientes c
              inner join responsabilidadiva r on (r.id = c.id_responsabilidadIva)
              WHERE (c.id_estructura = $_SESSION[structure]) and (c.id = $cliente) ";
        $result = mysql_query($sql, $conn);
        $select = '<table border="0" align="center" width="50%">';
        if($data = mysql_fetch_array($result)){
              $id = $data['id'];
              $activo = ($data['activo']) ? "checked" : "";
              $select.="<tr>
                            <td WIDTH='20%'>Razon Social</td>
                            <td><div class='txt' id='razon_social-$id'>$data[razon_social]</div></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td WIDTH='20%'>Direccion</td>
                            <td><div class='txt' id='direccion-$id'>$data[direccion]</div></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td WIDTH='20%'>Telefono</td>
                            <td><div class='txt' id='telefono-$id'>$data[telefono]</div></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td WIDTH='20%'>CUIT</td>
                            <td><div class='cuit' id='cuit-$id'>$data[cuit]</div></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td WIDTH='20%'>Responsabilidad</td>
                            <td><div class='resp' id='id_responsabilidadIva-$id'>$data[responsabilidad]</div></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td WIDTH='20%'>Activo</td>
                            <td><input type='checkbox' $activo id='activo-$id'></td>
                            <td></td>
                        </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</table>
                   <script>

                                        $("input:checkbox").click(function(){
                                                                              var data = $(this).attr("id");
                                                                              var activo = 0;
                                                                              if ($(this).is(":checked"))
                                                                                 activo = 1;
                                                                              $.post("/modelo/clientes/abmclientes.php",{id: data, value: activo});
                                                                         });
                                        $(".txt").editable("/modelo/clientes/abmclientes.php", {cancel    : "Cancelar",
                                                                                                             submit    : "Guardar"});
                                        $(".resp").editable("/modelo/clientes/abmclientes.php",{data   : '.json_encode($array).',
                                                                                                             type   : "select",
                                                                                                             cancel    : "Cancelar",
                                                                                                             submit    : "Guardar"});
                                        $(".cuit").editable("/modelo/clientes/abmclientes.php", {type:"masked",
                                                                                                              mask: "99-99999999-9",
                                                                                                              cancel    : "Cancelar",
                                                                                                              submit    : "Guardar"});


	              </script>';
        print $select;
     }
     

?>

