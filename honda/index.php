<?php session_start();


  include ('../controlador/bdadmin.php');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html> <head> <title>Login | Adminisnistracion Master Bus</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="Copyright (c) 2012 MailChimp. All Rights Reserved.">
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-itunes-app" content="app-id=366794783">
<link rel="stylesheet" type="text/css" href="/screen.css">
<link rel="stylesheet" type="text/css" href="/logins.css">
<link type="text/css" href="./css/blitzer/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
<script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="./js/jquery-ui-1.8.22.custom.min.js"></script>
<script type="text/javascript" src="./js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="./js/validate-form/jquery.metadata.js"></script>

</head>
 <body id="login" class="new_teal">
 <?php
        $conn = conexcion();
      $sql="SELECT valor FROM opciones WHERE opcion = 'site_cliente_activo'";
      $result = mysql_query($sql, $conn);
      $activo = 0;
      if ($data = mysql_fetch_array($result))
         $activo = $data[0];
 ?>
 <div id="login-box">
      <div id="outer-content">
           <div id="inner-content">
                <div id="content">
                     <div id="login-form-wrap">
                          <div>
                               <form id="login-form" name="login-form" method="POST" action="./login.php">
                                     <img src="./masterbus-logo.png" border="0">
                                     <?php if ($activo) {   ?>
                                     <input type="hidden" name="referrer" class="av-hidden" value="" id="referrer">
                                     <input type="hidden" name="from" class="av-hidden" value="" id="from">
                                     <input type="hidden" name="auth_token" class="av-hidden" value="" id="auth_token">
                                     <input type="hidden" name="auth_system" class="av-hidden" value="" id="auth_system">
                                     <fieldset>
                                               <legend>Secure Login</legend>
                                               <div class="login-field above-below15 above30 clear">
                                                    <label for="username" class="placeholder ">Usuario</label>
                                                    <input type="text" name="usr" tabindex="1" class="required av-text" value="" id="username">
                                               </div>
                                               <div class="login-field above-below15">
                                                    <label for="password" class="placeholder ">Password</label>
                                                    <input type="password" name="pwd" tabindex="2" class="required av-password" value="" id="password">
                                               </div>
                                               <input type="submit" id="ingresar" value="Ingresar" class="button float-left" tabindex="3">
                                     </fieldset>
                                                                    <?php
                                     }
                                     else{?>
                                                <p class="av-text"><h2>Sitio en mantenimineto</h2></p>
                                      <?php
                                     }

                                     ?>
                               </form>
                          </div>
                          <br class="clear">
                     </div>
                     <br class="clear">
                </div>
           </div>
      </div>
 </div>
 <div id="struct" title="Estructuras">
      <div id="contentstr">
      </div>
 </div>
 </body>
 </html>
