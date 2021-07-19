<?
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
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $dias	= (strtotime($desde)-strtotime($hasta))/86400;
	 $dias 	= abs($dias);
     $dias = floor($dias);

     $nomday = array(1=>"Lu",2=>"Ma",3=>"Mi",4=>"Ju",5=>"Vi",6=>"Sa",7=>"Do");

     $conn = conexcion();
     $dia1=date(d, strtotime($desde));
     $anio1=date(Y, strtotime($desde));
     $mes1=date(m, strtotime($desde));
     $optcoches="";
     $sqlcoches = "SELECT id, interno
                            FROM unidades
                            where (activo) and (id_estructura = $_SESSION[structure])
                            order by interno";
     $result = mysql_query($sqlcoches);
     while ($row = mysql_fetch_array($result))
           $optcoches.="<option value='$row[id]'>$row[interno]</option>";
           
           
     $sql="SELECT n.id_empleado, cn.id, desde
                    FROM novedades n
                    inner join empleados e on (n.id_empleado = e.id_empleado)
                    inner join cod_novedades cn on (cn.id = n.id_novedad)
                    where (desde between '$desde' and '$hasta') and (hasta between '$desde' and '$hasta') and
                          (e.id_estructura = $_SESSION[structure]) and (cn.id = 15)
                    order by id_empleado, desde";

     $result = mysql_query($sql, $conn);
     
     $results = array();
     
     $data = mysql_fetch_array($result);
     
     while($data){
           $emple = $data['id_empleado'];
           $noves = array();
           while (($data) && ($emple == $data['id_empleado'])){
                 $noves[] = "'$data[desde]'";
                 $data = mysql_fetch_array($result);
           }
           $results[$emple] = $noves;
     }
              
              $fechsabana = mktime( 0, 0, 0, $fecha[0], 1, $fecha[1]);
              //$dias = date("t", $fechsabana);
              setlocale(LC_TIME, 'spanish');
              // die($dias);
              $tabla ='<table id="tablitasssss" align="center" width="100%" >
                              <thead>
                                     <tr align="center" valign="middle">
                                         <th rowspan="2" WIDTH="150px">Conductor</th>
                                         <th rowspan="2" WIDTH="50px">Interno</th>
                                         <th colspan="'.($dias+1).'">'.strftime("%B", $fechsabana).'  '.$fecha[1].'</th>
                                     </tr>
                                     <tr align="center" valign="middle">';
              $fecha1=strtotime($desde);
              for ($i = 0; $i <= $dias; $i++){
                  $nro=date(d, $fecha1);
                  $tabla.="<th>".$nomday[date(N, $fecha1)]."<br>".$nro."</th>";
                  $fecha1=$fecha1 +86400;
              }
              $tabla.='</tr>
                       </thead>
                       <tbody>';
              $sql="SELECT id_empleado, upper(concat(apellido, ', ', nombre)) as apenom
                    FROM empleados e
                    where (activo) and (id_cargo = 1) and (id_empleador = $_POST[emple]) and (e.id_estructura = $_SESSION[structure])
                    order by apenom";
              $result = mysql_query($sql, $conn);

              while($row = mysql_fetch_array($result)){
                         $tabla.="<tr>
                                      <td WIDTH='150px'>".htmlentities($row[apenom])."</td>
                                      <td></td>";
                         $fecha1=strtotime($desde);
                         for ($i = 0; $i <= $dias; $i++){
                             $class="";
                             if (date(N, $fecha1) == 7){
                                $class="dom";
                             }
                             $dianov=date(d, ($fecha1));
                             $mesnove=date(m, ($fecha1));
                             $anionove=date(Y, ($fecha1));

                             $fecnove ="$anionove-$mesnove-$dianov";
                             $check = "";
                             if (array_key_exists($row['id_empleado'], $results))
                                if (in_array("'$fecnove'", $results[$row['id_empleado']])){
                                   $check="checked";
                                }
                             $nov="<input type='checkbox' $check onClick='cambiarFranco($row[id_empleado], \"$fecnove\", this.checked);'>";
                             $tabla.="<td align='center' class='$class'>$nov</td>";
                             $fecha1=$fecha1 +86400;
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
                                $('#tablitasssss').tableHover({colClass: 'hover', cellClass: 'hovercell', clickClass: 'click', ignoreCols: [1]});
                                function cambiarFranco(emp, fec, st){
                                         var state = 0;
                                         if (st){
                                            state = 1;
                                         }
                                         $.post('/modelo/rrhh/diagfr.php', {accion:'update', id_e: emp, fecha: fec, estado: state});
                                }";
              $tabla.="</script>";
              mysql_free_result($result);
              mysql_close($conn);
              print $tabla;
  }
  elseif($accion == 'update'){
     $st = $_POST['estado'];
     $conductor = $_POST['id_e'];
     $fecha = $_POST['fecha'];
     if ($st){
        $campos = 'id, id_empleado, desde, hasta, id_novedad, estado, activa, pendiente, usuario, fecha_alta, usertxt, id_estructura';
        $values = "$conductor, '$fecha', '$fecha', 15, 'no_disp', 1, 0, $_SESSION[userid], now(), '', $_SESSION[structure]";
        print (insert('novedades', $campos, $values));
     }
     else{
          $conn = conexcion();
          $sql = "DELETE FROM novedades where (desde = '$fecha') and (id_empleado = $conductor) and (id_novedad = 15) and (id_estructura = $_SESSION[structure])";
          mysql_query($sql, $conn);
          mysql_close($conn);
     }
  }
?>

