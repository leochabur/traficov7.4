<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/bd/consultas/consultasBD.php');
  
  $conn = conexcion();

  $estructura = $_SESSION['structure'];
  $orden = $_POST['orden'];

           $conn = conexcion();
           if (isset($_POST['orden'])){

              $sql = "select fservicio, o.id_cliente, o.id_estructura_cliente, filtralista
                      from ordenes o
                      left join restgralconductorcliente rgc on rgc.id_cliente = o.id_cliente and rgc.id_estructura_cliente = o.id_estructura_cliente
                      where o.id = $orden";
              $result = mysql_query($sql, $conn);
              if ($data = mysql_fetch_array($result)){

                 if ($data[3]){  ///el cliente filtra la lista de conductores seleccionados

                    getOptionConductoresRestringido($data[0], $data[1], $data[2], $conn);
                    exit;
                 }
                 else
                     $sql = getSQLConductores($data[0], $estructura);
              }
              else
                  exit;
              
           }
           else{
                $fecha = dateToMysql($_POST['fecha'], "/");
                $sql = getSQLConductores($fecha, $estructura);
           }

           $result = mysql_query($sql, $conn);
           $option="<option value='0'></option>";
           $data = mysql_fetch_array($result);
           while ($data){
                 $novedad = $data['nov'];
                 $codnov = $data['id_nov'];
                 if (!$codnov){
                    $back="#D8FCF8";
                 }
                 else{
                      $back="#FFC0C0";
                 }
                 $option.="<optgroup label='$novedad'>";
                 while (($data) && ($data['id_nov'] == $codnov)){
                       $option.="<option style='background-color: $back' value=\"$data[id_empleado]\">".htmlentities($data['emple'])."</option>";
                       $data = mysql_fetch_array($result);
                 }
                 $option.="</optgroup>";
           }
           mysql_free_result($result);
           mysql_close($conn);
           $option.="<style type='text/css'>
                            select option{
                                                padding: 6px;
                                                }
                     </style>";
           print $option;
  //para levantar el cronograma asociado a la orden y obtener las restricciones de las licencias de conductores
  
  //consulta para recuperar las licencias q estan habilitadas a realizar e recorrido
 /* $q_lic_hab = "SELECT lxc.id_licencia as lic
                FROM (select * from ordenes where (id = $orden) and (id_estructura = $est)) o
                inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                inner join licenciasxcronograma lxc on (lxc.id_cronograma = s.id_cronograma) and (lxc.id_estructura_cronograma = s.id_estructura_cronograma)";

  $result_lic_hab = mysql_query($q_lic_hab, $conn);
  if (mysql_num_rows($result_lic_hab)){
     $data = mysql_fetch_array($result_lic_hab);
     $q_cond_hab = "SELECT e.id_empleado, concat(apellido ,', ',nombre) as empleado, if(datediff(max(vigencia_hasta), date(now())) >= 0, 0,1) as ok
                    FROM (SELECT id_empleado, id_estructura, apellido, nombre FROM empleados WHERE (activo) and (id_cargo = 1)) e
                    inner join licenciaconductor lc on (lc.id_conductor = e.id_empleado) and (lc.id_estructura_empleado = e.id_estructura) and (lc.id_licencia = $data[lic]) and (lc.id_estructura_licencia = $est)
                    group by lc.id_conductor, lc.id_licencia
                    order by ok, empleado";


  }
  else{
     $q_cond_hab = "SELECT id_empleado, upper(concat(apellido, ', ', nombre)) as empleado, 0 as ok
                    FROM empleados e
                    where (activo) and (id_cargo = 1) and (afectado_a_estructura = $est)
                    order by apellido";
  }

  $result = mysql_query($q_cond_hab, $conn) or die (mysql_error($conn));
  $data = mysql_fetch_array($result);
  $option="<option value='0'></option>";
  while ($data){
        $ok = $data['ok'];
        if (!$ok){
           $back="#D8FCF8";
           $option.= "<optgroup label='Conductores Habilitados'>";
        }
        else{
             $back="#FFC0C0";
            $option.= "<optgroup label='Conductores No Habilitados'>";
            }
        while ($data && ($ok == $data['ok'])){
              $option.="<option style='background-color: $back' class='$ok' value='$data[id_empleado]'>".htmlentities($data['empleado'])."</option>";
              $data = mysql_fetch_array($result);
        }
		$option.="</optgroup>";
  }
  mysql_close($conn);
  print $option;  */
  
function getOptionConductoresRestringido($fecha, $cliente, $str, $conn){
  $sql="select e.id_empleado, concat(apellido,', ', nombre) as apenom, 'si' as permitido, n.id, if (n.id is null, 'CONDUCTORES DISPONIBLES', upper(nov_text)) as nov_text, apellido, if(n.id is null, 1,0) as disponible, 'CONDUCTORES ADMITIDOS POR EL CLIENTE' as texto
        from empleados e
        inner join conductoresxcliente cxc on cxc.id_empleado = e.id_empleado
        left join (select * from novedades where '$fecha' between desde and hasta and activa) n on n.id_empleado = e.id_empleado and n.id_estructura = e.id_estructura
        left join cod_novedades cn on cn.id = n.id_novedad
        where cxc.id_cliente = $cliente and id_estructuracliente = $str and permitido and activo
        union all
        select e.id_empleado, concat(apellido,', ', nombre) as apenom, 'no' as permitido, n.id, if (n.id is null, 'CONDUCTORES DISPONIBLES', upper(nov_text)) as nov_text, apellido, if(n.id is null, 1,0) as disponible, 'CONDUCTORES NO ADMITIDOS POR EL CLIENTE' as texto
        from empleados e
        left join (select * from novedades where '$fecha' between desde and hasta and activa) n on n.id_empleado = e.id_empleado and n.id_estructura = e.id_estructura
        left join cod_novedades cn on cn.id = n.id_novedad
        where e.id_empleado not in (SELECT id_empleado FROM conductoresxcliente where id_cliente = $cliente and id_estructuracliente = $str and not permitido) and id_cargo = 1 and activo
        order by permitido DESC, disponible DESC, nov_text, apellido";
  //die($sql);
  $result = mysql_query($sql, $conn);
  $row = mysql_fetch_array($result);
  $option="";
  while ($row){
        $permitido = $row[2];
        $option.="<optgroup label='$row[texto]'>";
        if ($permitido == 'si'){
                 $back="#D8FCF8";
        }
        else{
                   $back="#FFC0C0";
        }
        while (($row) && ($permitido == $row[2])){
              $disponible = $row[6];
              $option.="<optgroup label='$row[nov_text]'>";

              while (($row) && ($permitido == $row[2]) && ($disponible == $row[6])){
                    $option.="<option style='background-color: $back' value='$row[0]'>$row[1]</option>";
                    $row = mysql_fetch_array($result);
              }
              $option.="</optgroup>";
        }
        $option.="</optgroup>";
  }
  print $option;

  }
  
?>
