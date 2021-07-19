<?
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];
  if ($accion == 'ssv'){ //codigo para guarar un servicio en la BD
              $estructura = STRUCTURED;
              $nom_crono = $_POST['crono'];
              $cliente = $_POST['cliente'];
              if ($_POST['srvvacio'] == 1){
                 $cliente = getOpcion('cliente-vacio', $_SESSION['structure']);
              }
              $origen = $_POST['origen'];
              $destino = $_POST['destino'];
              $clase = $_POST['clase'];
              $km = $_POST['km'];
              $tiempo = $_POST['tiempo'];
              $campos = "id,id_estructura,nombre,km,id_cliente,id_estructura_cliente,tiempo_viaje,ciudades_id_origen,ciudades_id_estructura_origen,ciudades_id_destino,ciudades_id_estructura_destino,claseServicio_id,claseServicio_id_estructura";
              $values = "$estructura, '$nom_crono', $km, $cliente, $estructura, '$tiempo', $origen, $estructura, $destino, $estructura, $clase, $estructura";
              if ($_POST['srvvacio'] == 1){
                 $campos.=", vacio, id_cliente_vacio, id_estructura_cliente_vacio";
                 $values.=", 1, $_POST[cliente_vacio], $estructura";
              }
              $id_crono = insert('cronogramas', $campos, $values);
              if($id_crono){
                            $campos = "id, id_estructura, id_cronograma, id_estructura_cronograma, hcitacion, hsalida, hllegada, hfinserv, i_v, id_turno, id_estructura_turno, id_TipoServicio, id_estructura_TipoServicio";
                                       ////////////////comienza la carga de los horarios del conograma//////////////
                            $count = count($_POST); // cantidad de argumentos recibidos
                            $count = ($count - 7) / 7; //recupero la cantidad de horarios que se agregaron al formulario
                            for($i = 1; $i <= $count; $i++){//id_cronograma y id_estructura_cronograma son la clave primaria de la tabla Cronogramas
                                   $values = "$estructura, $id_crono, $estructura,'".$_POST['h_cita_'.$i]."','".$_POST['h_salida_'.$i]."','".$_POST['h_llegada_'.$i]."','".$_POST['h_fin_'.$i]."','".$_POST['iv_'.$i]."',".$_POST['turno_'.$i].",$estructura,".$_POST['tipo_'.$i].",$estructura";
                                   insert('servicios', $campos, $values);  //obtiene el ultimo id del servicio ingresado
                            }
              }
              return $id_crono;
  }
?>

