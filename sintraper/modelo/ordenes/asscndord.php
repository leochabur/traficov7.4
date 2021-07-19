<?
  session_start();
  include ('../../controlador/bdadmin.php');
   include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'ldcn'){
     $fecha = explode('/', $_POST['fecha']);
     $fecha = "$fecha[2]-$fecha[1]-$fecha[0]";
     $conn = conexcion();
     $sql="(SELECT e.id_empleado, upper(concat(apellido,', ',nombre)) as apellido, 'Disponible' as estado, 0 as novedad
            from empleados e
            where (id_estructura = $_SESSION[structure]) and (id_empleado not in (select id_empleado from novedades where ('$fecha' between desde and hasta) and (id_estructura = $_SESSION[structure])))
            Order BY e.apellido ASC
            )
            union
            (SELECT e.id_empleado, upper(concat(apellido,', ',nombre)) as apellido, cn.nov_text, cn.id as novedad
            FROM (select * from novedades where ('$fecha' between desde and hasta) and (id_estructura = $_SESSION[structure])) l
            inner join empleados e on (e.id_empleado = l.id_empleado)
            inner join cod_novedades cn on (cn.id = l.id_novedad)
            Order BY estado, apellido
            )
            order by novedad, apellido";
     //die($sql);
     $result = mysql_query($sql, $conn);
     $select = 'Conductores <select id="conductores" name="conductores"  class="ui-widget-content  ui-corner-all">';
     $data = mysql_fetch_array($result);
     while ($data){
           $nov = $data['novedad'];
           $select.="<optgroup label='$data[estado]'>";
           while (($data) && ($data['novedad'] == $nov)){
                 $select.="<option value='$data[id_empleado]'>$data[apellido]</option>";
                 $data = mysql_fetch_array($result);
           }
           $select.="</optgroup>";
     }
     $select.="</select>
               <input type='button' value='Cargar Ordenes' id='loorcn'>
               <script type='text/javascript'>
                       $(function(){
                                    var conduc;
	                                $('select').multiselect({
                                                             multiple: false,
                                                             header: 'Seleccione un conductor',
                                                             noneSelectedText: 'Seleccione un conductor',
                                                             selectedList: 1,
                                                             minWidth: 300
                                                             });
                                    $('#loorcn').button().click(function(){
                                                                           var fechao = $('#fecha').val();
                                                                           conduc = $('#conductores').val();
                                                                           $('#orasig').html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
                                                                            $.post('/modelo/ordenes/asscndord.php', {accion: 'ldcc', fecha: fechao, cond: conduc, condtxt: $('#conductores option:selected').text()}, function(data) {

                                                                                                                                                                   $('#orasig').html(data);
                                                                                                                                                                  });
                                                                           });
                                 });
               </script>
               <hr align='tr'>";
     print $select;
  }
  elseif($accion == 'ldcc'){
       $conn = conexcion();
       $fecha = explode('/', $_POST['fecha']);
       $fecha = "$fecha[2]-$fecha[1]-$fecha[0]";
       $sql = "SELECT o.id as id_orden, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social
                   FROM ordenes o
                   LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                   WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure]) and (not finalizada) and ((id_chofer_1 = $_POST[cond])or(id_chofer_2 = $_POST[cond]))";
       $result = mysql_query($sql, $conn);
       $asignadas = "<fieldset>
                    <legend>Ordenes asignadas a $_POST[condtxt]</legend>
                    <table id='ordasig' class='tablesorter' width='75%'>
                            <thead>
                            <tr>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicios</th>
                                <th>Cliente</th>
                                <th>Accion</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr id='$data[id_orden]'>
                              <td>$data[hcitacion]</td>
                              <td>$data[hsalida]</td>
                              <td>$data[nombre]</td>
                              <td>$data[razon_social]</td>
                              <td><input type='button' value='Quitar' onClick='quitar($data[id_orden]);'></td>
                          </tr>";
       }
       $asignadas.="</tbody>
                    </table>
                    </fieldset>
                    <br>";
                    
       $sql = "SELECT o.id as id_orden, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social
               FROM ordenes o
               LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
               WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure]) and (not finalizada) and (id_chofer_1 is null)";
              $result = mysql_query($sql, $conn);
       $asignadas.= "<fieldset>
                    <legend>Ordenes sin conductores asignados</legend>
                    <table id='ordsinasig' class='tablesorter' width='75%'>
                            <thead>
                            <tr>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicios</th>
                                <th>Cliente</th>
                                <th>Accion</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr id='$data[id_orden]'>
                              <td>$data[hcitacion]</td>
                              <td>$data[hsalida]</td>
                              <td>$data[nombre]</td>
                              <td>$data[razon_social]</td>
                              <td><input type='button' value='Agregar' onClick='agregarorden($data[id_orden]);'></td>
                          </tr>";
       }
       $asignadas.="</tbody>
                    </table>
                    </fieldset>
                    <script type='text/javascript'>

                            function agregarorden(id_orden){
                                                   var cond = $('#conductores').val();
                                                   $.post('/modelo/ordenes/asscndord.php', {accion: 'addordcond', ord: id_orden, con: cond}, function(data){
                                                                                                                                                                   $('#'+id_orden).remove();
                                                                                                                                                                   $('#ordasig tbody').append(data);

                                                                                                                                                            });
                            }

                            function quitar(id_orden){
                                                   $.post('/modelo/ordenes/asscndord.php', {accion: 'remordcond', ord: id_orden}, function(data){
                                                                                                                                                 $('#'+id_orden).remove();
                                                                                                                                                 $('#ordsinasig tbody').append(data);
                                                                                                                                                 });
                            }
                    </script>
                    ";
       mysql_free_result($result);
       mysql_close($conn);
       print $asignadas;
  }
  elseif($accion == 'addordcond'){
       $orden = $_POST['ord'];
       $condu = $_POST['con'];
       $ok = update('ordenes', 'id_chofer_1, id_estructura_chofer1', "$condu, $_SESSION[structure]", "(id = $orden) and (id_estructura = $_SESSION[structure])");
       if ($ok){
          $conn = conexcion();
          $sql = "SELECT o.id as id_orden, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social
                  FROM ordenes o
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  WHERE (o.id = $orden) and (o.id_estructura = $_SESSION[structure])";
          $result = mysql_query($sql, $conn);
          if ($data = mysql_fetch_array($result)){
             $ok ="<tr id='$data[id_orden]'>
                              <td>$data[hcitacion]</td>
                              <td>$data[hsalida]</td>
                              <td>$data[nombre]</td>
                              <td>$data[razon_social]</td>
                              <td><input type='button' value='Quitar' onClick='quitar($data[id_orden]);'></td>
                 </tr>";
             mysql_free_result($result);
          }
          mysql_close($conn);
       }
       print $ok;
  }
  elseif($accion == 'remordcond'){
       $orden = $_POST['ord'];
       $ok = update('ordenes', 'id_chofer_1, id_estructura_chofer1', "null, null", "(id = $orden) and (id_estructura = $_SESSION[structure])");
       if ($ok){
          $conn = conexcion();
          $sql = "SELECT o.id as id_orden, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social
                  FROM ordenes o
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  WHERE (o.id = $orden) and (o.id_estructura = $_SESSION[structure])";
          $result = mysql_query($sql, $conn);
          if ($data = mysql_fetch_array($result)){
             $ok ="<tr id='$data[id_orden]'>
                              <td>$data[hcitacion]</td>
                              <td>$data[hsalida]</td>
                              <td>$data[nombre]</td>
                              <td>$data[razon_social]</td>
                              <td><input type='button' value='Agregar' onClick='remove($data[id_orden]);'></td>
                 </tr>";
             mysql_free_result($result);
          }
          mysql_close($conn);
       }
       print $ok;
  }
  
?>

