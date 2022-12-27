<?php
     session_start();
     include('../controlador/ejecutar_sql.php');

     if (!(isset($_SESSION['isAuth']) && ($_SESSION['isAuth'] == 1)))
     {
         session_destroy();
         header("Location: /");
         exit;
     }

     if (isset($_GET['v'])){
        $_SESSION['auth'] = '1';
        $_SESSION['userid'] = $_GET['t'];
        $_SESSION['structure'] = $_GET['v'];
        $_SESSION['structure_name'] = $_GET['s'];
        $_SESSION['permiso'] = $_GET['pm'];
        $_SESSION['modaf'] = $_GET['ma'];
        $_SESSION['todas'] = '1,0';
        $sql_usuario = ejecutarSQL("SELECT apenom FROM usuarios WHERE id = $_GET[t]");
        if ($row = mysql_fetch_array($sql_usuario)){
                  $_SESSION['apenomUser'] = $row['apenom'];
        }
        $sqlmodulos = "SELECT id_moduloestructura, permiso, uxe.moduloAfectado
                     FROM usuariosxestructuras uxe
                     INNER JOIN usuarioestructuramodulo uem on uem.id_usuarioestructura = uxe.id
                     WHERE (id_usuario = $_SESSION[userid]) and (id_estructura = $_SESSION[structure])";
        $resultmodulos = ejecutarSQL($sqlmodulos);
        while ($row = mysql_fetch_array($resultmodulos)){
              $_SESSION['permisos'][$row['id_moduloestructura']] = $row['permiso'];
        }
        mysql_free_result($resultmodulos);
        $campos = "id, id_estructura, id_usuario, hinicio, hfin, host, estructura";
        $values = "$_GET[v], $_GET[t], now(), now(), '$_SERVER[REMOTE_ADDR]', $_GET[v]";
        $_SESSION['accid']=insert("accesousuarios", $campos, $values);
     }
     else{
          if (!$_SESSION['auth']){
             session_destroy();
             header("Location: /");
             exit;
          }
     }
     include("main.php");
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>
<style>
  body { min-width: 520px; }
  .column { width: 570px; float: left; padding-bottom: 100px; }
  .portlet { margin: 0 1em 1em 0; }
  .portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
  .portlet-header .ui-icon { float: right; }
  .portlet-content { padding: 0.4em; }
  .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
  .ui-sortable-placeholder * { visibility: hidden; }
  .texto {
    font-size: 0.775em;
}
  </style>
  <script>
  $(function() {
    $( ".column" ).sortable({
      connectWith: ".column"
    });

    $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
        .end()
      .find( ".portlet-content" );

    $( ".portlet-header .ui-icon" ).click(function() {
      $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
      $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
    });

    $( ".column" ).disableSelection();
  });
  </script>
<body>
<?php
     menu();
?>
<br>
<div class="column">

  <div class="portlet">
    <div class="portlet-header">Proximos vencimientos de Habilitaciones</div>
    <div class="portlet-content texto"><?php
                                      include('informes/segvial/proxvtos.php');
                                      ?>
    </div>
  </div>

  </div>
  <div class="column">


  <div class="portlet">
    <div class="portlet-header">Licencias a vencer los proximos 60 dias</div>
    <div class="portlet-content texto"><?php
                                      include('informes/rrhh/proxvtoscond.php');
                                      ?></div>
  </div>



  <div class="portlet">
    <div class="portlet-header">Libretas de Trabajo a vencer los proximos 30 dias</div>
    <div class="portlet-content texto"><?php
                                      include('informes/rrhh/proxvtoslib.php');
                                      ?></div>
  </div>

</div>
</body>
</html>
