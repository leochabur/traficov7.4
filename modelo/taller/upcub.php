<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include_once('../../controlador/ejecutar_sql.php');
     include_once ('../../modelo/utils/dateutils.php');
     
     $accion = $_POST['accion'];
     if ($accion == 'med'){
        //$conn = conexcion();
        $campos = "id, marca";
        $valores = "'$_POST[marca]'";
        $id = insert("marcaCubierta", $campos, $valores);
        $select="<option value='$id' selected>".strtoupper($_POST['marca'])."</option>";
        //mysql_close($conn);
        print $select;
     }
     elseif ($accion == 'medida'){
        //$conn = conexcion();
        $campos = "id, medida";
        $valores = "'$_POST[nombre]'";
        $id = insert("medidasCubiertas", $campos, $valores);
        $select="<option value='$id' selected>".strtoupper($_POST['nombre'])."</option>";
        //mysql_close($conn);
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
     elseif ($accion == 'deposito'){
      //  $conn = conexcion();
        $campos = "id, nombre";
        $valores = "'$_POST[nombre]'";
        $id = insert("depositos", $campos, $valores);
        $select="<option value='$id' selected>".strtoupper($_POST['nombre'])."</option>";
        //mysql_close($conn);
        print $select;
     }
      elseif($accion == 'svecub'){
        $fechac ="";
        if ($_POST['fechac'])
           $fechac = dateToMysql($_POST['fechac'],'/');

        $marca=$_POST['selMar'];
        $medida=$_POST['medida'];
        $estado=$_POST['estadoC'];
        $deposito=$_POST['depositoC'];
        
        $sql="select * FROM cubiertas where codigo = '$_POST[codigo]'"; //consulta para obtener la est a la cual esta afectado el conductor y asi asignarle la misma al crtificado
       // die($sql);
        $result = ejecutarSQL($sql);
       // die("num fields: ".mysql_num_fields($result));
        if ($row = mysql_fetch_array($result)){
           die(json_encode("-1"));
        }

        $campos = "id, codigo, id_marca, id_estado, fecha_compra, fecha_alta_sistema, id_medida, activo, cant_recapado, id_deposito";
        $values = "$_POST[codigo], $marca, $estado, '$fechac', now(), $medida, 1, 0, $deposito";
        print insert("cubiertas", $campos, $values);
      }
?>
