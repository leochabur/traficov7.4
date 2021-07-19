<?php
  set_time_limit(0);
  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
    include ('../../modelo/utils/dateutils.php');

  $accion = $_POST['accion'];

  
  if ($accion == 'add'){ ///codigo para guardar ////
                 $resp = array();
                 try{
                     $sql = "INSERT INTO cicloinforme (id_tiposervicio, id_estructuratiposervicio, ingresodesde, salidahasta)
                             VALUES ($_POST[tipo], $_SESSION[structure], '$_POST[desde]', '$_POST[hasta]')
                             ON DUPLICATE KEY UPDATE ingresodesde = '$_POST[desde]', salidahasta = '$_POST[hasta]'";
                     ejecutarSql($sql);
                     $resp[estado]=true;
                     $resp[det]=false;
                     $resp[mje]='Se ha agregado/modificado el tipo de servicio al informe!!!';
                     print json_encode($resp);
                 }catch (Exception $e) {
                                              $resp[estado]=false;
                                              $resp[mje]='NO se ha podido agregar el tipo de servicio al informe. Recagre la pagina por favor!!';
                                              $resp[sql]=$e->getMessage();
                                              print json_encode($resp);
                                       };
  }
  elseif($accion == 'del'){
                 $resp = array();
                 try{
                     $sql = "delete from cicloinforme where id = $_POST[tipo]";
                     ejecutarSql($sql);
                     $resp[estado]=true;
                     $resp[det]=true;
                     $resp[mje]='Se ha eliminado el tipo de servicio del informe!!!';
                     print json_encode($resp);
                 }catch (Exception $e) {
                                              $resp[estado]=false;
                                              $resp[mje]='NO se ha podido elimnar el tipo de servicio del informe. Recagre la pagina por favor!!';
                                              $resp[sql]=$e->getMessage();
                                              print json_encode($resp);
                                       };
  }
  elseif($accion == 'adddes'){
                 $resp = array();
                 try{
                     $nombre = str_replace(",",";",$_POST[nombre]);
                     $id = insert('correos_informe', 'id, correo, nombre',"'$_POST[email]', '$nombre'");
                     $resp[estado]=true;
                     $resp[nombre]=$_POST[nombre];
                     $resp[mail]=$_POST[email];
                     $resp[id]=$id;
                     print json_encode($resp);
                 }catch (Exception $e) {
                                              $resp[estado]=false;
                                              $resp[mje]='No se ha podido guardar el destinatario!';
                                              $resp[sql]=$e->getMessage();
                                              print json_encode($resp);
                                       };
                 
                 
  }
  elseif($accion == 'deldes'){

                 $resp = array();
                 try{
                     $id = explode('-', $_POST[dest]);
                     $id = delete('correos_informe', 'id',"$id[1]");
                     $resp[estado]=true;
                     $resp[nombre]=$_POST[nombre];
                     print json_encode($resp);
                 }catch (Exception $e) {
                                              $resp[estado]=false;
                                              $resp[mje]='No se ha podido eliminar el destinatario!';
                                              $resp[sql]=$e->getMessage();
                                              print json_encode($resp);
                                       };
  }
  elseif($accion = 'cnfsend'){
                 $fecha = dateToMysql($_POST['fecha'], '/');
                 $sql = "SELECT upper(tipo) as tipo, date_format(date_add(concat('$fecha',' ',salidahasta), interval 1 day), '%d/%m/%Y - %H:%i') as hasta,
                                date_format(concat('$fecha', ' ',ingresodesde), '%d/%m/%Y - %H:%i') as desde
                         FROM cicloinforme c
                         left join tiposervicio ts on ts.id = c.id_tiposervicio and ts.id_estructura = c.id_estructuratiposervicio";
                 $result = ejecutarSql($sql);
                 $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                             <legend class="ui-widget ui-widget-header ui-corner-all">Rrsumen Informe</legend>
		                             <div id="mensaje"></div>
                                     <table border="1" align="center" width="50%" name="tabla" class="tablesorter">
                                                                     <tr>
                                    <td WIDTH="20%">Tipo de Servicio</td>
                                    <td>Llegada a Planta desde...</td>
                                    <td>Salida de Planta hasta...</td>
                                </tr>';
                 while ($row = mysql_fetch_array($result)){
                       $tabla.="<tr>
                                    <td>$row[0]</td>
                                    <td>$row[desde]</td>
                                    <td>$row[hasta]</td>
                                    </tr>";
                 }
                 $tabla.="<tr><td colspan='3'><input type='button' value='Generar y enviar' id='sendinf'></td></tr>
                          </table>
                 </fieldset>
                 <script>
                         $('#sendinf').button().click(function(){
                                                                 $('#sendinf').hide();
                                                                 $.post('/modelo/enviomail/reporteexcel.php', {fecha:$('#fecha').val()}, function(data){\$('#sendinf').show();});
                                                                 });


                 </script>";
                 print $tabla;
  }
?>
