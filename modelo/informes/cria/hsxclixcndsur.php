<?php
  session_start();
  set_time_limit(0);
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  
function mintohour($min){
         $hours=floor($min/60);
         $min=$min%60;
         if($min < 10)
         return "$hours:0$min";
         else
         return "$hours:$min";
}
  
  include ('../../../controlador/bdadmin.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);
     if (isset($_POST['all']))
     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
     else
          $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>".htmlentities($data[0])."</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'ldsrv'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cond = '';

     if ($_POST['cli']){
        $cond.= "and (id_cliente = $_POST[cli])";
     }
     $sql = " SELECT date_format(citacion, '%d/%m/%Y') as fecha, upper(razon_social) as cliente, concat(apellido,', ', em.nombre) as conductor,
                    date_format(citacion,'%%d/%m/%Y %H:%i') as citacion, 
                    time_format(finalizacion,'%H:%i') as finaliza, o.nombre
              FROM (
                    SELECT o.id, citacion, salida, llegada, finalizacion, id_chofer_1, id_cliente, id_estructura_cliente
                    from ordenes o
                    inner join horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
                    where id_estructura = 2 AND id_chofer_1 is not null
                    union
                    select o.id, citacion, salida, llegada, finalizacion, id_chofer_2, id_cliente, id_estructura_cliente
                    from ordenes o
                    inner join horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
                    where id_estructura = 2 AND fservicio = '2020-07-02' and id_chofer_2 is not null
                    union
                    select o.id, citacion, salida, llegada, finalizacion, id_empleado, id_cliente, id_estructura_cliente
                    from ordenes o
                    inner join horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
                    inner join tripulacionxordenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                    where id_estructura = 2 
                  ) o
              inner join empleados em on em.id_empleado = id_chofer_1
              inner join clientes c on c.id = id_cliente and c.id_estructura = id_estructura_cliente
              WHERE (date(citacion) between '$desde' and '$hasta') OR (date(finalizacion) between '$desde' AND '$hasta') 
                      OR ((citacion > '$desde') AND (finalizacion < '$hasta'))
              ";

         //    die($sql);
     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $total_gral=0;
     
     while ($data){
           $cliente = $data['cliente'];
           $i = 0;
           $tabla.='<tr class="ui-widget-header">
                        <th colspan="5">'.$data[cliente].'</th>
                    </tr>';
           $tot_cli=0;
           while (($data)&&($cliente == $data['cliente'])){
                 $tabla.="<tr class='ui-widget-header'>
                              <th colspan='5'>Servicios correspondientes al $data[fecha]</th>
                         </tr>
                         <tr>
                             <th>Conductor</th>
                             <th>Servicio</th>
                             <th>H. Salida</th>
                             <th>H. Llegada</th>
                             <th>Cant. Hs.</th>
                         </tr>";
                 $fecha = $data[fecha];
                 $tot_fecha=0;
                 while (($data)&&($cliente == $data['cliente'])&&($fecha == $data['fecha']))
                 {
                       $last=$data;
                       $tot_fecha+=$data[5];
                       $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                       $tabla.="<tr bgcolor='$color'>
                              <td align='left'>".htmlentities($data[2])."</td>
                              <td align='left'>".htmlentities($data[6])."</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                              <td align='right' >".mintohour($data[5]/60)."</td>
                          </tr>";
                          $data = mysql_fetch_array($result);
                          $i++;
                 }
                 $tot_cli+=$tot_fecha;
                 $tabla.="<tr><td colspan='4' align='right'><b>Total Hora del $last[0]:</b></td><td align='right'> <b>".mintohour($tot_fecha/60)."</b></td></tr>";                                                    //$
           }
           $tabla.="<tr><td colspan='4' align='right'><b>TOTAL HORAS DEL CLIENTE: $last[1]:</b></td><td align='right'> <b>".mintohour($tot_cli/60)."</b></td></tr>";
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">
                          $(".preciosiva").keypress(function(event){
	                                                                var keycode = (event.keyCode ? event.keyCode : event.which);
	                                                                if(keycode == "13"){
                                                                               var ot = $(this).attr("id").split("-")[1];
                                                                               var value = $(this).val();
                                                                               var comp = $(this);
		                                                                       if ($.isNumeric(value)){
                                                                                  $.post("/modelo/turismo/srvlst.php",
                                                                                         {accion:"svepr", ordt:ot, monto: value},
                                                                                         function(data){
                                                                                                        var response = $.parseJSON(data);
                                                                                                        if (response.status){
                                                                                                           $("#pf"+ot).html(response.pfinal);
                                                                                                        }
                                                                                                        });
		                                                                       }
		                                                                       else{
                                                                                    alert("El importe ingresado es invalido!!");
                                                                                    $(this).select();
		                                                                       }
                                                                    }
                                                                    });
                  </script>';
    print $tabla;
  }
  
?>

