<?php
     set_time_limit(0);
     error_reporting(0);
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT un.id, concat(interno, ' - ', razon_social) as interno
             FROM unidades un
             INNER JOIN empleadores e ON id_propietario = e.id AND id_estructura_propietario = e.id_estructura
             order by un.interno";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="internos" name="internos" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[interno]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#internos').selectmenu({width: 300});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif ($accion == 'ldcond'){
     $conn = conexcion();

     $sql =
     "SELECT id_empleado, upper(concat(apellido,', ', nombre)) as empleado
             FROM empleados e
             where e.id_estructura =  $_SESSION[structure]
             order by apellido, nombre";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="conductores" name="conductores" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[0]'>$data[1]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#conductores').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'list'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $interno = '';
     if ($_POST['internos']){
           $interno = "and (u.id = $_POST[internos])";
     }
     $cond = '';
     if ($_POST['conductores']){
           $cond = "and (e.id_empleado = $_POST[conductores])";
     }
     $all = "";
     if ($_POST['pend'] == 0){
        $all = "and (reparada = 0)";
     }
     
     $filtrarf="";
     if (($_POST['desde'] != '') && ($_POST['hasta'] != '')){
        $filtrarf = "and fecha_siniestro between '$desde' and '$hasta'";
     }
     
     if (($_POST['nrosin'] != '')){
        $filtrnro = "and s.id = $_POST[nrosin]";
     }
     
     if (($_POST['nrosincomp'] != '')){
        $filtrnroas = "and s.num_sin_asignado = '$_POST[nrosincomp]'";
     }

     
     $sql = "SELECT legajo,  upper(concat(apellido,', ', nombre)) as empleado, nrodoc, siniestro_numero, date_format(fecha_siniestro, '%d/%m/%Y') as fecha, time_format(hora_siniestro, '%H:%i') as hora,
       ec.estado, cu.codigo, calle1, calle2, upper(ci.ciudad) as ciudad, upper(tl.tipo) as tipolesion,
       res.responsabilidad as resp,
       upper(ca.cobertura) as cobertura, interno, coa.compania, numero_poliza, (if (costos_administrativos is null, 0, costos_administrativos)+ if(costos_reparacion_unidades is null, 0, costos_reparacion_unidades)) as indemnizacion_a_terceros, s.id, latitud, longitud, concat('Num. Siniestro: ',s.id, '  -  Fecha: ',date_format(fecha_siniestro, '%d/%m/%Y')) as detalle, s.id as id_sin,
       mdr.id as minuta
