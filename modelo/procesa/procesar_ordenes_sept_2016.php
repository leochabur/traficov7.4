<?php
  session_start();
  include ($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define('STRUCTURED', $_SESSION['structure']);
  
  $accion = $_POST['accion'];
  if ($accion == 'soe'){ //codigo para guardar una orden de un vacio
              $estructura = STRUCTURED;
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";
              
              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";
              
              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";
              
              $cliente = $_POST['cliente_vacio'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";
              
              $corresponde_a = $_POST['corresponde_a'];
              $campos.=",id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=",$corresponde_a, $estructura";
              
              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";
              
              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";
              
              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";
              
              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";
              
              $hfins = $_POST['hfins'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";
              
              $km = $_POST['km'];
              $campos.=",km, vacio";
              $values.=",$km, 1";
              
              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              
              $id = insert('ordenes', "id, $campos", $values);
              
              $conn = conexcion();
		      $sql="SELECT * FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estado = 1) and (id_estructura = $_SESSION[structure])";
		      $resul = mysql_query($sql, $conn);
		      if (mysql_num_rows($result))
                 mysql_query("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $id)", $conn);
              mysql_free_result($result);
              mysql_close($conn);
              
              


  }elseif ($accion == 'orev'){
              
              $estructura = STRUCTURED;
              
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];

              $campos.=",fservicio, id_claseservicio, id_estructuraclaseservicio";
              $values.=",'$fecha', $_POST[clase], $estructura";

              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";


              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";

              $hfins = $_POST['hfins'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";
              
              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";
              
              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }
              
              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              $id = insert('ordenes', "id, $campos", $values);
              $campos = "id_orden, id_estructura, id_turno, id_estructura_turno, id_tipo_servicio, id_estructura_tipo_servicio, i_v";
              $values = "$id, $estructura, $_POST[turnos], $estructura, $_POST[turnos], $estructura, '$_POST[iv]'";
             // die("INSERT INTO tipotnoordenes ($values)");
             $val = $id;
             
              $conn = conexcion();
		      $sql="SELECT * FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estado = 1) and (id_estructura = $_SESSION[structure])";
		      $resul = mysql_query($sql, $conn);
		      if (mysql_num_rows($result))
                 mysql_query("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $id)", $conn);
              mysql_free_result($result);
              mysql_close($conn);
              
              $id = ejecutarSQL("INSERT INTO tipotnoordenes values ($values)");
              echo $val;

  }elseif ($accion == 'soser'){ //codigo para guarar una orden de un servicio en la BD
              $conn = conexcion();
              $estructura = STRUCTURED;

              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";

              $servicio = $_POST['horario'];
              $campos.=",id_servicio, id_estructura_servicio";
              $values.=", $servicio, $estructura";
              
              $sql = "SELECT c.nombre, c.ciudades_id_origen, c.ciudades_id_destino, c.vacio, if (c.id_cliente_vacio is null, 0, c.id_cliente_vacio) as id_cliente_vacio , c.id_estructura_cliente_vacio
                      FROM servicios s
                      INNER JOIN cronogramas c on ((c.id = s.id_cronograma)and(c.id_estructura = s.id_estructura_cronograma))
                      WHERE ((s.id = $servicio)and(s.id_estructura = $estructura))";
              $result = mysql_query($sql, $conn); //recupera el nombre del cronograma para asociarlo a la orden
              if ($data = mysql_fetch_array($result)){
                 $nombre  = $data['nombre'];
                 $origen  = $data['ciudades_id_origen'];
                 $destino = $data['ciudades_id_destino'];
                 $vacio = $data['vacio'];
                 if ($vacio == 1){
                    $campos.=", vacio";
                    $values.=", $vacio";
                    if ($data['id_cliente_vacio']){
                       $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                       $values.=", $data[id_cliente_vacio], $data[id_estructura_cliente_vacio]";
                    }
                    
                 }
              }
              mysql_close($conn);
              
              $campos.=",nombre";
              $values.=",'$nombre'";
              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";

              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";
              
              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";
              
              $hfinserv = $_POST['hfinserv'];
              $campos.=",hfinservicio";
              $values.=",'$hfinserv'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              
              $id = insert('ordenes', "id, $campos", $values);
          //    die($id);
              
              $conn = conexcion();
		      $sql="SELECT * FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estado = 1) and (id_estructura = $_SESSION[structure])";
            //  die($sql);
              $resul = mysql_query($sql, $conn);
		      if ($data = mysql_fetch_array($result))
                 mysql_query("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $id)", $conn) or die("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $id)");
              mysql_free_result($result);
              mysql_close($conn);

  }elseif ($accion == 'soes'){

              $estructura = STRUCTURED;

              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";

              $nombre  = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $cli_vacio = $vacio = getOpcion('cliente-vacio', $_SESSION['structure']);
              $campos.=",id_cliente, id_estructura_cliente, vacio";
              $values.=", $cli_vacio, $estructura, 1";
              
              $clientevacio = $_POST['corresponde'];
              $campos.=",id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=",$clientevacio, $estructura";

              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";

              $hfinserv = $_POST['hfinserv'];
              $campos.=",hfinservicio";
              $values.=",'$hfinserv'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }

              $id = insert('ordenes', "id, $campos", $values);
              
              $conn = conexcion();
		      $sql="SELECT * FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estado = 1) and (id_estructura = $_SESSION[structure])";
		      $resul = mysql_query($sql, $conn);
		      if (mysql_num_rows($result))
                 mysql_query("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $id)", $conn);
              mysql_free_result($result);
              mysql_close($conn);

  }elseif ($accion == 'deor'){ //codigo para eliminar una orden
              $id = $_POST['order'];
              $estructura = STRUCTURED;
              $campo='borrada, id_user, fecha_accion';
              $value="1, $_SESSION[userid], now()";
              backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
              update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
  }elseif ($accion == 'sendodate'){ //CODIGO PARA ENVIAR UNA ORDEN A OTRA FECHA
              $estructura = STRUCTURED;
              $fecha = $_POST['fecha'];
              $id = $_POST['order'];
              
              $campo='fservicio, id_user, fecha_accion';
              $value="'$fecha', $_SESSION[userid], now()";
              backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
              update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
              update('horarios_ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
  }elseif ($accion == 'chemcli'){ //CODIGO PCAMBIAR EL CLIENTE AL CUAL SE LE AFECTA UN VACIO
              $estructura = STRUCTURED;
              $cliente = $_POST['cli'];
              $id = $_POST['order'];
              $sql =     "SELECT * FROM ordenes where (id = $id) and (id_estructura = $estructura) and (vacio)";
              $sql_cli = "SELECT razon_social FROM clientes c where id = $cliente and id_estructura = ".STRUCTURED;
              $conn = conexcion();
              $result = mysql_query($sql, $conn);
              $result_cli = mysql_query($sql_cli, $conn);
              mysql_close($conn);
              if(mysql_num_rows($result) > 0){
                      $data = mysql_fetch_array($result);
                      $crono = $data['nombre'];
                      $data_cli = mysql_fetch_array($result_cli);
                      $nom_cliente = $data_cli['razon_social'];
                      $pos = strpos($crono, '~');
                      if ($pos){
                         $crono=substr_replace($crono, "($nom_cliente)", ($pos+1), strlen($crono));
                      }
                      else{
                           $crono.= "~($nom_cliente)";
                      }
                      $campo='nombre, id_cliente_vacio, id_estructura_cliente_vacio, id_user, fecha_accion';
                      $value="'$crono', $cliente, $estructura, $_SESSION[userid], now()";
                      backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
                      update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
                      update('horarios_ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
                      print $crono;
              }
  }
?>
