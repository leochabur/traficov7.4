<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include('./dateutils.php');


  $desde = dateToMysql($_POST['desde'], '/');
  $hasta = dateToMysql($_POST['hasta'], '/');
  $t_serv = $_POST['ts'];
  $turno =  $_POST['tu'];
  $txt_tur = $_POST['txtt'];
  $ef_cf = $_POST['c_e'];   //flag para saber si el grafco que hay que generar es de confiabilidad o eficiencia

  if ($ef_cf == 'efc'){
     $url = "./graph-generate.php?desde=$desde&hasta=$hasta&ts=$t_serv&tu=$turno&i_v=i&title=$txt_tur ENTRADA";
     $url_v = "./graph-generate.php?desde=$desde&hasta=$hasta&ts=$t_serv&tu=$turno&i_v=v&title=$txt_tur SALIDA";
     $html= "<script>
                  $('#dats').html('<p align=\"center\"><h2>Graficos de Eficiencia</h2></p><hr>');
                  $('#dats').append(\"<div align='center'><img src='$url' border='0' id='d$desde"."h$hasta"."t$turno"."s$t_serv"."i'></div>\");
                  $('#dats').append('<br>');
                  $('#dats').append(\"<div align='center'><img src='$url_v' border='0' id='d$desde"."h$hasta"."t$turno"."s$t_serv"."v'></div>\");
                  $('img').click(function(){
                                            var par = $(this).attr('id');
                                            var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                            dialog.dialog({
                                                           close: function(event, ui) {dialog.remove();},
                                                           title: 'Fuentes de grafico',
                                                           width:1050,
                                                           height:350,
                                                           modal:true,
                                                           show: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 },
                                                           hide: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 }
                                                           });
                                                           dialog.load('./fontdraw.php',{params:par},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                                            });


           </script>";
  }
  elseif ($ef_cf == 'cnf'){
     $url = "./graph-generate-cnf.php?desde=$desde&hasta=$hasta&ts=$t_serv&tu=$turno&i_v=i&title=$txt_tur ENTRADA";
     $url_v = "./graph-generate-cnf.php?desde=$desde&hasta=$hasta&ts=$t_serv&tu=$turno&i_v=v&title=$txt_tur SALIDA";
     $html = "<script>
                  $('#dats').html('<p align=\"center\"><h2>Graficos de Confiabilidad</h2></p><hr>');
                  $('#dats').append(\"<div align='center'><img src='$url' border='0' id='d$desde"."h$hasta"."t$turno"."s$t_serv"."i'></div>\");
                  $('#dats').append('<br>');
                  $('#dats').append(\"<div align='center'><img src='$url_v' border='0' id='d$desde"."h$hasta"."t$turno"."s$t_serv"."v'></div>\");
                  $('img').click(function(){
                                            var par = $(this).attr('id');
                                            var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                            dialog.dialog({
                                                           close: function(event, ui) {dialog.remove();},
                                                           title: 'Fuentes de grafico',
                                                           width:1050,
                                                           height:350,
                                                           modal:true,
                                                           show: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 },
                                                           hide: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 }
                                                           });
                                                           dialog.load('./fontdraw.php',{params:par},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                                            });
                  </script>";
  }
  print $html;
  
?>

