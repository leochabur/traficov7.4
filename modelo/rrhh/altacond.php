<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  include_once ('../utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'sveevt'){ //codigo para guardar un empleado de forma eventual
     $conn = conexcion();
     $sql = "SELECT * FROM empleados WHERE (legajo = $_POST[legajo]) and (id_estructura = $_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     cerrarconexcion($conn);
     if (mysql_fetch_array($result)){
        $ok = "0"; //codigo de error para indicar que ya existe el numero de legajo que se esta intentando asignar
     }
     else{
          $ok = insert('empleados', 'id_empleado, usuario_alta_provisoria, legajo, apellido, nombre, id_cargo, id_estructura, procesado, fecha_alta', "$_SESSION[userid], $_POST[legajo], '$_POST[apellido]', '$_POST[nombre]', 1, $_SESSION[structure], 0, now()"); //agrega un conductor pendiente de procesamiento por parte del sector de RRHH
     }
     print $ok;
  }
   elseif($accion == 'load'){
   		try{
          $conn = conexcion();
          $sql = "SELECT upper(es.nombre) as nomestr, es.id as idestr, e.id_empleado, c.id as id_ciudad, nrodoc, upper(c.ciudad) as nom_ciudad, e.id_empleado, upper(domicilio) as direccion, e.telefono, legajo, upper(apellido) as apellido, upper(e.nombre) as nombre, nrodoc, em.id as id_empleador, upper(razon_social) as empleador, if(e.activo, 'checked', '') as activo,
                         date_format(fechanac, '%d/%m/%Y') as fnac, date_format(inicio_relacion_laboral, '%d/%m/%Y') as inrelab, cuil, upper(ca.descripcion) as cargo, ca.id as idcargo,
                         date_format(fecha_fin_relacion_laboral, '%d/%m/%Y') as finrelab, email
                  FROM empleados e
                  left join ciudades c on (c.id = e.id_ciudad) and (c.id_estructura = e.id_estructura_ciudad)
                  left join cargo ca on ca.id = e.id_cargo
                  left join empleadores em on (em.id = e.id_empleador)
                  inner join estructuras es on es.id = e.id_estructura
                  where (e.id_empleado = $_POST[conductor])";

          $result = mysql_query($sql, $conn);
          $data = mysql_fetch_array($result);
          cerrarconexcion($conn);
          print '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Datos del conductor</legend>
                <form id="modunidad">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleador</td>
                                    <td>
                                        <select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all">'.
                                                '<option value="'.$data['id_empleador'].'">'.$data['empleador'].'</option>'.
                                                armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED,1).
                                       '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                            <td WIDTH="20%"><font color="#FF0000"><b>Afectado a estructura...</b></font></td>
                                            <td><select id="struct" name="struct" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">'.
                                            '<option value="'.$data['idestr'].'">'.$data['nomestr'].'</option>'.
                                            armarSelect('estructuras', 'nombre', 'id', 'nombre', "",1).'
                                     </select>
                                              </td>
                                              <td>
                                              </td>
                                              </tr>
                                <tr>
                                    <td WIDTH="20%">Legajo</td>
                                    <td><input id="legajo" readonly name="legajo" size="8" value="'.$data['legajo'].'" class="ui-widget ui-widget-content  ui-corner-all"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Apellido</td>
                                    <td><input id="apellido" name="apellido" value="'.htmlentities($data['apellido']).'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td><input id="nombre" name="nombre" value="'.htmlentities($data['nombre']).'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">DNI</td>
                                    <td><input id="dni" name="dni" value="'.$data['nrodoc'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Direccion</td>
                                    <td><input size="50" id="direccion" name="direccion" value="'.htmlentities($data['direccion']).'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td><input id="telefono" name="telefono" value="'.$data['telefono'].'" class="ui-widget ui-widget-content  ui-corner-all"/></td>
                                    <td></td>
                                </tr>
                                <td WIDTH="20%">Ciudad</td>
                                    <td><select id="city" name="city" class="ui-widget ui-widget-content  ui-corner-all" >'.
                                    '<option value="'.$data['id_ciudad'].'">'.$data['nom_ciudad'].'</option>'.
                                    armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])", 1).
                              '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <td WIDTH="20%">Cargo</td>
                                    <td><select id="cargo" name="cargo" class="ui-widget ui-widget-content  ui-corner-all" >'.
                                    '<option value="'.$data['idcargo'].'">'.$data['cargo'].'</option>'.
                                    armarSelect('cargo', 'descripcion', 'id', 'descripcion', "(id_estructura = $_SESSION[structure])", 1).
                              '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Nacimiento</td>
                                    <td><input id="fnac" name="fnac" value="'.$data['fnac'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Inicio Relacion Laboral</td>
                                    <td><input id="finlab" name="finlab" value="'.$data['inrelab'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Fin Relacion Laboral</td>
                                    <td><input id="ffinlab" name="ffinlab" value="'.$data['finrelab'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">CUIL</td>
                                    <td><input id="cuil" name="cuil" value="'.$data['cuil'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">E-mail</td>
                                    <td><input id="mail" name="mail" value="'.$data['email'].'" class="ui-widget ui-widget-content  ui-corner-all" size="50" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Activo</label></td>
                                    <td><input name="activo" '.$data['activo'].' type="checkbox" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Datos"/> </td>
                                </tr>
                         </table>

            <input type="hidden" name="accion" value="upcond">
            <input type="hidden" name="id_empleado" value="'.$data['id_empleado'].'">
            </fieldset>
         </form>
         </fieldset>
         <style type="text/css">
                #modunidad .error{
	                    font-size:0.8em;
	                    color:#ff0000;
                 }
         </style>
         <script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#save").button();
                                                       $("#empleador, #city, #struct").selectmenu({width: 250});
                                                       $("#cargo").selectmenu({width: 350});
                                                        $("#finlab, #fnac, #ffinlab").datepicker({dateFormat:"dd/mm/yy"});
                                                       $("#cuil").mask("99-99999999-9");
                                                       $("#dni").mask("99.999.999");
                                                       $("#modunidad").validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#modunidad").serialize();
                                                                                                                 $.post("/modelo/rrhh/altacond.php", datos, function(data){
                                                                                                                                                                             if (data == 1){
                                                                                                                                                                                      var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                                 "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                                 "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                                 "<strong>Se ha modificado con exito el conductor en la Base de Datos!</strong></p>"+
                                                                                                                                                                                                 "</div>"+
                                                                                                                                                                                                 "<div>";
                                                                                                                                                                                       $("#data").html(mje);

                                                                                                                                                                             }
                                                                                                                                                                             });
                                                                                                        }
                                                                              });
                          });
         </script>';
     }
     catch (Exception $e) { print $e->getMessage(); }
  }
  elseif($accion == 'upcond'){
          $sql = "SELECT procesado FROM empleados where (id_empleado = $_POST[id_empleado])";
          $result = ejecutarSQL($sql);
          $userdef="";
          $userid="";
          if ($row = mysql_fetch_array($result))
          {
             if ($row['procesado'] == 0){
                $userdef = ", usuario_alta_definitiva, fecha_alta_definitiva, procesado";
                $userid = ", $_SESSION[userid], now(), 1";
             }
          }
          $cargo = $_POST['cargo'];
          $valuecargo="";
          $campocargo="";
          if($cargo){
                     $valuecargo = ",$cargo, $_SESSION[structure]";
                     $campocargo = ",id_cargo, id_estructura_cargo";
          }
          
          $activo = $_POST['activo'] ? 1 : 0;

          $fnac = dateToMysql($_POST['fnac'], "/");
          $firl = dateToMysql($_POST['finlab'], "/");
          $ffirl = dateToMysql($_POST['ffinlab'], "/");
          
          $campos = "email, afectado_a_estructura, id_estructura, id_empleador, id_estructura_empleador, apellido, nombre, nrodoc, domicilio, telefono, id_ciudad, id_estructura_ciudad, fechanac, inicio_relacion_laboral, fecha_fin_relacion_laboral, cuil, activo $userdef $campocargo";
          $values = "'$_POST[mail]', $_POST[struct], $_POST[struct], $_POST[empleador], $_SESSION[structure], '$_POST[apellido]', '$_POST[nombre]', '$_POST[dni]', '$_POST[direccion]', '$_POST[telefono]', $_POST[city], $_SESSION[structure], '$fnac', '$firl', '$ffirl', '$_POST[cuil]', $activo $userid $valuecargo";

          $status = update("empleados", $campos, $values, "(id_empleado = $_POST[id_empleado])");

          if (!$activo)
          {
            try{
                comunicatePush(' - FROM UPDATE EMPLEADOS');
              }
              catch(Exception $e){}
          }

          print $status;

  }

	function comunicatePush($from)
	{
            $conn = conexcion(true);
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://iotdevices.masterbus.net/api/login",
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>array(
                                                    'user' => 'Leo',
                                                    'pass' => 'leoMB'
                                                ),
              CURLOPT_RETURNTRANSFER => 1, 
              CURLOPT_HTTPHEADER => array(
                                          'content-type' => 'application/json'
                                          ),
            ));
            $response = curl_exec($curl);    
            curl_close($curl);

            $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$response - LOGIN PUSH', now())";            
            mysqli_query($conn, $insert);

            $body = json_decode($response, true);


            $url = "http://iotdevices.masterbus.net/api/fcm/push";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
               "access-token: $body[token]",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $data = '{"msg":
                            {"data":
                                    {"backend":"faceid",
                                    "desc":"SistemaFaceID",
                                    "action":"sync"
                                    }
                            }
                    }';


            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            curl_close($curl);

            $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$resp - $from', now())";
            
            mysqli_query($conn, $insert);
            mysqli_close($conn);
    }

?>