FROM siniestros s
left join empleados e on e.id_empleado = s.id_empleado
left join estadoClima ec on ec.id = s.estado_clima
left join codUbicacionSiniestro cu on cu.id = s.cod_ubicacion
left join ciudades ci on ci.id = s.id_localidad
left join tipoLesionSiniestro tl on tl.id = s.tipo_lesion
left join coberturaAfectadaSiniestro ca on ca.id = s.cobertura_afectada
left join unidades u on u.id = s.id_coche
left join clientes cl on cl.id = s.id_cliente and cl.id_estructura = s.id_estructura
left join companiasAseguradoras coa on coa.id = s.compania_seguro
left join resp_estimada_siniestro res on res.id = s.resp_estimada
left join minuta_de_reunion mdr on mdr.id_siniestro = s.id
where not s.borrada $filtrarf $cond $interno $filtrnro $filtrnroas
order by $_POST[order] $_POST[asds]";
   //  die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $pendi="";
     if ($_POST['pend']){
        //$pendi = "<div class='navigation' style='width:200px'><b>Pendientes de reparacion</b></div><br>";
     }

     
     $tabla=$pendi.'
                    <div id="tabs">
  <ul>
    <li><a href="#tabs-1">Detalle de Siniestros</a></li>
  </ul>
    <div id="tabs-1">
                    <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Nro. Sin.</th>
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>DNI</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Clima</th>
                        <th>Ubi.</th>
                        <th>Calle 1</th>
                        <th>Calle 2</th>
                        <th>Ciudad</th>
                        <th>Tipo Lesion</th>
                        <th>Resp.</th>
                        <th>Cob. Af.</th>
                        <th>Interno</th>
                        <th>Comp. Seguro</th>
                        <th>Num. Poliza</th>
                        <th>Gastos Originados</th>
                        <th>Editar</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     $marcadores = "";
     $i=0;
     while ($data){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $print='';
               if ($data[minuta])
                  $print=" <a href='../../modelo/segvial/mta.php?nro=$data[minuta]' target='_blank'><img src='../../vista/print.png' border='0' width='15' height='15'></a>";
               $tabla.="<tr id='$data[18]' $class>
                            <td align='right'>$data[3]</td>
                            <td align='right'>$data[0]</td>
                            <td align='left'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='center'>$data[4]</td>
                            <td align='center'>$data[5]</td>
                            <td align='left'>$data[6]</td>
                            <td align='center'>$data[7]</td>
                            <td align='left'>$data[8]</td>
                            <td align='left'>$data[9]</td>
                            <td align='left'>$data[10]</td>
                            <td align='left'>$data[11]</td>
                            <td align='left'>$data[12]</td>
                            <td align='left'>$data[13]</td>
                            <td align='right'>$data[14]</td>
                            <td align='left'>$data[15]</td>
                            <td align='right'>$data[16]</td>
                            <td align='right'>$data[17]</td>
                            <td align='right'><a href='../../vista/segvial/upsnt.php?nro=$data[id]'><img src='../../vista/edit.png' border='0' width='15' height='15'></a></td>
                            </tr>";
               if ($data[latitud] && $data[longitud]){
                  if ($i == 0){
                         $marcadores =  "['$row[detalle]', $row[latitud], $row[longitud], $data[id_sin]]";
                  }
                 else{
                    $marcadores.= ",['$row[detalle]', $row[latitud], $row[longitud], $data[id_sin]]";
                 }
                 $i++;
               }
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>
              <a href="../../vista/segvial/mapa.php?ds='.$desde.'&hs='.$hasta.'" id="mapa" target="_blank">Ver mapa de siniestros</a>
                </div>
              </div>

              <script>
                      $( "#tabs" ).tabs();
                      $( "#mapa" ).button();

              </script>
                  <style>
                         #example th{
                                padding:13px;
                                font-size: 82.5%;
                                }
                         #example tr{
                                padding:13px;
                                font-size: 80.5%;
                                }
                         .pad{
                                padding:10px;
                                font-size: 85%;
                                }
                         #example tbody tr:hover {

                                        background-color: #FF8080;

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
  elseif($accion == 'detsin'){
                 $result = ejecutarSQL("SELECT legajo,  upper(concat(apellido,', ', nombre)) as empleado, nrodoc, siniestro_numero, date_format(fecha_siniestro, '%d/%m/%Y') as fecha, time_format(hora_siniestro, '%H:%i') as hora,
       ec.estado, cu.codigo, calle1, calle2, upper(ci.ciudad) as ciudad, upper(tl.tipo) as tipolesion,
       cl.razon_social as cliente, responsabilidad, maniobra, norma,
       upper(ca.cobertura) as cobertura, interno, coa.compania, numero_poliza, (costos_administrativos+costos_reparacion_unidades) as indemnizacion_a_terceros, s.id
FROM siniestros s
left join empleados e on e.id_empleado = s.id_empleado
left join estadoClima ec on ec.id = s.estado_clima
left join codUbicacionSiniestro cu on cu.id = s.cod_ubicacion
left join ciudades ci on ci.id = s.id_localidad
left join tipoLesionSiniestro tl on tl.id = s.tipo_lesion
left join coberturaAfectadaSiniestro ca on ca.id = s.cobertura_afectada
left join unidades u on u.id = s.id_coche
left join clientes cl on cl.id = s.id_cliente
left join companiasAseguradoras coa on coa.id = s.compania_seguro
left join resp_estimada_siniestro re on re.id = s.resp_estimada
left join tipo_maniobra_siniestro tm on tm.id = s.tipo_maniobra
left join normas_seg_vial ns on ns.id = s.norma_no_respetada
where s.id = $_POST[sin]");
           $row = mysql_fetch_array($result);

         print'<form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Detalle Siniestro</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="75%">
                                <tr>
                                    <td WIDTH="20%">Estructura</td>
                                    <td>
                                        <input type="text" size="20" class="required ui-widget ui-widget-content  ui-corner-all" value="'.$row[0].'">
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Siniestro</label></td>
                                    <td><input id="fsin" name="fsin" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" value="'.$row[fecha].'"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Siniestro</td>
                                    <td colspan="2"><input id="hsin" maxlength="5" size="4" name="hsin" class="required hora ui-widget-content ui-corner-all" value="'.$row[hora].'"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Num. Siniestro</td>
                                    <td colspan="2"><input id="nsin" size="8" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[siniestro_numero].'"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Conductor</td>
                                    <td>
                                        <input id="nsin" size="35" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[empleado].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[clien].'"/>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Estado clima</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[estado].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cod. Ubicacion</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[codigo].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Ciudad Siniestro</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[ciudad].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Calle 1</td>
                                    <td>
                                        <input id="calle1sin"  size="30" name="calle1sin" class="required ui-widget-content ui-corner-all" value="'.$row[calle1].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Calle 2</td>
                                    <td>
                                        <input id="calle2sin" size="30" name="calle2sin" class="required ui-widget-content ui-corner-all" value="'.$row[calle2].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo Lesion</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[tipolesion].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Responsabilidad</td>
                                    <td>
                                        <input id="nsin" size="18" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[responsabilidad].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td>
                                        <input id="nsin" size="8" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[interno].'"/>
                                    </td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cobertura Afectada</td>
                                    <td>
                                        <input id="nsin" size="35" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[cobertura].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo Maniobra</td>
                                    <td>
                                          <textarea rows="3" cols="60" readonly class="required ui-widget-content ui-corner-all">'.$row[maniobra].'</textarea>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Norma No Respetada</td>
                                    <td>
                                        <textarea rows="3" cols="60" readonly class="required ui-widget-content ui-corner-all">'.$row[norma].'</textarea>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Compañia Aseguradora</td>
                                    <td>
                                        <input id="nsin" size="30" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="'.$row[compania].'"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Numero poliza</td>
                                    <td colspan="2"><input id="poliza" size="15" name="poliza" class="required ui-widget-content number  ui-corner-all" value="'.$row[numero_poliza].'"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Costos Administrativos</td>
                                    <td colspan="2"><input id="costadmin" size="7" name="costadmin" class="ui-widget-content number  ui-corner-all" value="'.$row['costos_administrativos'].'"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Costos Reparacion Unidades</td>
                                    <td colspan="2"><input id="costrepa" size="7" name="costrepa" class="ui-widget-content number  ui-corner-all" value="'.$row['costos_reparacion_unidades'].'"/></td>
                                </tr>
                         </table>
	</fieldset>
</form>';





  }
  
?>

