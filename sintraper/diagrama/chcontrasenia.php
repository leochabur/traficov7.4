<?php
/*------------------------------------------------------------------------------------------------------- */
/*                              M O D U L O  -  CONSULTA CONDUCTORES                                      */
/*------------------------------------------------------------------------------------------------------- */
          session_start();

        if (!(isset($_SESSION['auth']) && ($_SESSION['auth'] == true)))
           echo "<script>window.location='index.php'</script>";
        else
            include('data.php');
            
        function verificarCampos(){
                 if (md5($_SESSION['passwd']) == md5($_POST['passwd'])){
                    if ((md5($_POST['npasswd']) == md5($_POST['rnpasswd'])) and ($_POST['npasswd'] != '')){
                       return true;
                    }else{
                          alerta('Las contraseñas no coinciden');
                          return false;
                          }
                }else{
                      alerta('La Contrase&ntilde;a actual es incorrecta');
                      return false;
                }
        }


?>

<link href="httpdocs/estilo.css" rel="stylesheet" type="text/css">
<link href="httpdocs/estilo2.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo6 {color: #000000}
-->
</style>
<script type="text/javascript" src="md5-min.js"></script>

<script>

function valida_envia(){
	if (document.form1.passwd.value.length==0){
		alert("Tiene que escribir su contraseña actual")
		document.form1.password.focus()
		return 0;
	}
	else{
         var valida="<? echo md5($_SESSION['passwd']) ?>";
         var validar= hex_md5(document.form1.passwd.value);
         if (valida != validar){
            alert("La contraseña actual es incorrecta");
            document.form1.passwd.value="";
            document.form1.password.focus();
            return 0;
         }
         else{
              if (document.form1.npasswd.value.length == 0){
                 alert("La contraseña no puede permanecer en blanco");
                 document.form1.npasswd.value="";
                 document.form1.npasswd.focus();
                 return 0;
              }
              else{
                   if (document.form1.rnpasswd.value.length == 0){
                      alert("La contraseña no puede permanecer en blanco");
                      document.form1.rnpasswd.value="";
                      document.form1.rnpasswd.focus();
                      return 0;
                   }
                   else{
                        var valida= hex_md5(document.form1.npasswd.value);
                        var validar= hex_md5(document.form1.rnpasswd.value);
                        if (validar != valida){
                           alert("Contraseña inválida ( No coincide con su confirmación)");
                           document.form1.npasswd.value="";
                           document.form1.rnpasswd.value="";
                           document.form1.npasswd.focus();
                           return 0;
                        }
                        else{
                             document.form1.submit();
                        }
                   }
              }

         }

	}
}
</script>

<body bgcolor="#ffffff" text="#000000" link="#0000ff" vlink="#800080" alink="#ff0000">
<p>
<?php if (!isset($_GET['ac']))encabezado($_SESSION["chofer"],$_SESSION["nivel"]);

$db1 = conectar_mysql();


if (isset($_POST['passwd'])){
   if ($_POST['passwd'] != ''){
       $query = mysql_query("SELECT * FROM passwords WHERE id_chofer = $_SESSION[id_chofer]") or die(mysql_error());
       if(mysql_num_rows($query) != 0){
            $data = mysql_fetch_array($query);
            if (verificarCampos()){
               mysql_query("UPDATE passwords SET clave= '$_POST[npasswd]' WHERE (id_chofer = '$_SESSION[id_chofer]')") or die(mysql_error());
               //$_SESSION['auth'] = false;
               session_destroy();
               echo '<script>window.location.href="index.php"</script>';
            }
       }else{
             if (verificarCampos()){
               mysql_query("INSERT INTO passwords (id_chofer, legajo, clave) values ('$_SESSION[id_chofer]', '$_SESSION[legajo]','$_POST[npasswd]')") or die(mysql_error());
               //$_SESSION['auth'] = false;
               session_destroy();
               echo '<script>window.location.href="index.php"</script>';
            }
       }
   }else{
         alerta('La contraseña no puede estar en blanco');
       }
}




?>
<br>
<div align="left"><p class="tgrande"><span class="celeste"> &gt; </span><?echo $_SESSION["chofer"]?></p></div>
<div align="center"> <p class="tmediano"><span class="celeste"> &gt; </span>Cambio de contraseña</p> </div>
<form name="form1" method="post" action="chcontrasenia.php">
  <label></label>
  <div>
    <div align="center">
      <table width="80%" border="0" bordercolor="#FFFFFF" bgcolor="#CCCCCC">
         <tr>
          <td><div align="right">Contrase&ntilde;a actual</div></td>
          <td><div align="left">
            <input type="password" name="passwd">
          </div></td>
        </tr>
         <tr>
          <td><div align="right">Nueva Contrase&ntilde;a</div></td>
          <td><div align="left">
            <input type="password" name="npasswd">
          </div></td>
        </tr>
         <tr>
          <td><div align="right">Repetir Contrase&ntilde;a</div></td>
          <td><div align="left">
            <input type="password" name="rnpasswd">
          </div></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="button" name="update" value="Cambiar" onClick="valida_envia()">
        </tr>
      </table>
    </div>
  </div>
  <p>&nbsp;</p>
</form>
<p>
<?
if(isset($db1)){
	mysql_close($db1) or die(alerta(mysql_error()));
}
echo'<hr id="hr">';
if (!isset($_GET['ac'])) piepagina($_SESSION["nivel"]);
?>
</body>
</html>

