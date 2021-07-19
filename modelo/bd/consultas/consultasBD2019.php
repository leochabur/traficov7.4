<?php
  function getSQLEmpleadores($str){
           if ($str){
              $str ="where exe.id_estructura = $str";
           }
           else{
                $str="";
           }
           
           $sql = "SELECT e.id, upper(razon_social) as razon_social
                   FROM (SELECT * FROM empleadores WHERE activo) e
                   inner join empleadoresporestructura exe on exe.id_empleador = e.id
                   $str
                   order by razon_social";
           return $sql;
  }
  
  function getSQLConductores($fecha, $estructura){
             $sql = "SELECT *
                   FROM
                   ((SELECT e.id_empleado, upper(cn.nov_text) as nov, if(em.id = 1,upper(concat(e.apellido, ', ',e.nombre)), upper(concat('(',em.razon_social,') ', e.apellido, ', ',e.nombre))) as emple, cn.id as id_nov
                   from novedades n
                   inner join empleados e on e.id_empleado = n.id_empleado
                   inner join cod_novedades cn on cn.id = n.id_novedad
                   inner join empleadores em on em.id = e.id_empleador
                   where ('$fecha' between desde and hasta) and (e.activo) and (not e.borrado) and (id_cargo = 1) and (n.activa) and (e.id_estructura = $estructura)
                   )
                   union
                   (select e.id_empleado, 'CONDUCTORES DISPONIBLES' as nov, if(em.id = 1,upper(concat(e.apellido, ', ',e.nombre)), upper(concat('(',em.razon_social,') ', e.apellido, ', ',e.nombre))) as emple, 0 as id_nov
                   from empleados e
                   inner join empleadores em on em.id = e.id_empleador
                   where (e.activo) and (not e.borrado) and (e.id_estructura = $estructura) and (id_cargo = 1) and (e.id_empleado not in (SELECT e.id_empleado
                                                                                                            from novedades nov
                                                                                                            inner join empleados e on e.id_empleado = nov.id_empleado
                                                                                                            where ('$fecha'  between desde and hasta) and (e.activo) and (not e.borrado) and (nov.activa) and (id_cargo = 1)
                                                                                                            group by e.id_empleado))
                   )) e
                   order by id_nov, emple";
$sql = "select e.id_empleado, if (cn.id is null, 'CONDUCTORES DISPONIBLES', upper(cn.nov_text)) as nov, if(em.id = 1,upper(concat(e.apellido, ', ',e.nombre)), upper(concat('(',em.razon_social,') ', e.apellido, ', ',e.nombre))) as emple, if (cn.id is null, 0, cn.id) as id_nov
from (select id_empleado, id_empleador, apellido, nombre from empleados where (activo) and (not borrado) and (id_cargo = 1) and (id_estructura = $estructura)) e
left join (select id_empleado, id_novedad from novedades where ('$fecha'  between desde and hasta) and (activa)) n on e.id_empleado = n.id_empleado
left join cod_novedades cn on cn.id = n.id_novedad
inner join empleadores em on em.id = e.id_empleador
order by id_nov, emple";
             return $sql;
  }
  
  function getSQLConductoresEstructura($estructura){
             $sql = "SELECT id_empleado, if(em.id = 1,upper(concat(e.apellido, ', ',e.nombre)), upper(concat('(',em.razon_social,') ', e.apellido, ', ',e.nombre))) as apenom
                    FROM empleados e
                    inner join empleadores em on em.id = e.id_empleador
                    WHERE (e.id_estructura = $estructura) and (id_cargo = 1) and (e.activo) and (not borrado)
                    ORDER BY apellido, nombre";
             return $sql;
  }

?>

