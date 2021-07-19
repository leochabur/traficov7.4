<?php

   /* function master() {
           $con = mysql_connect("rrhh.masterbus.net", "xxmasterbus", "master,07A");
           mysql_select_db("trafico", $con);
           return $con;
   }*/
   
   function cerrarconexcionV2($conn) {
           mysql_close($conn);
   }

   function conexcionV2($pdo = null) {
         if (!$pdo){
            /* $conn = mysql_connect('localhost', 'root', 'leo1979');       
             mysql_query("SET NAMES 'utf8'", $conn);
             mysql_select_db('master', $conn);
             return $conn;
             /**/
           $conn = mysql_connect('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp');
           mysql_query("SET NAMES 'utf8'", $conn);
           mysql_select_db('c0mbexport', $conn);
           return $conn;         
         }
         else{
            $mysqli = new mysqli('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
           // $mysqli = new mysqli('127.0.0.1', 'root', 'leo1979', 'master');
            mysqli_query($mysqli, "SET NAMES 'utf8'");
            return $mysqli;
         }
   }
   

   function beginV2($conn){
           $sql = "SET AUTOCOMMIT=0";
           $resultado = mysqli_query($conn, $sql);
           $sql = "BEGIN";
           mysqli_query($conn, $sql);
   }   

   function commitV2($conn){
            $sql = "COMMIT";
            mysqli_query($conn, $sql);
   }   


   function rollbackV2($conn){
            $sql = "ROLLBACK";
            mysqli_query($conn, $sql);
   }   
   
   
   function getBDV2(){
          return 'c0mbexport';
         // return 'master';
   }


?>
