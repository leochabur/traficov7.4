<?
  session_start();
  include ('../../controlador/bdadmin.php');
  
  $conn = conexcion();

  $estructura = $_SESSION['structure'];
  $orden = $_POST['orden'];

           $conn = conexcion();
           $sql = "SELECT *
                   FROM
                   ((SELECT e.id_empleado, upper(cn.nov_text) as nov, upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')')) as emple, cn.id as id_nov
                   from novedades n
                   inner join empleados e on e.id_empleado = n.id_empleado
                   inner join cod_novedades cn on cn.id = n.id_novedad
                   inner join empleadores em on em.id = e.id_empleador
                   where ((select fservicio from ordenes where id = $orden) between desde and hasta) and (e.activo) and (n.id_estructura = $estructura) and (id_cargo = 1)
                   )
                   union
                   (select e.id_empleado, 'CONDUCTORES DISPONIBLES' as nov, upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')')) as emple, 0 as id_nov
                   from empleados e
                   inner join empleadores em on em.id = e.id_empleador
                   where (e.activo) and (e.id_estructura = $estructura) and (id_cargo = 1) and (e.id_empleado not in (SELECT e.id_empleado
                                                                                                            from novedades nov
                                                                                                            inner join empleados e on e.id_empleado = nov.id_empleado
                                                                                                            where ((select fservicio from ordenes where id = $orden)  between desde and hasta) and (e.activo) and (nov.id_estructura = $estructura) and (id_cargo = 1)
                                                                                                            group by e.id_empleado))
                   )) e
                   order by id_nov, emple";

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
  
?>
