<?php
    include "parameter.php";
    
    set_time_limit(0);
    function master() {
           $con = mysql_connect("rrhh.masterbus.net", "xxmasterbus", "master,07A");
           mysql_select_db("trafico", $con);
           return $con;
   }
   
   function cerrarconexcion($conn) {
           mysql_close($conn);
   }

   function conexcion($pdo = null) {
         if (false)//!$pdo)
         {
             //@$conn = mysql_connect('localhost', 'root', 'leo1979');
             @$conn = mysql_connect(HOSTNAME,  USERNAME, PASSWORD);
             mysql_query("SET NAMES 'utf8'", $conn);
           mysql_select_db(DBNAME, $conn);
           return $conn;       
         }
         else
         {
            $mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DBNAME);
            mysqli_query($mysqli, "SET NAMES 'utf8'");
            return $mysqli;
         }
   }
   
   function begin($conn){
           $sql = "SET AUTOCOMMIT=0";
           $resultado = mysql_query($sql, $conn);
           $sql = "BEGIN";
           mysql_query($sql, $conn);
   }

   function begini($conn){
           $sql = "SET AUTOCOMMIT=0";
           $resultado = mysqli_query($conn, $sql);
           $sql = "BEGIN";
           mysqli_query($conn, $sql);
   }   
   
   function commit($conn){
            $sql = "COMMIT";
            mysql_query($sql, $conn);
   }

   function rollback($conn){
            $sql = "ROLLBACK";
            mysql_query($sql, $conn);
   }

   function rollbacki($conn){
            $sql = "ROLLBACK";
            mysql_query($conn, $sql);
   }   
   
   
   function getBD(){
          return 'c0mbexport';
          //return 'master';
   }

   function storedSql($sql){
           /* $file = fopen("/var/www/export/controlador/tmpsql.log",'a');
            fwrite($file,$sql."\r\n");
            fclose($file);  */
   }

   ///////////////redefinir conexciones PDO

   function mysql_query($sql, $conn)
   {
        try
        {
            return mysqli_query($conn, $sql);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_num_rows($result)
   {
        try
        {
            return mysqli_num_rows($result);
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_fetch_array($result)
   {
        try
        {
            return $result->fetch_array();
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_close($conn)
   {
        try
        {
            mysqli_close($conn);
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_errno($conn)
   {
        try
        {
            return mysqli_errno($conn);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_free_result($result)
   {
        try
        {
            mysqli_free_result($result);
        }
        catch (Exception $e) { throw $e; }
   }   

   function mysql_insert_id($conn)
   {
        try
        {
            return mysqli_insert_id($conn);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_fetch_row($result)
   {
        try
        {
            return mysqli_fetch_row($result);
        }
        catch (Exception $e) { throw $e; }
   }

   
   
?>
