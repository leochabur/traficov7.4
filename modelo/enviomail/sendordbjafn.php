<?php
session_start();

include_once("../../controlador/bdadmin.php");
include_once('../../modelo/enviomail/sendmail.php');

function sentMail($orden){
   $ok = 0;
   if (isset($_SESSION['senmail'])){
      if ($_SESSION['senmail'] == 1){
         $ok = 1;
      }
   }
   if ($ok){
   $con = conexcion();
   $sql = "SELECT mail FROM usuarios where id = $_SESSION[userid]";
   $result = mysql_query($sql, $con);
   $dirs = "";
   if ($data = mysql_fetch_array($result)){
      $dirs = $data['mail'];
   }
   $dirs.=",leochabur@gmail.com, rdattoli@masterbus.net, mdepeon@masterbus.net";
  // $dirs.=",leochabur@gmail.com";
   $sql = "select date_format(fservicio, '%d/%m/%Y'), nombre, hcitacion, hsalida, razon_social, upper(apenom), date_format(fecha_accion, '%d/%m/%Y - %H:%i:%s')
           from ordenes o
           LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
           left join usuarios u on u.id = o.id_user
           where o.id = $orden";
        
   $result = mysql_query($sql, $con);
   $cuerpo = "$sql";
   if ($data = mysql_fetch_array($result)){
           $cuerpo = "<table border=1>
                     <tr>
                         <td>Fecha Servicio</td>
                         <td>Nombre Servicio</td>
                         <td>Cliente</td>
                         <td>H. Citacion</td>
                         <td>H. Salida</td>
                         <td>Usuario</td>
                         <td>Fecha - Hora modificacion</td>
                     </tr>
                     <tr>
                         <td>$data[0]</td>
                         <td>$data[1]</td>
                         <td>$data[4]</td>
                         <td>$data[2]</td>
                         <td>$data[3]</td>
                         <td>$data[5]</td>
                         <td>$data[6]</td>
                     </tr>
                     </table>";
   }
   cerrarconexcion($con);
   enviarMail($dirs, $cuerpo, "Ordenes Desactivadas");
   }
}


?>
