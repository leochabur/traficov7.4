<?

    function master() {
           $con = mysql_connect("rrhh.masterbus.net", "xxmasterbus", "master,07A");
           mysql_select_db("trafico", $con);
           return $con;
   }
   
   function cerrarconexcion($conn) {
           mysql_close($conn);
   }

   function conexcion() {
           $conn = mysql_connect('export.masterbus.net', 'mbexpuser', 'Mb2013Exp');
           mysql_select_db('mbexport', $conn);
           return $conn;
   }
   
   function begin($conn){
           $sql = "SET AUTOCOMMIT=0";
           $resultado = mysql_query($sql, $conn);
           $sql = "BEGIN";
           mysql_query($sql, $conn);
   }
   
   function commit($conn){
            $sql = "COMMIT";
            mysql_query($sql, $conn);
   }

   function rollback($conn){
            $sql = "ROLLBACK";
            mysql_query($sql, $conn);
   }
   
   
   function getBD(){
            return 'mbexport';
   }

   function storedSql($sql){
            $file = fopen("/var/www/export/controlador/tmpsql.log",'a');
            fwrite($file,$sql."\r\n");
            fclose($file);
   }
?>
