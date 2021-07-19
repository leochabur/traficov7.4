<?
 $link = mssql_connect('190.105.232.111', 'master', 'master2013') or die('error al conectar');

if (!$link) {
    die('Something went wrong while connecting to MSSQL');
}
echo ('ok');
mssql_select_db('CONSAT', $link);

$result = mssql_query ("select * from movilesMaster where movil = 'int - 0208'", $link);

  if ($data = mssql_fetch_array($result)){
                                         print_r($data);
                     $lati=$data['latitud'];
                     $long=$data['longitud'];
  }
  mssql_close($conn);
  print ($lati." --- ".$long);
?>
