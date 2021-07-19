<?php
  session_start();
        if(!$_SESSION["auth"]) header("Location: ./index.php?e=true");
  include('main.php');
  include_once('../controlador/bdadmin.php');
  include_once('../controlador/ejecutar_sql.php');
  include_once('../vista/paneles/viewpanel.php');
?>
<style type="text/css">
thead tr th {padding:20px 10px 20px 10px ; }
tbody tr td {padding:3px 3px 3px 3px ; }
h2 {display:inline}

         .rojo {background-color: #DD614A;}
         .fil1{background-color: #808080;}
         .fil2{background-color: #D0D0D0;}
</style>

    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />

<body vlink=#999999 text-decoration: none; >


<?php
     $inicio = date('d/m/Y');
     $fin = date('d/m/Y', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
  encabezado($_SESSION["chofer"],$_SESSION["nivel"]);

   /*             $conn = conexcion();
        $query = "SELECT upper(mensaje) as mensaje
      FROM mensajes
      WHERE (id_empleado = $_SESSION[id_chofer]) and (vigencia_desde <= date(now())) and (not visto)";
        $result = mysql_query($query, $conn);
        if (mysql_num_rows($result)){
          $mje = "<fieldset class=\"el08\">
            <legend>Mensajes pendientes</legend>";
                while ($data = mysql_fetch_array($result)){
                  $mje.="<div class=\"el08\" align=\"center\">$data[mensaje]<br></div>";
                }
    $mje.="</fieldset>
                </p>";
          print $mje;
           }      */
?>


</head>

<body>

<?php
         menu();
?>
<br><br>
                         <table class="table table-zebra" border="0" align="center" width="50%" name="tabla">
                                <thead>
                                       <tr>
                                           <th>Descripcion</th>
                                           <th>Descarga</th>
                                       </tr>
                                </thead>
                                <tbody>
                                <?php
                                     $result = ejecutarSQL("SELECT upper(texto) as link, n.file FROM normativa n ORDER BY texto");
                                     while ($data = mysql_fetch_array($result)){
                                           print "<tr>
                                                      <td>$data[0]</td>
                                                      <td align='center'><a href='/vista/iso/$data[1]'  target='_blank'><img src='../vista/download.png' width='30' height='30' border='0'></a></td>
                                                 </tr>";
                                     }
                                ?>
                                </tbody>
                         </table>
</body>
</html>











