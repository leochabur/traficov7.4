<?php @session_start();


   if (!$_SESSION['auth']){
      session_destroy();
      print('<b><p align="center">Su sesion ha expirado!</p></b><meta http-equiv="Refresh" content="2;url=/toyota">');
      exit;
   }


                                                    /*     $.notty({
                                                                title : "Notificaciones RRHH",
                                                                content : "Ver novedades"
                                                                });
                                                       $.notty({
                                                                title : "Notificaciones Seg Vial",
                                                                content : "Ver novedades"
                                                                });*/
  
  function encabezado($titulo){
           print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                  <html>
                  <head>
                        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
                        <title>'.$titulo.'</title>
                        <link type="text/css" href="../vista/css/menu.css" rel="stylesheet" />
                        <link type="text/css" href="../vista/css/blitzer/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
                        <link rel="stylesheet" type="text/css" href="./css/jquery.notty.css" />
                        <script type="text/javascript" src="../vista/js/jquery-1.7.2.min.js"></script>
                        <script type="text/javascript" src="../vista/js/jquery-ui-1.8.22.custom.min.js"></script>
                        <script type="text/javascript" src="../vista/js/menu.js"></script>
                        <script type="text/javascript" src="../vista/js/jquery.notty.js"></script>
                          <link type="text/css" href="../vista/css/blue/style.css" rel="stylesheet"/>

                  </head>
                  <script type="text/javascript">
                          var fechaActual = new Date();
                          var dia = fechaActual.getDate();7.
                          var mes = fechaActual.getMonth() +1;
                          var anno = fechaActual.getFullYear();
                          if (dia <10) dia = "0" + dia;
                          if (mes <10) mes = "0" + mes;
                          var fechaHoy = dia + "/" + mes + "/" + anno;

                          $(document).ready(function(){
                                                       $("#orders").dialog({autoOpen: false, width: 650, modal: true});
                                                       muestraReloj();
                                                       $("body div:last").remove();

                                                       });

                          function muestraReloj(){
                                   if (!document.layers && !document.all && !document.getElementById) return;
                                   var fechacompleta = new Date();
                                   var horas = fechacompleta.getHours();
                                   var minutos = fechacompleta.getMinutes();
                                   var segundos = fechacompleta.getSeconds();
                                   if (minutos <= 9)
                                      minutos = "0" + minutos;
                                   if (segundos <= 9)
                                      segundos = "0" + segundos;
                                   horario = horas + ":" + minutos+":"+segundos;
                                   $("#reloj").html(fechaHoy+"  -  "+horario);
                                   setTimeout("muestraReloj()", 1000);
                          }

                  </script>
                  <style type="text/css">
                         body { background:#ededed; }
                         body { font-size: 82.5%; }
                  </style>
                  <div style="visibility: hidden"> <a href="http://apycom.com/">Apycom jQuery Menus</a></div>
                  <div id="orders"></div>';
  }
  
  function menu(){
      
      $menu = '<br><span class="ui-widget">Operacion Campana</span>
               <img src="./masterbus-logo.png" border="0" align="right">
               <br>
               <span class="ui-widget"><div id="reloj"></div></span>
               <hr align="tr">
               <div id="menu">
                         <ul class="menu">
                             <li>
                                 <a href="/toyota/" class="parent"><span>Salir</span></a>
                             </li>
                         </ul>
                         </div>';

        print $menu;
  }
?>


