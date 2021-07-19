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
                     $sql = "INSERT INTO cicloinforme (id_tiposervicio, id_estructuratiposervicio, ingresodesde, salidahasta, id_cliente, id_estructura_cliente)
                             VALUES ($_POST[tipo], $_SESSION[structure], '$_POST[desde]', '$_POST[hasta]', $_POST[cli], $_SESSION[structure])
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
  elseif($accion == 'cnfsend'){
                 $fecha = dateToMysql($_POST['fecha'], '/');
                 $sql = "SELECT upper(tipo) as tipo, date_format(date_add(concat('$fecha',' ',salidahasta), interval 1 day), '%d/%m/%Y - %H:%i') as hasta,
                                date_format(concat('$fecha', ' ',ingresodesde), '%d/%m/%Y - %H:%i') as desde
                         FROM cicloinforme c
                         left join tiposervicio ts on ts.id = c.id_tiposervicio and ts.id_estructura = c.id_estructuratiposervicio
                         WHERE c.id_cliente = 10";
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
  elseif($accion == 'ciclo'){
                         $tabla='<table border="1" align="center" width="40%" name="tabla" class="tablesorter">
                                <tr>
                                    <td WIDTH="20%">Tipo de Servicio</td>
                                    <td>LLegada a Planta desde... / Salida de Planta hasta... </td>
                                    <td align=""> </td>
                                </tr>';
                          $conn = conexcion();
                                
                          $sql = "SELECT id as id_ciclo, id_tiposervicio as id_tipo, date_format(ingresodesde,'%H:%i') as desde, date_format(salidahasta,'%H:%i') as hasta
                                  from cicloinforme
                                  where id_cliente = $_POST[cli] AND id_estructura_cliente = $_SESSION[structure]";
                          $ciclos = ejecutarSql($sql, $conn);
                          $informes = array();
                          while ($c = mysql_fetch_array($ciclos)){
                            $informes[$c['id_tipo']] = $c;                          
                          }
                         
                                  $sql = "SELECT if(ci.id, ci.id, 0) as id_ciclo, t.id as tipo_servicio, tipo, date_format(if(c.id is null, null, ingresodesde),'%H:%i') as desde,
                                                    date_format(if (c.id is null, null, salidahasta),'%H:%i') as hasta
                                             FROM tiposervicio t
                                             left join cicloinforme ci on id_tiposervicio = t.id and  id_estructuratiposervicio = t.id_estructura
                                             left join (select * from clientes where id = $_POST[cli] AND id_estructura = $_SESSION[structure]) c ON c.id = id_cliente AND id_estructura_cliente = c.id_estructura
                                             where t.id_estructura = $_SESSION[structure] 
                                             ORDER BY tipo";
                                  $sql = "SELECT id, tipo
                                          FROM tiposervicio 
                                          WHERE id_estructura = $_SESSION[structure]
                                          ORDER BY tipo";
                                     $result = ejecutarSql($sql, $conn);
                                     while ($row = mysql_fetch_array($result)){
                                            $desde = $hasta = '';
                                            $id_ciclo = "";
                                            if (array_key_exists($row['id'], $informes)){
                                                $desde = $informes[$row['id']]['desde'];
                                                $hasta = $informes[$row['id']]['hasta'];
                                                $id_ciclo = "<input type='submit' value='Eliminar' class='boton'>
                                                             <input type='hidden' name='accion' value='del'/>
                                                             <input type='hidden' name='tipo' value='".$informes[$row['id']]['id_ciclo']."'/>";
                                            }
                                            $tabla.= "<tr>
                                                      <td>$row[tipo]</td>
                                                      <td>
                                                      <form action='/modelo/ordenes/sendinf.php' class='addciclo'>
                                                        <input type='text' size='6' title='Llegada a planta desde...' name='desde' value='".$desde."' class='hora'>
                                                        <input type='text' size='6' title='Salida de planta hasta...' name='hasta' value='".$hasta."' class='hora'>
                                                        <input type='submit' value='Guardar/Modificar' class='boton'>
                                                        <input type='hidden' name='accion' value='add'/>
                                                        <input type='hidden' name='cli' value='$_POST[cli]'/>
                                                        <input type='hidden' name='tipo' value='".$row['id']."'/>
                                                      </form>
                                                      </td>
                                                      <td>
                                                      <form action='/modelo/ordenes/sendinf.php' class='addciclo'>
                                                        $id_ciclo
                                                      </form>
                                                      </td>
                                                 </tr>";
                                     }                                     
                         $tabla.="</table>
                                  <script>
                                      $.mask.definitions['~']='[012]';
                                      $.mask.definitions['%']='[012345]';
                                      $('.hora').mask('~9:%9',{completed:function(){}});
                                      $('.boton').button();
                                      $('.addciclo').submit(function(event){
                                                                        event.preventDefault();
                                                                        var f = $(this);
                                                                        $.post(f.attr('action'), 
                                                                        f.serialize(), 
                                                                        function(data){
                                                                                        var response = $.parseJSON(data);
                                                                                        if (response.estado){
                                                                                              $('#clientes').trigger('change');
                                                                                        }
                                                                                        else
                                                                                              alert(response.mje);
                                                                                        });
                                                                        });
                                  </script>";
                         print $tabla;
  }
?>
