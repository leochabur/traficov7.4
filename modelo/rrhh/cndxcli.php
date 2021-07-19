<?php
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
  
  define(STRUCTURED, $_SESSION['structure']);
  
  function in_range($start_date, $end_date, $evaluame) {
    $start_ts = strtotime($start_date);
    $end_ts = strtotime($end_date);
    $user_ts = strtotime($evaluame);
    return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
  }

  $accion = $_POST['accion'];
  if ($accion == 'sbana'){ //codigo para generar sabana francos
     $conn = conexcion();
     
     $sql="SELECT id_cliente, id_estructuracliente, id_empleado FROM conductoresxcliente order by id_empleado";
     $result = mysql_query($sql, $conn);
     $restricciones = array();
     $row = mysql_fetch_array($result);
     while ($row){
           $emple = $row[id_empleado];
           $clienxcond=array();
           while (($row) && ($emple == $row[id_empleado])){
                 $clienxcond[]= $row[id_cliente];
                 $ult = $row[id_empleado];
                 $row = mysql_fetch_array($result);
           }
           $restricciones[$ult] = $clienxcond;
     }
     
     $sql = "SELECT id, upper(razon_social) FROM clientes c WHERE id in (SELECT id_cliente FROM clienterestriccion) order by razon_social";
     
     $result = mysql_query($sql, $conn);

     $tabla ='<table id="tablitasssss" align="center" width="100%" >
                     <thead>
                            <tr align="center" valign="middle">
                                <th rowspan="2" WIDTH="150px"></th>';
     $clientes = array();
     $i=0;
     while ($row = mysql_fetch_array($result)){
           $tabla.="<th>$row[1]</th>";
           $clientes[$i++] = array($row[0], $row[1]);
     }
     $tabla.='</tr>
                       </thead>
                       <tbody>';
     
     

     $sql="SELECT upper(concat(apellido,', ', nombre)) as apenom, upper(razon_social) as empleador, e.id_empleado
           FROM empleados e
           inner join empleadores emp on emp.id = e.id_empleador
           where e.activo and id_cargo = 1 and not borrado
           order by apenom";
      $result = mysql_query($sql, $conn);
      while ($row = mysql_fetch_array($result)){
            $tabla.="<tr><td>$row[0]</td>";
            for ($i = 0; $i < count($clientes); $i++){
                $chek="";
                if (in_array($clientes[$i][0], $restricciones[$row[id_empleado]])){
                   $chek = "checked";
                }
                $tabla.="<td align='center'><input type='checkbox' $chek onClick='cambiarRestriccion($row[id_empleado],".$clientes[$i][0].", this.checked);'></td>";
            }
            $tabla.="</tr>";
      }
              $tabla.="</tbody>
                       </table>
                       <style type='text/css'>
                       .sab{
                                 background-color: #909090;
                       }
                       .dom{
                                 background-color: #FFC0FF;
                       }
table

{

	width: 400px;

}

td.click, th.click

{

	background-color: #bbb;

}

td.hover, tr.hover

{

	background-color: #69f;

}

th.hover, tfoot td.hover

{

	background-color: ivory;

}

td.hovercell, th.hovercell

{

	background-color: #abc;

}

td.hoverrow, th.hoverrow

{

	background-color: #6df;

}
table {
	font-family:arial;
	background-color: #CDCDCD;

	font-size: 8pt;
	text-align: left;
}
table thead tr th, table.tablesorter tfoot tr th {
	background-color: #e6EEEE;
	border: 1px solid #FFF;
	font-size: 8pt;
	padding: 4px;
}
table thead tr .header {
	background-image: url(bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}


                       </style>
                        <script type='text/javascript'>
                                $('#tablitasssss').chromatable({height: '500px', width:'100%', scrolling: 'yes'});
                                $('#tablitasssss').tableHover();
                                function cambiarRestriccion(emp, cli, st){
                                         var state = 0;
                                         if (st){
                                            state = 1;
                                         }
                                         $.post('/modelo/rrhh/cndxcli.php', {accion:'update', id_e: emp, id_c: cli, estado: state});
                                }";
              $tabla.="</script>";
              mysql_free_result($result);
              mysql_close($conn);
              print $tabla;
  }
  elseif($accion == 'update'){
     $st = $_POST['estado'];
     $conductor = $_POST['id_e'];
     $cliente = $_POST['id_c'];
     if ($st){
        $campos = 'id, id_cliente, id_estructuracliente, id_empleado';
        $values = "$cliente, $_SESSION[structure], $conductor";
        print (insert('conductoresxcliente', $campos, $values));
     }
     else{
          $conn = conexcion();
          $sql = "DELETE FROM conductoresxcliente where (id_cliente = $cliente) and (id_estructuracliente = $_SESSION[structure]) and (id_empleado = $conductor)";
          mysql_query($sql, $conn);
          mysql_close($conn);
     }
  }
?>

