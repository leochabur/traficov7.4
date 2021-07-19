<?php
    @session_start();
    include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadminV2.php');
    
    /*function getOpcion($opcion, $estructura, $conn = 0){
             $value = "";
             if (!$conn)
                $conn = conexcion();
             $sql = "SELECT valor FROM opciones WHERE (opcion = '$opcion') and (id_estructura = $estructura)";
             $result = mysql_query($sql, $conn);
             if ($data = mysql_fetch_array($result)){
                $value = $data['valor'];
             }
             if (!$conn)
                cerrarconexcion($conn);
             return $value;
    }*/
    
   /* function insert($tabla, $campos, $valores, $con=0){
             if ($con)
                $conn = $con;
             else
                 $conn = conexcion();
             if ($conn){
        //     begin($conn);
             $prox = prox($conn, $tabla);
             $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             $ok=0;
             if (!mysql_errno($conn)){
              //  storedSql($sql);
                $ok = mysql_insert_id($conn);
          //      commit($conn);
                if (!$con)
                   cerrarconexcion($conn);
                return $ok;
             }
             else{
                  $ok = 0;
                  $err= mysql_error($conn);
            //      rollback($conn);
                  if (!$con)
                     cerrarconexcion($conn);
                  throw new Exception("$err - $sql");
             }

             }
             else{
                  throw new Exception('Sin conexcion a la Base de Datos!.');
             }
    }*/

    function inserti($tabla, $campos, $valores, $conn=0){
            /* if ($con)
                $conn = $con;
             else
                 $conn = conexcion(true);*/

             $prox = proxi($conn, $tabla);
             $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)"; 
             $result = mysqli_query($conn, $sql);
             $ok=0;
             if (!mysqli_errno($conn))
             {
                $ok = mysqli_insert_id($conn);
                return $ok;
             }
             else{
                  $ok = 0;
                  $err= mysqli_error($conn);
                  throw new Exception("$err - $sql");
             }
    }

    
  /*  function backup($tabla_original, $tabla_respaldo, $condicion, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             if (!$con){
                begin($conn);
             }
             $sql = "INSERT INTO $tabla_respaldo (SELECT * FROM $tabla_original WHERE ($condicion))"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             if (mysql_errno($conn)){
                $error = mysql_error($conn);
                cerrarconexcion($conn);
                throw new Exception($sql.' - '.$error);
             }
             $ok=0;
             if ($result){
                storedSql($sql);
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
             }
             else{
                  $ok = 0;
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
             }
             return $ok;
    }*/
    
    /*function delete($tabla, $campos, $valores, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             if (!$con){
                begin($conn);
             }
             $campos = explode(',', $campos);
             $valores = explode(',', $valores);
             $cond = "";
             for ($i = 0; $i < count($campos); $i++){
                 if ($cond == ""){
                    $cond = "($campos[$i] = $valores[$i])";
                 }
                 else{
                      $cond.= "and ($campos[$i] = $valores[$i])";
                 }
             }

             $sql = "DELETE FROM $tabla WHERE ($cond)";
             $ok=1;
             mysql_query($sql, $conn);

             if (!mysql_errno($conn)){
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
                return $ok;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
                  throw new Exception($sql.' - '.$error);
             }

    }*/
    
 /*   function insertOrReplace($tabla, $campos, $valores, $replace, $id){
             $conn = conexcion();
             begin($conn);
             if (!$id){
                $prox = prox($conn, $tabla);
                $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)";
             }
             else{
                $sql = "INSERT INTO $tabla ($campos) VALUES ($id, $valores) on duplicate key update $replace"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             }
             $result = mysql_query($sql, $conn);
             $ok=0;
             if ($result){
                storedSql($sql);
                $ok = mysql_insert_id($conn);
                commit($conn);
                cerrarconexcion($conn);
             }
             else{
                  $ok = 0;
                  rollback($conn);
                  cerrarconexcion($conn);
             }
             return $ok;
    }*/
    
    /*function update($tabla, $campos, $valores, $condReg, $con = 0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
                  begin($conn);
             }
             $campos = explode(',', $campos);
             $valores = explode(',', $valores);
             $sets = "";
             for ($i = 0; $i < count($campos); $i++){
                 if ($sets == ""){
                    $sets = $campos[$i]."=".$valores[$i];
                 }
                 else{
                      $sets.= ", ".$campos[$i]."=".$valores[$i];
                 }
             }
             
             $sql = "UPDATE $tabla SET $sets WHERE ($condReg)";
             mysql_query($sql, $conn);
             $ok=1;
             if (!mysql_errno($conn)){
                storedSql($sql);
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
                return $ok;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
                  throw new Exception($error.' - '.$sql);
             }
    }*/
    
 /*   function prox($conn, $table){
             $sql = "select AUTO_INCREMENT as prox from information_schema.TABLES where TABLE_SCHEMA='".getBD()."' and TABLE_NAME='$table'";
             $result = mysql_query($sql, $conn) or die(mysql_error($conn)." - ".$sql);
             $data = mysql_fetch_array($result);
             return $data[0];
    }*/

    function proxi($conn, $table){
             $sql = "select AUTO_INCREMENT as prox from information_schema.TABLES where TABLE_SCHEMA='".getBDV2()."' and TABLE_NAME='$table'";
             $result = mysqli_query($conn, $sql) or die(mysqli_error($conn)." - ".$sql);
             $data = mysqli_fetch_array($result);
             return $data[0];
    }    

    function ejecutarSQL($sql, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             $result = mysql_query($sql, $conn);
             if (!mysql_errno($conn)){
                if (!$con){
                   cerrarconexcion($conn);
                }
                return $result;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     cerrarconexcion($conn);
                  }
                  throw new Exception($error." ".$sql);
             }
    }
?>
