<?
  //define(RAIZ, '/nuevotrafico');
  //include ('/nuevotrafico/controlador/bdadmin.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
  function armarSelect ($tabla, $orden, $key, $valor, $estructura, $return = 0){

           $conn = conexcion(true);

           if ($estructura)
              $cond = "WHERE $estructura";
           $sql = "SELECT $key, UPPER($valor) FROM $tabla $cond ORDER BY $orden";
           $result = mysql_query($sql, $conn);

           $option="";
           while ($data = mysql_fetch_row($result)){
                 $option.="<option value=\"$data[0]\">".htmlentities($data[1])."</option>";
           }

           mysql_free_result($result);
           mysql_close($conn);
           if ($return)          //parche muy chancho deberia hacer return siempre
              return $option;
           else
               print $option;
  }
  
  function armarSelectCond ($estructura){
           $conn = conexcion();

           $sql = "SELECT id_empleado, concat(apellido,', ', nombre) as apenom FROM empleados WHERE (afectado_a_estructura = $estructura) and (id_cargo = 1) and (activo) ORDER BY apellido, nombre";
           $result = mysql_query($sql, $conn);

           $option="";
           while ($data = mysql_fetch_array($result)){
                 $option.="<option value=\"$data[id_empleado]\">".htmlentities($data['apenom'])."</option>";
           }
           mysql_free_result($result);
           mysql_close($conn);
           print $option;
  }
  
  function armarSelectCondNov ($estructura, $fecha){
           $fecha = dateToMysql($fecha, '/');
           $conn = conexcion();
           $sql = "SELECT *
                   FROM
                   ((SELECT e.id_empleado, upper(cn.nov_text) as nov, upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')')) as emple, cn.id as id_nov
                   from novedades n
                   inner join empleados e on e.id_empleado = n.id_empleado
                   inner join cod_novedades cn on cn.id = n.id_novedad
                   inner join empleadores em on em.id = e.id_empleador
                   where ('$fecha' between desde and hasta) and (e.activo) and (n.id_estructura = $estructura) and (id_cargo = 1)
                   )
                   union
                   (select e.id_empleado, 'DISPONIBLE' as nov, upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')')) as emple, 0 as id_nov
                   from empleados e
                   inner join empleadores em on em.id = e.id_empleador
                   where (e.activo) and (e.id_estructura = 1) and (id_cargo = 1) and (e.id_empleado not in (SELECT e.id_empleado
                                                                                                            from novedades nov
                                                                                                            inner join empleados e on e.id_empleado = nov.id_empleado
                                                                                                            where ('$fecha' between desde and hasta) and (e.activo) and (nov.id_estructura = $estructura) and (id_cargo = 1)
                                                                                                            group by e.id_empleado))
                   )) e
                   order by id_nov, emple";

           $result = mysql_query($sql, $conn);
           $option="";
           $data = mysql_fetch_array($result);
           while ($data){
                 $novedad = $data['nov'];
                 $codnov = $data['id_nov'];
                 $option.="<optgroup label='$novedad'>";
                 while (($data) && ($data['id_nov'] == $codnov)){
                       $option.="<option value=\"$data[id_empleado]\">".htmlentities($data['emple'])."</option>";
                       $data = mysql_fetch_array($result);
                 }
                 $option.="</optgroup>";
           }
           mysql_free_result($result);
           mysql_close($conn);
           print $option;
  }
?>

