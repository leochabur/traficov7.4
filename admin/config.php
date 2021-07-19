<?php
// archivo configuracion del sitio
// error config
error_reporting ( E_ALL );
ini_set ( "display_errors", 1 );
ini_set ( "log_errors", 1 );
// ini_set("error_log", "syslog");

// si es localhost esta en desarrollo
$enprod = false;
if (strpos ( $_SERVER ['SERVER_NAME'], 'localhost' ) === false) :
	$enprod = false;
	//ini_set ( "display_errors", 0 );

endif;

// Timezone
date_default_timezone_set ( "America/Argentina/Buenos_Aires" );

// ruta base (depende de si esta o no en produccion)
define ( "RUTA_BASE", ($enprod) ? "/" : "/" );
//ruta admin (depende de si esta o no en produccion)
define ( "RUTA_ADMIN", RUTA_BASE."admin/"  );
define ( "RUTA_CLIENTE", RUTA_BASE."cliente/"  );
// ruta raiz
define ( "RUTA_RAIZ", __DIR__ );
// define("RUTA_RAIZ",$_SERVER['DOCUMENT_ROOT'] );
// ruta subidas
define ( "RUTA_UPLOADS", 'uploads/' );
// ruta librerias
define ( "RUTA_LIB", 'lib/' );

//protocolo
$protocol = empty ( $_SERVER ['HTTPS'] ) ? 'http' : 'https';

// urls sitio
define ( "URL_WEB", "$protocol://" . $_SERVER ['SERVER_NAME'] . RUTA_BASE );
define ( "URL_ADMIN", "$protocol://" . $_SERVER ['SERVER_NAME'] . RUTA_ADMIN );
define ( "URL_CLIENTE", "$protocol://" . $_SERVER ['SERVER_NAME'] . RUTA_CLIENTE);
define ( "URL_INICIO", URL_WEB . "inicio" );
define ( "URL_NOSOTROS", URL_WEB . "laempresa" );
define ( "URL_SERVICIOS", URL_WEB . "servicios" );
define ( "URL_CHARTER", URL_WEB . "charters" );
define ( "URL_TRASLADOS", URL_WEB . "traslados" );
define ( "URL_TURISMO", URL_WEB . "turismo" );
define ( "URL_FLOTAS", URL_WEB . "flotas" );
define ( "URL_CONTACTO", URL_WEB . "contacto" );
define ( "URL_PRESUPUESTO", URL_WEB . "presupuesto" );
define ( "URL_NOVEDADES", URL_WEB . "novedades" );
define ( "URL_404", URL_WEB . "error404" );

// titulo
define ( "WEB_TITULO", 'Empresa de Transporte Santa Rita' );
define ( "WEB_DESCRIP", 'Santa Rita es una Empresa de Transportes de pasajeros. Realizamos viajes privados y públicos. Contamos con unidades de exlusivas. Ofrecemos las mejoras ofertas del mercado y le garantizamos un excelente servicio.' );

$extensionesok =  array('gif','png' ,'jpg', 'jpeg' );
$tiposok = array( "image/jpeg", "image/gif",  "image/png" );


define("PREF_SESS_CLI", "srSesCli");
define("PREF_SESS_USER", "srSesUser");
