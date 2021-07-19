<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include ('../../../controlador/bdadmin.php');
  include ('../../../controlador/ejecutar_sql.php');
  include ('../../../vista/paneles/viewpanel.php');
  include_once ('../../utils/dateutils.php');
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
                  where (e.id_empleado = $_POST[conductor])";

          $result = mysql_query($sql, $conn);
          $data = mysql_fetch_array($result);

          $tabla='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Datos del conductor</legend>
                <form id="modunidad">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleador</td>
                                    <td>
                                        <select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all">'.
                                                '<option value="'.$data['id_empleador'].'">'.$data['empleador'].'</option>'.
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
                                    <td><input id="apellido" readonly name="apellido" value="'.htmlentities($data['apellido']).'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td><input id="nombre" readonly name="nombre" value="'.htmlentities($data['nombre']).'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">DNI</td>
                                    <td><input id="dni" name="dni" readonly value="'.$data['nrodoc'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Novedades</td>
                                    <td>
                                        <select id="novs" name="novs" class="ui-widget ui-widget-content  ui-corner-all">';
          $sql="SELECT n.id, upper(concat(cn.nov_text, '  |   (',date_format(desde, '%d/%m/%Y'),'  -  ', date_format(hasta, '%d/%m/%Y'),')'))
                      FROM novedades n
                      inner join cod_novedades cn on cn.id = n.id_novedad
                      inner join estructuras e on e.id = n.id_estructura
                      inner join usuarios u on u.id = n.usuario
                      inner join empleados em on em.id_empleado = n.id_empleado
                      where (em.id_empleado = $_POST[conductor]) and (n.activa)
                      order by desde desc";
          $result = mysql_query($sql, $conn);
          while ($data = mysql_fetch_array($result)){
                $tabla.='<option value="'.$data[0].'">'.$data[1].'</option>';
          }
                                                
          cerrarconexcion($conn);
          $tabla.='</select>
                                    </td>
                                    <td>
                                        <input type="button" id="loadnov" value="Cargar Novedad">
                                    </td>
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
                          $(document).ready(function(){';
                                               if (isset($_POST['novedad'])){
                                                  $tabla.="\$(\"#novs option[value=$_POST[novedad]]\").attr(\"selected\", \"selected\");
                                                           \$.post(\"/modelo/rrhh/nvdas/modnvda.php\", {accion: \"loadnov\", id_nov: \$(\"#novs\").val(), des:'$_POST[desde]', has:'$_POST[hasta]'}, function(data){\$(\"#nove\").html(data);});";
                                               }
                                               $tabla.='$("#novs").selectmenu({width: 400});
                                                       $("#loadnov").button().click(function(data){
                                                                                                   $.post("/modelo/rrhh/nvdas/modnvda.php", {accion: "loadnov", id_nov: $("#novs").val()}, function(data){$("#nove").html(data);});
                                                                                                   });
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
         print $tabla;
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
  elseif($accion == 'loadnov'){
          $sql = "SELECT n.id, date_format(desde, '%d/%m/%Y') as desde, date_format(hasta, '%d/%m/%Y') as hasta, cn.nov_text, cn.id
                      FROM novedades n
                      inner join cod_novedades cn on cn.id = n.id_novedad
                      where (n.id = $_POST[id_nov])";
          $res = ejecutarSQL($sql);
          if ($row = mysql_fetch_array($res)){
          $tabla='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                  <legend class="ui-widget ui-widget-header ui-corner-all">Novedad</legend>
                  <form id="modnov">
                        <input type="hidden" name="accion" id="accion" value="upnove">
                        <input type="hidden" name="idnove" id="idnove" value="'.$_POST['id_nov'].'">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Novedad</td>
                                    <td>
                                        <select id="codnov" name="codnov" class="ui-widget ui-widget-content  ui-corner-all">'.
                                                '<option value="'.$row[4].'">'.$row[3].'</option>'.
                                        $sql="SELECT id, upper(nov_text) FROM cod_novedades c where activa order by nov_text";
                                        $result = ejecutarSQL($sql);
                                        while ($data = mysql_fetch_array($result)){
                                              $tabla.='<option value="'.$data[0].'">'.$data[1].'</option>';
                                        }
                                       $tabla.='</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Desde</td>
                                    <td><input id="desde"  name="desde" size="20" class="required ui-widget ui-widget-content  ui-corner-all" value="'.$row[desde].'"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hasta</td>
                                    <td><input id="hasta"  name="hasta" size="20" class="required ui-widget ui-widget-content  ui-corner-all" value="'.$row[hasta].'"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Activa</td>
                                    <td colspan="2">Si<input type="radio" checked name="act" value="1">
                                    No<input type="radio" name="act" value="0"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"></td>
                                    <td></td>
                                    <td><input type="submit" id="svch" value="Guardar Cambios"></td>
                                </tr>';
                                if (isset($_POST['des'])){
                                   $tabla.="<tr>
                                                <td colspan='3'>
                                                    <input type='button' value='Volver' id='back'>
                                                </td>
                                            </tr>";
                                }
                         $tabla.='</table>
            </fieldset>

         </form>
         </fieldset>
                  <style type="text/css">
                #modnov .error{
	                    font-size:0.8em;
	                    color:#ff0000;
                 }
         </style>
         <script type="text/javascript">
                          $(document).ready(function(){';
                                                       if (isset($_POST['des'])){
                                                          $tabla.='$("#back").button().click(function(data){
                                                                                                           document.location.href="/vista/rrhh/nvdas/nvlist.php?ds='.$_POST['des'].'&hs='.$_POST['has'].'";
                                                                                                          }); ';
                                                       }

                                                       $tabla.='$("#codnov").selectmenu({width: 400});
                                                       $("#svch").button();
                                                       $("#loadnov").button().click(function(data){

                                                                                                   $.post("/modelo/rrhh/nvdas/modnvda.php", {accion: "loadnov", id_nov: $("#novs").val()}, function(data){$("#nove").html(data);});
                                                                                                   });

                                                        $("#desde, #hasta").datepicker({dateFormat:"dd/mm/yy"});

                                                       $("#modnov").validate({
                                                                              submitHandler: function(e){

                                                                                                         var datos = $("#modnov").serialize();
                                                                                                         $.post("/modelo/rrhh/nvdas/modnvda.php", datos, function(data){
                                                                                                                                                                         $("#nove").html("");
                                                                                                                                                                         $("#data").html("");
                                                                                                                                                                         document.location.href="/vista/rrhh/nvdas/nvlist.php?ds='.$_POST['des'].'&hs='.$_POST['has'].'";
                                                                                                                                                                        });
                                                                                                        }
                                                                              });
                          });
         </script>';

         print $tabla;

          }
  }
  elseif($accion == 'upnove'){
                 $desde = dateToMysql($_POST['desde'], "/");
                 $hasta = dateToMysql($_POST['hasta'], "/");
                 
                 $conn = conexcion();
                 $sql = "SELECT id_empleado FROM novedades where id = $_POST[idnove]";    //recupera el id del conductor
                 $result = mysql_query($sql, $conn);
                 $cond = 0;
                 if ($data = mysql_fetch_array($result)){
                    $cond = $data['id_empleado'];
                 }
                 
                 $sql = "select id as id_orden from ordenes where (fservicio between '$desde' and '$hasta') and (id_chofer_1 = $cond) and (id_estructura = $_SESSION[structure])";
                 $result = mysql_query($sql, $conn);
                 cerrarconexcion($conn);
                 $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
                 $value="null, null, $_SESSION[userid], now()";
                 
                 while ($row = mysql_fetch_array($result)){
                       backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                       update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                 }
                 
                 $conn = conexcion();
                 $sql = "select id as id_orden from ordenes where (fservicio between '$desde' and '$hasta') and (id_chofer_2 = $cond) and (id_estructura = $_SESSION[structure])";
                 $result = mysql_query($sql, $conn);
                 cerrarconexcion($conn);
                 $campo='id_chofer_2, id_estructura_chofer2, id_user, fecha_accion';
                 $value="null, null, $_SESSION[userid], now()";

                 while ($row = mysql_fetch_array($result)){
                       backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                       update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                 }


                 $campo = "desde, hasta, id_novedad, activa";
                 $value="'$desde', '$hasta', $_POST[codnov], $_POST[act]";
                 print update('novedades', $campo, $value, "(id = $_POST[idnove])");
  }
?>

