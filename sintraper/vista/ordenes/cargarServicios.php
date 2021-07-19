<?php
session_start();

if(!$_SESSION["auth"] or $_SESSION["nivel"]<2) header("Location: ../index.php?e=true");
include('../data.php');
include('fechas.php');
$query="SELECT o.id_orden, s.id_servicio, c.nombre, c.nombrecliente, s.hcitacion, s.hsalida, m.interno, ch.apenom
        FROM (SELECT * FROM ordenes WHERE fservicio = '".cambiaf_a_mysql($_POST['fecha'])."') o
        inner join servicios s on s.id_servicio = o.id_servicio
        inner join cronogramas c on c.codigo = s.cod_cronograma
        inner join micros m on m.id_micro = o.id_micro
        inner join choferes ch on (ch.id_chofer = o.id_chofer1)";
$conn = conectar_mysql() or die(alerta(mysql_error()));
$result = mysql_query($query, $conn);
$tabla = " <script language=\"JavaScript\">
           jq13(document).ready(function(){
                                       jq13(\"#tablita tr:odd\").css(\"background-color\", \"#ddd\");
                                       jq13(\"#tablita tr:even\").css(\"background-color\", \"#ccc\");
          });
          </script>
<table align=\"center\" width=\"67%\" id=\"tablita\">
          <tr>
              <td>Si/No</td>
              <td>Cronograma</td>
              <td>Cliente</td>
              <td>H. citacion</td>
              <td>H. salida</td>
              <td>Interno</td>
              <td>Conductor</td>
          </tr>";
$ordenes = "";
while ($data = mysql_fetch_array($result)){
      $tabla.="<tr>
                   <td><input type=\"checkbox\" id=\"$data[id_orden]\" checked onclick=\"cargarCheck(this);\"></td>
                   <td>".htmlentities($data['nombre'])."</td>
                   <td>".htmlentities($data['nombrecliente'])."</td>
                   <td>$data[hcitacion]</td>
                   <td>$data[hsalida]</td>
                   <td>$data[interno]</td>
                   <td>".htmlentities($data['apenom'])."</td>
               </tr>";
      $ordenes.="'$data[id_orden]',";
}
$ordenes.="'-1'";
$tabla.="</table>
         <script language=\"JavaScript\">
                 var ordenes = new Array($ordenes);
         </script>";
@mysql_close($conn);

print $tabla;
?>

