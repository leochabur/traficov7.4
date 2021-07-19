<?php

    function master() {
           $con = mysql_connect("rrhh.masterbus.net", "xxmasterbus", "master,07A");
           mysql_select_db("trafico", $con);
           return $con;
   }
   
   function cerrarconexcion($conn) {
           mysql_close($conn);
   }

   function conexcion($pdo = null) {
         if (!$pdo){
             /*$conn = mysql_connect('localhost', 'root', 'leo1979');       
             mysql_query("SET NAMES 'utf8'", $conn);
             mysql_select_db('c0m*bexport', $conn);
             return $conn;*/
           $conn = mysql_connect('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp');
           mysql_query("SET NAMES 'utf8'", $conn);
           mysql_select_db('c0mbexport', $conn);
           return $conn;             
         }
         else{
           // $mysqli = new mysqli('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
            $mysqli = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
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
?>
