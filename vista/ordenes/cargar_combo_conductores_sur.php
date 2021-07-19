<?php
     set_time_limit(0);
     error_reporting(0);
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
                     $sql = getSQLConductoresSUR($data[0], $estructura);
              }
              else
                  exit;
              
           }
           else{
                $fecha = dateToMysql($_POST['fecha'], "/");
                $sql = getSQLConductoresSUR($fecha, $estructura);
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


  function getSQLConductoresSUR($fecha, $estructura){

      $sql = "select e.id_empleado, if (cn.id is null, 'CONDUCTORES DISPONIBLES', upper(cn.nov_text)) as nov, if(em.id = 1,upper(concat(e.apellido, ', ',e.nombre)), upper(concat('(',em.razon_social,') ', e.apellido, ', ',e.nombre))) as emple, if (cn.id is null, 0, cn.id) as id_nov
      from (select id_empleado, id_empleador, apellido, nombre from empleados where (activo) and (not borrado) and (id_estructura = $estructura)) e
      left join (select id_empleado, id_novedad from novedades where ('$fecha'  between desde and hasta) and (activa)) n on e.id_empleado = n.id_empleado
      left join cod_novedades cn on cn.id = n.id_novedad
      inner join empleadores em on em.id = e.id_empleador
      order by id_nov, emple";
       return $sql;
  }
  
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
        where e.id_empleado not in (SELECT id_empleado FROM conductoresxcliente where id_cliente = $cliente and id_estructuracliente = $str and permitido) and activo
        order by permitido DESC, disponible DESC, nov_text, apellido";
  //die($sql);
  $result = mysql_query($sql, $conn);
  $row = mysql_fetch_array($result);
  $option="";
  $option="<option value='0'></option>";
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
