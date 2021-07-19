<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'lirp'){
   /*  $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/'); */
     $prov = '';
     if ($_POST['proov']){
           $prov = "and (id_proveedor = $_POST[proov])";
     }
     $marca = '';
     if ($_POST['marcas']){
           $cond = "and (id_empleado = $_POST[marcas])";
     }
     
     $sql = "SELECT if (e.id is null, r.id, 0) as id, upper(descripcion) as descr, upper(marca), upper(proveedor)
             FROM repuestos r
             left join evaluacion_repuestos e on e.id_repuesto = r.id
             inner join marca_repuesto mr on mr.id = r.id_marca
             inner join proveedores p on p.id = r.id_proveedor
             where activo $prov $marca
             order by descripcion";
  //   die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $pendi="";

     
     $tabla='<table id="example" name="example" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Descripcion</th>
                        <th>Marca</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>';
     while ($data = mysql_fetch_array($result)){
               $estado = "";
               if (!$data[0])
                  $estado="En evaluacion";
               $tabla.="<tr id='$data[0]' $class>
                            <td align='left'>$data[1]</td>
                            <td align='left'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='left'>$estado</td>
                            </tr>";
     }
     $tabla.='</tbody>
              </table>
              <script>
                                   $("#example tbody tr").click(function(){
                                                                           var prod = $(this).attr("id");
                                                                           if (prod != 0){
                                                                           $("#repto").val(prod);
                                                                           var children = id = $(this).find("td").eq(0).html();
                                                                           $("#txtlgn").text("Iniciar evaluacion de: "+children);
                                                                           $("#eval").show();
                                                                           }
                                                                           else{
                                                                                $("#eval").hide();
                                                                           }
                                                                           $("#inicioev").val("");
                                                                           $("#evakm").val("");
                                                                           $("#evadias").val("");
                                                                           });
                                   $("#example").dataTable({
                                                         "sScrollY": "350px",
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
                  <style>
                         #example thead{ font-size: 55%; }
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

}
                  </style>';
    print $tabla;
  }
  elseif($accion == 'modanom'){
                 $repa = 0;
                 $fecha = "NULL";
                 $hora = "NULL";
                 if (isset($_POST['rpda'])){
                    $repa = 1;
                    if ($_POST['frepa']){
                       $fecha = "'".dateToMysql($_POST['frepa'], '/')."'";
                    }
                    if ($_POST['hrepa']){
                       $hora = "'".$_POST['hrepa']."'";
                    }
                 }
                 try{
                    update("anomalias", "reparada, observacion_taller, fecha_reparacion, hora_reparacion, id_usuario_reparacion, orden_trabajo", "$repa, '$_POST[otaller]', $fecha, $hora, $_SESSION[userid], '$_POST[orepa]'", "(id = $_POST[anomalia])");
                    print "1";
                 }
                 catch (Exception $e) {
                       print "0";
                 }
  }
  elseif($accion == 'adm'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         $marca = $_POST['mca'];
         if ($marca){
            $id = insert("marca_repuesto", "id, marca", "'$marca'");
            $response[id]= $id;
            $response[mca]= strtoupper($marca);
            $response[msge] = "Se ha almacenado con exito la marca del producto en la BD";
         }
         else{
              $response[status] = false;
              $response[msge] = "El campo no puede permanecer en blanco!!!";
         }
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar la marca del producto!!!";
                           print json_encode($response);
                          };
  }
  elseif($accion == 'adp'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         $prov = $_POST['prov'];
         if ($prov){
            $id = insert("proveedores", "id, proveedor", "'$prov'");
            $response[id]= $id;
            $response[pro]= strtoupper($prov);
            $response[msge] = "Se ha almacenado con exito el proveedor en la BD";
         }
         else{
              $response[status] = false;
              $response[msge] = "El campo no puede permanecer en blanco!!!";
         }
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar el proveedor!!!";
                           print json_encode($response);
                          };
  }
  elseif($accion == 'addpr'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         $prov = $_POST['proov'];
         $marca = $_POST['marcas'];
         $desc = $_POST['desc'];
         $codigo = $_POST['code'];
         if (!$prov){
            $response[msge] = "Debe seleccionar un proveedor!!";
            $response[status] = false;
         }
         elseif(!$marca){
            $response[msge] = "Debe seleccionar una marca!!";
            $response[status] = false;
         }
         elseif(!$desc){
            $response[msge] = "Debe ingresar la descripcion del articulo!!";
            $response[status] = false;
         }
         else{
            $id = insert("repuestos", "id, descripcion, codigo_barras, id_marca, id_proveedor, activo", "'$desc', '$codigo', $marca, $prov, 1");
            $response[id]= $id;
            $response[pro]= strtoupper($desc);
            $response[msge] = "Se ha almacenado con exito el articulo en la BD";
         }
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar el articulo!!!";
                           print json_encode($response);
                          };
  }
  elseif($accion == 'inev'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         $finicio = $_POST['inicioev'];
         $interno = $_POST['interno'];
         $km = $_POST['evakm'];
         $dias = $_POST['evadias'];
         $rpto = $_POST['repto'];
         if (!$finicio){
            $response[msge] = "Debe seleccionar la fecha de inicio de la prueba!!";
            $response[status] = false;
         }
         elseif(!$dias && !$km){
            $response[msge] = "Debe seleccionar como mecanismo de evaluacion KM o dias!!";
            $response[status] = false;
         }
         else{
              $km = ($_POST['evakm']?$_POST['evakm']:'NULL');
              $dias = ($_POST['evadias']?$_POST['evadias']:'NULL');
              $finicio = dateToMysql($_POST['inicioev'], '/');
              $id = insert("evaluacion_repuestos", "id, fecha_inicio_prueba, id_repuesto, id_interno, km_evaluacion, dias_evaluacion, fecha_carga, id_usr_carga", "'$finicio', $rpto, $interno, $km, $dias, now(), $_SESSION[userid]");
              $response[id]= $id;
              $response[pro]= strtoupper($prov);
              $response[msge] = "Se ha dado inicio a la evaluacion del articulo!!!";
         }
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar el proveedor!!!";
                           print json_encode($response);
                          };
  }
  
?>

