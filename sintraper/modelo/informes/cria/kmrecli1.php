<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[nombre]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $struct = '';
     $cliente = '';
     if ($_POST['str']){
        $struct = "and (o.id_estructura = $_POST[str])";
        if ($_POST['cli']){
           $cliente = "and (o.id_cliente = $_POST[cli])";
        }
     }
     else{
          if (isset($_POST['cli'])){
             if ($_POST['cli']){
                $cliente = "and (o.id_cliente = $_POST[cli])";
             }
          }
     }
     $sql_km = "select sum(km) as km
                from ordenes o
                where (fservicio between '$desde' and '$hasta') $struct $cliente";

     $sql = "SELECT sum(km) as km, count(*) as cantsrv, count(distinct(id_micro)) as unidades, upper(e.nombre) as str, upper(c.razon_social) as cli
             FROM ordenes o
             inner join estructuras e on e.id = o.id_estructura
             inner join clientes c on c.id = o.id_cliente
             where (fservicio between '$desde' and '$hasta') $struct $cliente
             group by o.id_estructura, o.id_cliente";

     $conn = conexcion();
     $res_km = mysql_query($sql_km, $conn);
     if ($row = mysql_fetch_array($res_km)){
        $km = $row['km'];
     }
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" class="ui-widget ui-widget-content">
                    <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Km</th>
                        <th>Cant. Serv.</th>
                        <th>Unid. Utilizadas</th>
                        <th>% del Total</th>
                    </tr>
                    </thead>
                    <tbody>';
     $por = 0;
     while ($data = mysql_fetch_array($result)){
           if ($km){
              $por = round((($data[km]/$km)*100),4)." %";
           }
           $tabla.="<tr>
                        <td align='left'>$data[cli]</td>
                        <td align='right'>$data[km]</td>
                        <td align='right'>$data[cantsrv]</td>
                        <td align='right'>$data[unidades]</td>
                        <td align='right'>$por</td>
                    </tr>";
           $por = 0;
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script type="text/javascript">
                          		$("#example").dataTable({
                                                        "sDom": \'<"top">rt<"bottom"flp><"clear">\',
					                                    "sScrollY": "200px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "",
                                                                     "sInfoFiltered": ""}
				                                       });
                  </script>
';
    print $tabla;
  }
  
?>

