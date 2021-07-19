<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cpy'){
     $conn = conexcion();
     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $sql = "SELECT nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_micro, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario FROM ordenes WHERE (id in ($ordenes)) and (id_estructura = ".STRUCTURED.")";
     $result = mysql_query($sql, $conn);
     $valores = "";
     $ok = 0;
     while ($data = mysql_fetch_array($result)){
           $campos = "id,id_estructura,fservicio,nombre,hcitacion,hsalida,hllegada,hfinservicio,km,id_ciudad_origen,id_estructura_ciudad_origen,id_ciudad_destino,id_estructura_ciudad_destino,id_cliente,id_estructura_cliente,finalizada,borrada,comentario, id_user";
           $valores = STRUCTURED.", '$fecha', '$data[nombre]','$data[hcitacion]','$data[hsalida]','$data[hllegada]','$data[hfinservicio]','$data[km]',$data[id_ciudad_origen],$data[id_estructura_ciudad_origen],$data[id_ciudad_destino],$data[id_estructura_ciudad_destino],$data[id_cliente],$data[id_estructura_cliente],'0','0','', $_SESSION[userid]";
           if($data['id_micro']){
                   $campos.=", id_micro";
                   $valores.=", $data[id_micro]";
           }
           if ($data['id_servicio'] && $data['id_estructura_servicio']){
                   $campos.=", id_servicio, id_estructura_servicio";
                   $valores.=", $data[id_servicio], $data[id_estructura_servicio]";
           }
           if ($data['id_cliente_vacio'] && $data['id_estructura_cliente_vacio']){
                   $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                   $valores.=", $data[id_cliente_vacio], $data[id_estructura_cliente_vacio]";
           }
           if ($data['id_chofer_1'] && $data['id_estructura_chofer1']){
                   $campos.=", id_chofer_1, id_estructura_chofer1";
                   $valores.= ", $data[id_chofer_1], $data[id_estructura_chofer1] ";
           }
           if ($data['id_chofer_2'] && $data['id_estructura_chofer2']){
                   $campos.=", id_chofer_2, id_estructura_chofer2";
                   $valores.= ", $data[id_chofer_2],$data[id_estructura_chofer2]";
           }
           $ok+= insert('ordenes', $campos, $valores);
     }
     print $ok;
  }
  
?>

