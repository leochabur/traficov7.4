<?
  session_start();

  include_once('../../controlador/ejecutar_sql.php');
  include_once('../../controlador/bdadmin.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cls'){
     $id = $_POST['orden']; // id del registro
     $campo = 'finalizada';
     $value = '1';
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
     
  }
elseif ($accion == 'susp'){
     $id = $_POST['orden']; // id del registro
     $campo = 'suspendida';
     $value = '1';
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");

  }
elseif ($accion == 'check'){
     $id = $_POST['orden']; // id del registro
     $campo = 'checkeada';
     $value = '1';
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");

  }elseif($accion == 'ldo'){
                  $conn = conexcion();
                  $id = $_POST['orden']; // id del registro
                  $sql="SELECT vacio, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hllegada, '%H:%i') as hllegada, date_format(hfinservicio, '%H:%i') as hfinservicio, id_ciudad_origen, id_ciudad_destino, id_cliente, id_chofer_1, if (ISNULL(id_cliente_vacio), 0, id_cliente_vacio) as id_cliente_vacio, km, fservicio
                        FROM ordenes o
                        WHERE (id = $id)";
                  $result = mysql_query($sql, $conn) or die(mysql_error($conn));
                  $rta = '';
                  if (mysql_num_rows($result)){
                     $data = mysql_fetch_array($result);
                     $rta = $data['hcitacion'];
                     $rta.= '-'.$data['hsalida'];
                     $rta.= '-'.$data['hllegada'];
                     $rta.= '-'.$data['hfinservicio'];
                     $rta.= '-'.$data['id_unidad_interno'];
                     $rta.= '-'.$data['id_ciudad_origen'];
                     $rta.= '-'.$data['id_ciudad_destino'];
                     $rta.= '-'.$data['id_cliente'];
                     $rta.= '-'.$data['id_chofer_1'];
                     $rta.= '-'.$data['vacio'];
                     $rta.= '-'.$data['km'];
                     $rta.= '-'.$data['fservicio'];
                  }
                  mysql_close($conn);
                  print $rta;
  }elseif($accion == 'update'){
                  $campo = "id_user, fecha_accion,".$_POST['cmp'];
                  $value = $_POST['valor'];
                  $struct = STRUCTURED;
                  if($_POST['valor'] == 0){
                     $value = 'NULL';
                     $struct = 'NULL';
                  }
                  $valor = "$_SESSION[userid], now(), $value";
                  $clave = $_POST['clave'];

                  if (isset($_POST['cmpstruct'])){
                     /*if ($_POST['cmpstruct'] == 'iem'){
                        $campo.=', id_estructura_micro';
                        $valor.=', '.$struct;
                     }
                     else*/
                     if ($_POST['cmpstruct'] == 'iech1'){
                            $campo.=', id_estructura_chofer1';
                            $valor.=', '.$struct;
                     }
                     elseif ($_POST['cmpstruct'] == 'iech2'){
                            $campo.=', id_estructura_chofer2';
                            $valor.=', '.$struct;
                     }
                  }
                  backup('ordenes', 'ordenes_modificadas', "(id = $clave) and (id_estructura = ".STRUCTURED.")");
                  print update('ordenes', $campo, $valor, "(id = $clave) and (id_estructura = ".STRUCTURED.")");
  }
?>

