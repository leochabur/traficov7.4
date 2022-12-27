<?php
    include "parameter.php";

    if (phpversion() > "7")
    {
        include "functions.php";
    }

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
            try
            {
                $mysqli = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DBNAME);

               // $mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DBNAME);
              //  die('hoa '.$mysqli);
                mysqli_query($mysqli, "SET NAMES 'utf8'");
                return $mysqli;
            }
            catch (Exception $e){ throw $e;}
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



   
   
?>
