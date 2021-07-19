<?
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
          $conn = conexcion();
          $sql = "SELECT e.id_empleado, c.id as id_ciudad, nrodoc, upper(c.ciudad) as nom_ciudad, e.id_empleado, upper(domicilio) as direccion, e.telefono, legajo, upper(apellido) as apellido, upper(nombre) as nombre, nrodoc, em.id as id_empleador, upper(razon_social) as empleador, if(e.activo, 'checked', '') as activo,
                         date_format(fechanac, '%d/%m/%Y') as fnac, date_format(inicio_relacion_laboral, '%d/%m/%Y') as inrelab, cuil
                  FROM empleados e
                  left join ciudades c on (c.id = e.id_ciudad) and (c.id_estructura = e.id_estructura_ciudad)
                  left join empleadores em on (em.id = e.id_empleador)
                  where (id_cargo = 1) and (e.id_estructura = $_SESSION[structure]) and (e.id_empleado = $_POST[conductor])";

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
                                                armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "(id_estructura = ".STRUCTURED.")",1).
                                       '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Legajo</td>
                                    <td><input id="legajo" readonly name="legajo" size="8" value="'.$data['legajo'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
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
                                    <td><input id="direccion" name="direccion" value="'.htmlentities($data['direccion']).'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td><input id="telefono" name="telefono" value="'.$data['telefono'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
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
                                    <td WIDTH="20%">CUIL</td>
                                    <td><input id="cuil" name="cuil" value="'.$data['cuil'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
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
                                                       $("#empleador, #city").selectmenu({width: 250});
                                                        $("#finlab, #fnac").datepicker({dateFormat:"dd/mm/yy"});
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
  elseif($accion == 'upcond'){
          $sql = "SELECT procesado FROM empleados where (id_empleado = $_POST[id_empleado])";
          $result = ejecutarSQL($sql);
          $userdef="";
          $userid="";
          if ($row = mysql_fetch_array($result)){
             if ($row['procesado'] == 0){
                $userdef = ", usuario_alta_definitiva, fecha_alta_definitiva, procesado";
                $userid = ", $_SESSION[userid], now(), 1";
             }
          }
          $activo = $_POST['activo'] ? 1 : 0;
          $fnac = dateToMysql($_POST['fnac'], "/");
          $firl = dateToMysql($_POST['finlab'], "/");
          
          $campos = "id_empleador, id_estructura_empleador, apellido, nombre, nrodoc, domicilio, telefono, id_ciudad, id_estructura_ciudad, fechanac, inicio_relacion_laboral, cuil, activo $userdef";
          $values = "$_POST[empleador], $_SESSION[structure], '$_POST[apellido]', '$_POST[nombre]', '$_POST[dni]', '$_POST[domicilio]', '$_POST[telefono]', $_POST[city], $_SESSION[structure], '$fnac', '$firl', '$_POST[cuil]', $activo $userid";

          print update("empleados", $campos, $values, "(id_empleado = $_POST[id_empleado])and(id_estructura = $_SESSION[structure])");

  }
?>

