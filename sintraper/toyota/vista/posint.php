<?php
     set_time_limit(0);
  $dsn = 'DRIVER={SQL Server};SERVER=190.105.232.111,1433;Database=CONSAT;';
  $con = odbc_connect($dsn,'master','master2013') or die('ODBC Error:: '.odbc_error().' :: '.odbc_errormsg().' :: '.$dsn);



  $int = 1;
  $conn = mysql_connect('export.masterbus.net', 'mbexpuser', 'Mb2013Exp');
  mysql_select_db('mbexport', $conn);
  while(true){
              $result = odbc_exec($con, "select * from movilesMaster where movil = 'int - 0134'");
              if ($data = odbc_fetch_array($result)){
                   $lati=$data['latitud'];
                   $long=$data['longitud'];
              }
              mysql_query("INSERT INTO possatelite (interno, lati, longi, fecha) VALUES ($int, $lati, $long, now())", $conn) or die (mysql_error($conn)."INSERT INTO possatelite (interno, lati, long, fecha) VALUES ($int, $lati, $long, now())");
              sleep(5);
  }
  odbc_close($conn);
?>
