<?php
    session_start();
    include_once('./bdadmin.php');
    
    function getOpcion($opcion, $estructura){
             $value = "";
             $conn = conexcion();
             $sql = "SELECT valor FROM opciones WHERE (opcion = '$opcion') and (id_estructura = $estructura)";
             $result = mysql_query($sql, $conn);
             if ($data = mysql_fetch_array($result)){
                $value = $data['valor'];
             }
             cerrarconexcion($conn);
             return $value;
    }
    
    function insert($tabla, $campos, $valores){
             $conn = conexcion();
             begin($conn);
             $prox = prox($conn, $tabla);
             $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             $ok=0;
             if ($result){
              //  storedSql($sql);
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
    }
    
    function backup($tabla_original, $tabla_respaldo, $condicion){
             $conn = conexcion();
             begin($conn);
             $sql = "INSERT INTO $tabla_respaldo (SELECT * FROM $tabla_original WHERE ($condicion))"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             $ok=0;
             if ($result){
                storedSql($sql);
                commit($conn);
                cerrarconexcion($conn);
             }
             else{
                  $ok = 0;
                  rollback($conn);
                  cerrarconexcion($conn);
             }
             return $ok;
    }
    
    function delete($tabla, $campos, $valores){
             $conn = conexcion();
             begin($conn);
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
             $result = mysql_query($sql, $conn);
             $ok=1;
             if ($result){
                storedSql($sql);
                commit($conn);
                cerrarconexcion($conn);
             }
             else{
                  $ok = 0;
                  rollback($conn);
                  cerrarconexcion($conn);
             }
             return $ok;
    }
    
    function insertOrReplace($tabla, $campos, $valores, $replace, $id){
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
    }
    
    function update($tabla, $campos, $valores, $condReg){
             $conn = conexcion();
             begin($conn);
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
             $result = mysql_query($sql, $conn);
             $ok=1;
             if ($result){
                storedSql($sql);
                commit($conn);
                cerrarconexcion($conn);
             }
             else{
                  $ok = 0;
                  rollback($conn);
                  cerrarconexcion($conn);
             }
             return $ok;
    }
    
    function prox($conn, $table){
             $sql = "select AUTO_INCREMENT as prox from information_schema.TABLES where TABLE_SCHEMA='".getBD()."' and TABLE_NAME='$table'";
             $result = mysql_query($sql, $conn) or die($sql);
             $data = mysql_fetch_array($result);
             return $data[0];
    }

    function ejecutarSQL($sql){
             $conn = conexcion();
             $result = mysql_query($sql);
             cerrarconexcion($conn);
             return $result;
    }
?>
