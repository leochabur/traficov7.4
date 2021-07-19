<?php
    session_start();
    include ('../../controlador/ejecutar_sql.php');

    $sql = "SELECT id, upper(razon_social) FROM clientes where (activo) and (id_estructura = $_SESSION[structure]) order by razon_social";

    $result = ejecutarSQL($sql);

    $tabla='<table border="0">
                <tr>
                    <td>Cliente</td>
                    <td><select id="clientess" name="clientess">';
    while ($row = mysql_fetch_array($result)){
          $tabla.="<option value='$row[0]'>".htmlentities($row[1])."</option>";
    }

    $tabla.='</selet>
                     </td>
                     <td><input type="button" id="save" value="Guardar Cambios"></td>
                     </tr>
                     </table>
            <script>
                    $("#cliente").selectmenu({width: 350});
                    $("#save").button().click(function(){
                                                         $.post("/modelo/ordenes/asvacord.php", {orden:'.$_POST['orden'].', cliente:$("#clientess").val()});
                                                         $("#dialogo").dialog("close");
                                                         });
            </script>';
	print $tabla;
?>
