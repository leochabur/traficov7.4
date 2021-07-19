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
           $conn = mysql_connect('127.0.0.1', 'brandsen_export', 'Febr2016');
          // $conn = mysql_connect('127.0.0.1', 'root', 'leo1979');
           //mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $conn);
           mysql_query("SET NAMES 'utf8'", $conn);
           mysql_select_db('brandsen_export', $conn);
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
            return 'brandsen_export';
   }

   function storedSql($sql){
           /* $file = fopen("/var/www/export/controlador/tmpsql.log",'a');
            fwrite($file,$sql."\r\n");
            fclose($file);  */
   }
?>
