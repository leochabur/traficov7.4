<? session_start();

   include_once('../controlador/ejecutar_sql.php');


   if (!$_SESSION['auth']){
      session_destroy();
      header("Location: /diagrama");
      exit;
   }
    define(RAIZ, '..');


                                                    /*     $.notty({
                                                                title : "Notificaciones RRHH",
                                                                content : "Ver novedades"
                                                                });
                                                       $.notty({
                                                                title : "Notificaciones Seg Vial",
                                                                content : "Ver novedades"
                                                                });        */
  function getNovTraf(){
           $sql = "SELECT upper(texto) as link, n.file
                   FROM normativa n
                   ORDER BY texto";
           $trafico = "";
           $result = ejecutarSQL($sql);
           if (mysql_num_rows($result) > 0){
              $trafico = '$.notty({
                                   title : " '.htmlentities('Política de Master bus').'",
                                   content : "';
              while($row = mysql_fetch_array($result)){
                         $trafico.="<br><a href='/vista/iso/$row[file]'><font color='#FF0000'>$row[link]</font></a>";
              }
              $trafico.='"});';
              mysql_free_result($result);
           }
           return $trafico;
  }
  
  function getMjes(){
        $query = "SELECT upper(mensaje) as mje
		          FROM mensajes
		          WHERE (id_empleado = $_SESSION[id_chofer]) and (vigencia_desde <= date(now())) and (not visto)";
        $mje='';
        $result = ejecutarSQL($query);
        if (mysql_num_rows($result)){
        	$mje = "<fieldset>
		            <legend>Mensajes pendientes</legend>";
            while ($data = mysql_fetch_array($result)){
                	$mje.="<div>$data[0]</div>";
            }
		    $mje.="</fieldset>";
        }
        print $mje;
  }
  
  function encabezado($titulo){
           print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                  <html>
                  <head>
                        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
                        <title>'.htmlentities($titulo).'</title>
                        <link type="text/css" href="'.RAIZ.'/vista/css/menu.css" rel="stylesheet" />
                        <link type="text/css" href="'.RAIZ.'/vista/css/blitzer/jquery-ui-1.8.22.custom.css" rel="stylesheet" />
                        <link rel="stylesheet" type="text/css" href="'.RAIZ.'/vista/css/jquery.notty.css" />
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/jquery-1.7.2.min.js"></script>
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/jquery-ui-1.8.22.custom.min.js"></script>
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/menu.js"></script>
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/jquery.notty.js"></script>
                          <link type="text/css" href="'.RAIZ.'/vista/css/blue/style.css" rel="stylesheet"/>

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

                                                      '.getNovTraf().'muestraReloj();
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
                  <div id="orders" title="Ordenes sin conductores asignados"></div>';
  }
  
  function menu(){
      $menu = '<br><span class="ui-widget"><b>'.htmlentities($_SESSION['chofer']).'</b></span>
               <img src="'.RAIZ.'/masterbus-logo.png" border="0" align="right">
               <br>
               <span class="ui-widget"><div id="reloj"></div></span>
               <hr align="tr">
               <div id="menu">
                         <ul class="menu">
                             <li>
                                 <a href="ppal.php" class="parent"><span>Inicio</span></a>
                             </li>
                             <li>
                                 <a href="#" class="parent"><span>Mi informacion</span></a>
                             </li>
                             <li>
                                 <a href="dgmwrk.php"><span>Ver diagrama</span></a>
                             </li>
                             <li>
                             <a href="anomalias.php"><span>Anomalias</span></a>
                             </li>
                             <li>
                                 <a href="#"><span>Cambiar Contrase&ntilde;a</span></a>
                             </li>
                             <li>
                                 <a href="/diagrama/logout.php" class="parent"><span>Salir</span></a>
                             </li>
                         </ul>
               </div>';
        print $menu;
  }
?>
<?php

?>

