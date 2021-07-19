<?php
session_start();
include('../controlador/bdadmin.php');
$conexion = conexcion();
$query = mysql_query("SELECT id_empleado, Concat(Upper(apellido),', ', Upper(nombre)) as name 
                      FROM empleados 
                      WHERE (legajo = '$_POST[txtNombre]') and (activo = 1) and (id_empleador = 1)") or die(alerta(mysql_error()));

if(mysql_num_rows($query) != 0)  //verifica si esta ingresando con legajo, de ser asi se asume como empleado de Master Bus 
{ //Verifica que el chofer no se encuentre deshabilitado

	$data = mysql_fetch_array($query);
    mysql_free_result($query);
    $_SESSION['id_chofer'] = $data["id_empleado"];
    $_SESSION['chofer'] = $data["name"];
	$query = mysql_query("SELECT * FROM passwords WHERE id_chofer = $data[id_empleado]");

	if($data = mysql_fetch_array($query)){  //Verifica que si el chofer esta habilitado, se encuentre generada su clave de acceso caso contrario redirige a la pagina de cambio de contraseña
          //die(print_r($data));
          if (md5($data['clave']) == md5($_POST['txtPassword'])){ //si esta generada la clave de acceso verifica que coincida con la introducida en el formulario de index.php
	           $_SESSION['auth'] = true;
                   $_SESSION['id_chofer'] = $data["id_chofer"];
                   $_SESSION['legajo'] = $data["legajo"];
                   $_SESSION['passwd'] = $_POST['txtPassword'];
                   $_SESSION['super'] = $data["super"];
                   $_SESSION['str'] = $data["estructura"];
                   $_SESSION['fleteros'] = $data["vistafletero"];
                   $_SESSION['admin']= mysql_num_rows(mysql_query("SELECT * FROM admin_choferes WHERE id_chofer= $_SESSION[id_chofer]"));
                   $query = mysql_query("UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]") or die(mysql_error());
                  // print "<script>window.location=ppal.php</script>";
                  header ("Location: ppal.php?pv=s");
          }
          else{
               //echo 'ok';
               echo "<script>window.location='index.php?e=1'</script>"; //Contraseña incorrecta
          }
    }else{

          if (md5($_POST['txtNombre']) == md5($_POST['txtPassword'])){    //La clave no ha sido generada, verifica que el usuario y clave originales coincida y redirecciona a cambiar la contraseña
             $_SESSION['auth'] = true;
             $_SESSION['legajo'] = $data["legajo"];
             $_SESSION['passwd'] = $_POST['txtPassword'];
             $_SESSION['super'] = $data['super'];
             echo "<script>window.location='chcontrasenia.php?ac=1'</script>";
         }
         else {

	          echo "<script>window.location='index.php?e=2'</script>";
           }
    }
}
else
{
      //Huno algun error con las credenciales, verifica si es un fletero
    $sql = "SELECT id_empleado, Concat(Upper(apellido),', ', Upper(nombre)) as name, upper(razon_social) as emple, REPLACE(nrodoc,'.','') as dni
            FROM empleados e
            INNER JOIN empleadores emp ON emp.id = e.id_empleador
            WHERE (nrodoc <> '') AND (REPLACE(nrodoc,'.','') = '$_POST[txtNombre]') and (e.activo = 1) and (emp.activo = 1) AND (emp.id not in (1))";
    $query = mysql_query($sql) or die(mysql_error($conexion));
    if($data = mysql_fetch_array($query)) 
    { //Verifica que el chofer no se encuentre deshabilitado
      $_SESSION['id_chofer'] = $data["id_empleado"];
      $_SESSION['chofer'] = $data["name"];
      $_SESSION['empleador'] = $data["emple"];
      if (md5($data['clave']) == md5($_POST['dni']))
      {  
             
                       $_SESSION['auth'] = true;
                       $_SESSION['id_chofer'] = $data["id_empleado"];
                       $_SESSION['legajo'] = $data["legajo"];
                       $_SESSION['passwd'] = $_POST['txtPassword'];
                       $_SESSION['super'] = $data["super"];
                       $_SESSION['str'] = $data["estructura"];                   
                       $_SESSION['fleteros'] = 1;//$data["vistafletero"];
                       $_SESSION['admin'] = 0;
                       $query = mysql_query("UPDATE passwords set ultimoacceso = NOW() WHERE id_chofer = $_SESSION[id_chofer]");
                       mysql_close($conexion);
                       header ("Location: ../externos/ppal.php?pv=s");
      }
      else
      {
          echo "<script>window.location='index.php?e=0'</script>"; //El chofer esta dado de baja o es inexistente
      }
    }
    else
    {

      echo "<script>window.location='index.php?e=0'</script>"; //El chofer esta dado de baja o es inexistente
    }
  
}
if(isset($conexion)){
	mysql_close($conexion) or die(alerta(mysql_error()));
}

?>
<?php

?>
