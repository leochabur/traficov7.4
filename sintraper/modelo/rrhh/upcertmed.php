<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include_once('../../controlador/ejecutar_sql.php');
     include_once ('../../modelo/utils/dateutils.php');
     
     $accion = $_POST['accion'];
     if ($accion == 'med'){
        $conn = conexcion();
        $campos = "id, apellido, nombre";
        $valores = "'$_POST[apellido]', '$_POST[nombre]'";
        $id = insert("medicos", $campos, $valores);
        $select="<option value='$id' selected>$_POST[apellido], $_POST[nombre]</option>";
        mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'espec'){
        $conn = conexcion();
        $campos = "id, especialidad";
        $valores = "'$_POST[nombre]'";
        $id = insert("especialidades", $campos, $valores);
        $select="<option value='$id' selected>$_POST[nombre]</option>";
        mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'ctroAsis'){
        $conn = conexcion();
        $campos = "id, nombre";
        $valores = "'$_POST[nombre]'";
        $id = insert("ctrosasistenciales", $campos, $valores);
        $select="<option value='$id' selected>$_POST[nombre]</option>";
        mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'diagnostic'){
        $conn = conexcion();
        $campos = "id, diagnostico";
        $valores = "'$_POST[nombre]'";
        $id = insert("diagnosticos", $campos, $valores);
        $select="<option value='$id' selected>$_POST[nombre]</option>";
        mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'loadNov'){
        $conn = conexcion();
        $nombre = $_POST['nombre'];
        $query = "SELECT n.id, concat(cn.nov_text,' - ', DATE_FORMAT(n.desde, '%d/%m/%Y'), ' - ',DATE_FORMAT(n.hasta, '%d/%m/%Y')) as novedad
                  FROM novedades n
                  inner join cod_novedades cn on cn.id = n.id_novedad
                  where id_empleado = $nombre
                  order by hasta DESC";
        $result = mysql_query($query, $conn);
        $select = "<select id='selNov' name='selNov'>";
        while ($data = mysql_fetch_array($result)){
           $select.="<option value=\"$data[id]\" $sel>$data[novedad]</option>";
        }
        $select.="</select>";
        mysql_free_result($result);
        mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'loadCert'){
        $nombre = $_POST['nombre'];
        $query = "SELECT cm.id, concat(upper(d.diagnostico), '   ',DATE_FORMAT(fecha_cert, '%d/%m/%Y'),' - ', DATE_FORMAT(vigente_hasta, '%d/%m/%Y')) as certificado
                  FROM certMedicos cm
                  inner join diagnosticos d on d.id = cm.id_diagnostico
                  where id_empleado = $nombre
                  order by fecha_cert";
        $result=mysql_query($query, $conn);
        $select = "<select id=\"selCert\" name=\"selCert\">";
        $select.= "<option value=\"0\">Seleccione un Certificado de la lista</option>";
        while ($data = mysql_fetch_array($result)){
           $select.="<option value=\"$data[id]\" $sel>$data[certificado]</option>";
        }
        $select.="</select>";
        @mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'loademp'){
        $conn = conexcion();
        $sql="SELECT id_empleado, concat(apellido,', ', nombre) as apenom
              FROM empleados
              WHERE (id_empleador = $_POST[emplor]) and (activo) and (id_estructura in (SELECT uxe.id_estructura
                                                    FROM usuarios u
                                                    inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                    where u.id = $_SESSION[userid]))
              ORDER BY apellido, nombre";
        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla= '';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<option value='$data[id_empleado]'>".htmlentities($data['apenom'])."</option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        print $tabla;
     }
     elseif($accion == 'cargarCert'){
        $query="SELECT cm.id, CONCAT('Fecha: ',date_format(fecha_cert, '%d/%m/%Y'), ' Vigente hasta: ',date_format(vigente_hasta, '%d/%m/%Y'),'. Medico: ', UPPER(CONCAT(m.apellido,', ',m.nombre)), ' Diagnostico: ', UPPER(d.diagnostico)) as certificado
                            FROM (SELECT id, id_medico, id_diagnostico, fecha_cert, vigente_hasta FROM certMedicos WHERE (id_empleado = $_POST[emple]) and (activo)) cm
                            inner join medicos m on m.id = cm.id_medico
                            inner join diagnosticos d on d.id = cm.id_diagnostico
                            ORDER BY fecha_cert";

        $result = mysql_query($query, $conn);
        $select = "<select id=\"selCert\" name=\"selCert\">";
        while ($data = mysql_fetch_array($result)){
           $select.="<option value=\"$data[id]\">$data[certificado]</option>";
        }
        $select.="</select>";
        @mysql_close($conn);
        print $select;
      }
      elseif($accion == 'borrarCert'){
        $query="UPDATE certMedicos set activo = 0 WHERE (id = $_POST[cert])";
        mysql_query($query, $conn);
        if (mysql_errno($conn)){
           $text = "<font color=\"#FF0000\"><b>No se pudo eliminar el certificado</b></font>";
        }
        else{
           $text = "<font color=\"#FF0000\"><b>Certificado eliminado con exito</b></font>";
        }
        @mysql_close($conn);
        print $text;
      }
      elseif($accion == 'svecert'){

        $desde = dateToMysql($_POST['desde'],'/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        $medico=$_POST['selMed'];
        $ctroas=$_POST['selCtro'];
        $espec=$_POST['selEsp'];
        $diagn=$_POST['selDiag'];
        $emple=$_POST['selCond'];
        $noved=$_POST['selNov'];
        $obser=$_POST['obs'];
        
        $sql="select id_estructura FROM empleados where id_empleado = $emple"; //consulta para obtener la est a la cual esta afectado el conductor y asi asignarle la misma al crtificado
        $result = ejecutarSQL($sql);
        if ($row = mysql_fetch_array($result)){
           $estruc = $row['id_estructura'];
           mysql_free_result($result);
        }

        $campos = "id, id_medico, id_ctroAsis, id_especialidad, id_diagnostico, id_empleado, id_novedad, fecha_cert, vigente_hasta, observaciones, fecha_alta_sistema, usuario_alta, activo, id_estructura";
        $values = "$medico, $ctroas, $espec, $diagn, $emple, $noved, '$desde', '$hasta', '$obser', now(), $_SESSION[userid], 1, $estruc";
        print insert("certmedicos", $campos, $values);
      }
?>
