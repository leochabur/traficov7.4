<?php
     session_start();
     include('../../controlador/bdadmin.php');

     $con=conexcion(true);

     $sql = "SELECT apenom, e.id as id_estructura, e.nombre as estructura, u.activo, u.id as id_user, permisos, moduloAfectado, u.nivel
                  FROM usuarios u
                  INNER JOIN usuariosxestructuras uxe ON (uxe.id_usuario = u.id)
                  INNER JOIN estructuras e ON (e.id = uxe.id_estructura)
                  WHERE (u.user = '$_POST[usr]') and (md5(u.password) = '".md5($_POST['pwd'])."') and (uxe.activo)";

     $query = mysql_query($sql, $con) or die("Error al intentar acceder al sistema. ERRNO:0001A");//"Error al recuperar los datos de acceso!");

     if(mysql_num_rows($query) != 0) {
           $data = mysql_fetch_array($query);

           if ($data['activo']){
              $sql="SELECT u.apenom, cpu.id_estructura, upper(c.razon_social) as str, u.id as id_user, 0 as permisos, 0 as moduloAfectado, c.id as id_cliente
                    FROM clientesporusuarios cpu
                    inner join clientes c on c.id = cpu.id_cliente
                    inner join usuarios u on u.id = cpu.id_usuario
                    WHERE (u.user = '$_POST[usr]')";
              $result = mysql_query($sql, $con);
              $mje="<table border='0'>
                           <tr>
                               <td><u>Estructura</u></td>
                           </tr>
                           <tr><td><hr></td></tr>";
              if(mysql_num_rows($result) > 0){
                   $row = mysql_fetch_array($result);
                   while ($row){
                         $_SESSION['lv'] = $row['nivel'];
                         $mje.="<tr>
                                    <td><a href='../../toyota/vista/index.php?v=$row[id_estructura]&s=$row[str]&t=$row[id_user]&n=$row[apenom]&pm=$row[permisos]&ma=$row[id_cliente]'><b>$row[str]</b></a></td>
                                </tr>";
                         $row = mysql_fetch_array($result);
                   }
              }
              else{
                   while ($data){
                         $_SESSION['lv'] = $data['nivel'];
                         $mje.="<tr>
                                    <td><a href='../../vista/index.php?v=$data[id_estructura]&s=$data[estructura]&t=$data[id_user]&n=$data[apenom]&pm=$data[permisos]&ma=$data[moduloAfectado]'><b>$data[estructura]</b></a></td>
                                </tr>";
                         $data = mysql_fetch_array($query);
                   }
              }
              $mje.="</table>";
           }
	       else{
                $mje="Usuario no autorizado";
           }
     }
     else{
          $mje="<b>Usuario o contrase".htmlentities("ñ")."a invalido!</b><br>
                   Click <a href='' id='rescue'><font color='#FF0000'>aqui</font></a> para restaurar su contrase".htmlentities("ñ")."a.<br>
                   Se enviara un mail a su cuenta con la nueva clave.
                   <script type='text/javascript'>
                           $('#rescue').click(function(event) {
                                                              event.preventDefault();
                                                              $.post('/modelo/acceso/recupasswd.php', {usr:'$_POST[usr]'}).done(function(data){
                                                                                                                                                $( '#struct' ).dialog('close');
                                                                                                                                              });
                                                         });
                   </script>";
     }
     
     if(isset($con)){
	                 mysql_close($con);// or die(alerta(mysql_error()));
     }
     print $mje;

?>

