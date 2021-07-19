<?php 
  @session_start();
if ($_SESSION['userid'] == 17)
{
  error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
}

   include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
      include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');

   if (!$_SESSION['auth']){
      session_destroy();
      print('<b><p align="center">Su sesion ha expirado!</p></b><meta http-equiv="Refresh" content="2;url=/">');
      exit;
   }
     $res_cc = ejecutarSQL("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]");
     if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
     }

  define('RAIZ', '');

                                                    /*     $.notty({
                                                                title : "Notificaciones RRHH",
                                                                content : "Ver novedades"
                                                                });
                                                       $.notty({
                                                                title : "Notificaciones Seg Vial",
                                                                content : "Ver novedades"
                                                                });*/
  function getNovRRHH(){
           $sinconductor = '';
           $scriptsincond = '';
           $trafico = '';
           if ($_SESSION['modaf'] == 1){ //el usuario es operador del modulo de RRHH por lo tanto solo recibe notificaciones de RRHH
              $sqlnoproce = "SELECT e.id_empleado, legajo, upper(concat(apellido, ', ',nombre)) as apenom, nrodoc, upper(razon_social) as empleador
                             FROM empleados e
                             left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
                             where (e.activo) and (not e.procesado)
                             order by empleador";
              $result = ejecutarSQL($sqlnoproce);
              $noproc = "";
              $scriptnoproc = "";
              $ok = false;
              if (mysql_num_rows($result) > 0){
                 $sinconductor='<a href="index.php" id="newcond"><font color="#FF0000">Nuevos Conductores dados de Alta</font></a>';
                 $scriptsincond='$("#newcond").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/rrhh.php", {accion: "lintsvto"}, function(data){
                                                                                                                             $("#orders").html(data);
                                                                                                                             $("#orders").dialog( "open" );
                                                                                                                             });
                                                           });';
                 mysql_free_result($result);
                 $ok = true;
              }
              $sql="SELECT e.id_empleado
                           FROM empleados e
                           left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
                           where (id_cargo = 1) and (e.activo) and (not borrado) and (em.activo) and (id_empleado not in (SELECT id_conductor FROM licenciasxconductor where (id_conductor = id_empleado) and (id_licencia not in (4, 5))))";
              $result = ejecutarSQL($sql);

              if (mysql_num_rows($result) > 0){
                 $sinconductor.='<a href="index.php" id="csva"><font color="#FF0000">Conductores sin Vencimientos Asignados</font></a>';
                 $scriptsincond.='$("#csva").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/rrhh.php", {accion: "consva"}, function(data){
                                                                                                                             $("#orders").html(data);
                                                                                                                             $("#orders").dialog( "open" );
                                                                                                                             });
                                                           });';
                 mysql_free_result($result);
                 $ok = true;
              }
              
              if ($ok){
                /* $trafico = "$.notty({
                                      title : 'Novedades RRHH',
                                      content : '$sinconductor'
                                     });
                                     $scriptsincond
                                  ";          */
              }
           }
           return $trafico;
  }

  function getNovSegVial(){
          $trafico = '';
           if ($_SESSION['modaf'] == 4){ //el usuario es operador del modulo de Seg Vial por lo tanto solo recibe notificaciones de Seg Vial
              $sqldefault = "SELECT id FROM unidades u where (u.id not in (SELECT idunidad FROM tipovencimientoporinterno group by idunidad)) and (activo) and (id_propietario = 1)";
              $resultdefault = ejecutarSQL($sqldefault);
              $row = mysql_fetch_array($resultdefault);
              $sinconductor = "";
              $scriptsincond = "";
              $ok = false;
              if (mysql_num_rows($resultdefault) > 0){
                 $sinconductor='<a href="index.php" id="insvto"><font color="#FF0000">Internos sin Vencimientos Asignados</font></a>';
                 $scriptsincond='$("#insvto").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/segvial.php", {accion: "lintsvto"}, function(data){
                                                                                                                             $("#orders").html(data);
                                                                                                                             $("#orders").dialog( "open" );
                                                                                                                             });
                                                           });';
                 mysql_free_result($resultdefault);
                 $ok = true;
              }
              $sql="SELECT id FROM unidades where (not procesado) and (activo) and (id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid]))";
              $resultdefault = ejecutarSQL($sqldefault);
              $row = mysql_fetch_array($resultdefault);

              if (mysql_num_rows($resultdefault) > 0){
                 $sinconductor.='<br><a href="index.php" id="insvto"><font color="#FF0000">Internos Pendientes a Confirmar</font></a>';
                 $scriptsincond.='$("#insvto").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/segvial.php", {accion: "lintsvto"}, function(data){
                                                                                                                             $("#orders").html(data);
                                                                                                                             $("#orders").dialog( "open" );
                                                                                                                             });
                                                           });';
                 mysql_free_result($resultdefault);
                 $ok = true;
              }
              if ($ok){
                 /*$trafico = "$.notty({
                                      title : 'Novedades Seg. Vial',
                                      content : '$sinconductor'
                                     });
                                     $scriptsincond
                                  ";    */

           }
           }
           return $trafico;
  }
  function getNovTraf(){
           //$sqldefault = "SELECT moduloAfectado FROM usuariosxestructuras u WHERE id_usuario = $_SESSION[userid] and id_estructura = $_SESSION[structure]";
          // $resultdefault = ejecutarSQL($sqldefault);
          // $row = mysql_fetch_array($resultdefault);
      $trafico = '';
           if ($_SESSION['modaf'] == 2){ //el usuario es operador del modulo de trafico por lo tanto solo recibe notificaciones de trafico
              $sql = "SELECT *
                      FROM ordenes o
                      where (id_chofer_1 is null) and (time(now()) <= hcitacion) and (date(now()) = fservicio) and (id_estructura = $_SESSION[structure])";
              $sinconductor = "";
              $scriptsincond = "";
              $ok = false;
              $result = ejecutarSQL($sql);
              if (mysql_num_rows($result) > 0){
                 $ok = true;
                 $sinconductor='<a href="index.php" id="osca"><font color="#FF0000">Ordenes sin conductor</font></a>';
                 $scriptsincond='$("#osca").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/main.php", {accion: "losca"}, function(data){
                                                                                                                             $("#orders").html(data);
                                                                                                                             $("#orders").dialog( "open" );
                                                                                                                             });
                                                           });';
                 mysql_free_result($result);
              }
              $sql = "SELECT *
                      FROM ordenes o
                      where (id_estructura = $_SESSION[structure]) and (vacio) and (id_cliente_vacio is null) and (id_estructura_cliente_vacio is null) and (fservicio = date(now()))";
              $result = ejecutarSQL($sql);
              if (mysql_num_rows($result) > 0){
                 $ok = true;
                 $sinconductor.='<br><a href="index.php" id="vsca"><font color="#FF0000">Vacios no asignados</font></a>';
                 $scriptsincond.='$("#vsca").click(function(event){
                                                           event.preventDefault();
                                                           $.post("/modelo/main/vacnoasig.php", {accion: "loscva"}, function(data){
                                                                                                                                     $("#orders").html(data);
                                                                                                                                     $("#orders").dialog( "open" );
                                                                                                                                     });
                                                           });';
              }
              if ($ok){
                /* $trafico = "$.notty({
                                      title : 'Novedades TRAFICO',
                                      content : '$sinconductor',
                                      timeout: 15000
                                     });
                                     $scriptsincond
                                  ";       */
              }
           }
           return $trafico;
  }
  
  function encabezado($titulo, $old = 0){
  //<link rel="stylesheet" type="text/css" href="'.RAIZ.'/vista/css/jquery.notty.css" />
           if ($old){
                 $jqueryui = "http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js";
           }
           else{
                $jqueryui = RAIZ.'/vista/js/jquery-ui-1.8.22.custom.min.js';
           }
           print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
                  <html>
                  <head>
                        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
                        <title>'.$titulo.'</title>
                        <link type="text/css" href="'.RAIZ.'/vista/css/menu.css" rel="stylesheet" />
                        <link type="text/css" href="'.RAIZ.'/vista/css/blitzer/jquery-ui-1.8.22.custom.css" rel="stylesheet" />

                        <script type="text/javascript" src="'.RAIZ.'/vista/js/jquery-1.7.2.min.js"></script>
                        <script type="text/javascript" src="'.$jqueryui.'"></script>
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/menu.js"></script>
                        <script type="text/javascript" src="'.RAIZ.'/vista/js/jquery.notty.js"></script>
                          <link type="text/css" href="'.RAIZ.'/vista/css/blue/style.css" rel="stylesheet"/>

                  </head>
                  <script type="text/javascript">
                          var fechaActual = new Date();
                          var dia = fechaActual.getDate();7.
                          var mes = fechaActual.getMonth() +1;
                          var anno = fechaActual.getFullYear();
                          if (dia <10) dia = "0" + dia;
                          if (mes <10) mes = "0" + mes;
                          var fechaHoy = dia + "/" + mes + "/" + anno;

                          $(document).ready(function(){
                                                       $("form").attr("autocomplete", "off");
                                                       $("#orders").dialog({autoOpen: false, width: 650, modal: true});
                                                       '.getNovTraf().getNovSegVial().getNovRRHH().'
                                                       muestraReloj();
                                                       $("body div:last").remove();

                                                       });

                          function muestraReloj(){
                                   if (!document.layers && !document.all && !document.getElementById) return;
                                   var fechacompleta = new Date();
                                   var horas = fechacompleta.getHours();
                                   var minutos = fechacompleta.getMinutes();
                                   var segundos = fechacompleta.getSeconds();
                                   if (minutos <= 9)
                                      minutos = "0" + minutos;
                                   if (segundos <= 9)
                                      segundos = "0" + segundos;
                                   horario = horas + ":" + minutos+":"+segundos;
                                   $("#reloj").html(fechaHoy+"  -  "+horario);
                                   setTimeout("muestraReloj()", 1000);
                          }

                  </script>
                  <style type="text/css">
                         body { background:#ededed; }
                         body { font-size: 82.5%; }
                  </style>
                  <div style="visibility: hidden"> <a href="http://apycom.com/">Apycom jQuery Menus</a></div>
                  <div id="orders"></div>';
  }
  
  function menuSegVial(){
           $menu='';
           if ((array_key_exists(4, $_SESSION['permisos'])) || (array_key_exists(2, $_SESSION['permisos']))){

                   $menu.='<li class="last"><a href="#"><span>Seguridad Vial</span></a>
                           <div>
                                <ul>
                                  <li>
                                    <a href="#" class="parent"><span>Aula Virtual</span></a>
                                    <div><ul>
                                        <li><a href="'.RAIZ.'/vista/segvial/adadav.php"><span>Ver Avances</span></a></li>
                                        <li><a href="'.RAIZ.'/vista/segvial/readadav.php"><span>Actualizar</span></a></li>
                                        <li><a href="'.RAIZ.'/vista/segvial/adminav.php"><span>Configurar</span></a></li>
                                        </ul>
                                        </div>
                                    </li>';
                   if (($_SESSION['permisos'][4] > 1) || ($_SESSION['permisos'][2] > -1)){
                   
                              $menu.='<li><a href="#" class="parent"><span>Siniestros</span></a>
                                    <div><ul>';
                                    if ($_SESSION['permisos'][4] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/upsnt.php"><span>Ingresar siniestro</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/reasnt.php"><span>Ver siniestros generados</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/gnrinf.php"><span>Informe Siniestral</span></a></li>';
                                    }

                   $menu.='</ul></div>
                           </li>';
                   if ($_SESSION['permisos'][4] > 1){
                   $menu.='<li><a href="#" class="parent"><span>Hechos Vandalicos</span></a>
                                    <div><ul>';

                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/upheva.php"><span>Ingresar Hechos</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/readhv.php"><span>Ver hechos generados</span></a></li>';


                   $menu.='</ul></div>
                           </li>';
                   }
                   $menu.='<li><a href="#" class="parent"><span>Infracciones</span></a>
                                    <div><ul>';
                                    if ($_SESSION['permisos'][4] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/upinf.php"><span>Ingresar Infraccion</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/readinf.php"><span>Ver infracciones generadas</span></a></li>';
                                    }

                   $menu.='</ul></div>
                           </li>';

                   
                   
                   
                   
                              $menu.='<li><a href="#" class="parent"><span>Unidades</span></a>
                                    <div><ul>';
                                    if ($_SESSION['permisos'][4] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/altauda.php"><span>Alta</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/moduda.php"><span>Baja/Modificaciones</span></a></li>';
                                    }
                                    if (($_SESSION['permisos'][4] > 0) || ($_SESSION['permisos'][2] > -1)){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/listuda.php"><span>Listado</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/taller/gsttcf.php"><span>Gestionar Tacografos</span></a></li>   ';
                                    }
                                    if ($_SESSION['permisos'][2] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/chudast.php" class=""><span>Cambiar Interno de Estructura</span></a></li>';
                                    }

                   $menu.='</ul></div>
                           </li>';
                   }
        $menu.='<li><a href="#" class="parent"><span>Vencimientos</span></a>
                    <div>
                         <ul>
                         <li><a href="#" class="parent"><span>VTV</span></a>
                             <div>
                                <ul>';

                               if ($_SESSION['permisos'][4] > 1){
                                  $menu.='<li><a href="'.RAIZ.'/vista/segvial/upvtouda.php"><span>Ingresar VTV</span></a></li>';
                                  $menu.='<li><a href="'.RAIZ.'/vista/segvial/vtoxint.php"><span>Configurar VTV Interno</span></a></li>';
                               }
                                  $menu.='<li><a href="'.RAIZ.'/vista/segvial/vtouda.php"><span>Ver Vencimientos</span></a></li>
                               </ul>
                             </div>
                         <li><a href="#" class="parent"><span>Seguros</span></a>
                             <div>
                                <ul>';

                               if ($_SESSION['permisos'][4] > 1){
                                  $menu.='<li><a href="'.RAIZ.'/vista/segvial/upvtocauda.php"><span>Ingresar Poliza Seguro</span></a></li>';
                               }
                                  $menu.='<li><a href="'.RAIZ.'/vista/segvial/vtocauda.php"><span>Ver Vencimientos</span></a></li>

                               </ul>
                             </div>
                         </ul>
                    </div>
                </li>';
                if ($_SESSION['permisos'][4] > 1){
                $menu.='<li><a href="#" class="parent"><span>Peajes</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/vista/segvial/altaestpea.php"><span>Ingresar Estacion Peaje</span></a></li>
                        <li><a href="'.RAIZ.'/vista/segvial/altapricepeaj.php"><span>Ingresar Precio Peaje</span></a></li>
                    </ul></div>
                </li>';
                }

        if ($_SESSION['permisos'][4] >= 0){
           $menu.='<li><a href="#" class="parent"><span>Admin. Taller</span></a>
                    <div>
                         <ul>';
           $confiLavadero = '';
           if ($_SESSION['permisos'][4] > 2){
                 $menu.='<li><a href="'.RAIZ.'/vista/taller/elrstc.php"><span>Iniciar desvio de compras</span></a></li>';
                 $confiLavadero = '<li><a href="'.RAIZ.'/vista/taller/settime.php"><span>Configurar tiempos de Lavado</span></a></li> 
                                   <li><a href="'.RAIZ.'/vista/taller/lvdoedlc.php"><span>Cargar Lavados Edilicios</span></a></li>';
           }
           if ($_SESSION['permisos'][4] >= 1){
             $menu.='<li><a a href="#" class="parent"><span>Lavadero</span></a>
                        <div>
                            <ul>
                                <li><a href="'.RAIZ.'/vista/taller/gestionlavados.php"><span>Cargar Lavados</span></a></li> 
                                <li><a href="'.RAIZ.'/vista/informes/cria/rsmlvd.php"><span>Informes Lavados</span></a></li>
                                '.$confiLavadero.'
                            </ul>
                        </div>
                              
                    </li>
                     
                     <li><a href="'.RAIZ.'/vista/taller/gstcgl.php"><span>Registrar Despacho Gasoil</span></a></li>
                     <li><a href="'.RAIZ.'/vista/taller/ingsgl.php"><span>Registrar Ingreso Gasoil</span></a></li>    
                     <li><a a href="#" class="parent"><span>Informes</span></a>
                        <div>
                            <ul>
                                <li><a href="'.RAIZ.'/vista/informes/cria/rsmrgfl.php"><span>Informe cargas combustible</span></a></li> 
                                <li><a href="'.RAIZ.'/vista/taller/printpl.php"><span>Imprimir Planilla Carga</span></a></li> 
                            </ul>
                        </div>
                     </li>
                     <li><a href="'.RAIZ.'/vista/taller/state.php"><span>Ver Km disponibles</span></a></li>                     
                     <li><a href="'.RAIZ.'/vista/taller/setque.php"><span>Configurar Parametros Unidades</span></a></li>';
           }
           $menu.='<li>
                         <a href="'.RAIZ.'/vista/taller/anomalias.php" class="parent"><span>Cargar anomalias</span></a>
                         <div>
                              <ul>
                                  <li><a href="'.RAIZ.'/vista/taller/reanom.php"><span>Ver anomalias</span></a></li>
                                  <li><a href="'.RAIZ.'/vista/taller/anomalias.php"><span>Cargar anomalias</span></a></li>
                              </ul>
                         </div>
                     </li>';
        $menu.='</ul>
        </div>
        </li>';
        }
         $menu.='</ul></div></li>';
           }
        return $menu;
  }
  
  function menuTrafico(){
           $ordenes = "ordenes";
           global $cantTripulacion;
           $mdpxs="modpxhs";
           $diagsrv = "modordres";
           if ($_SESSION['structure'] == 3)
              $mdpxs="modpxhsgls";
           if ($cantTripulacion > 2){
              $ordenes = "ordenessur";
              $diagsrv = "moordresur";
           }
           $menu='';
           if (array_key_exists(2, $_SESSION['permisos'])){
              $menu.='<li>
                          <a href="#" class="parent"><span>Ordenes de Trabajo</span></a>
                          <div>
                               <ul>
                                   <li><a href="'.RAIZ.'/vista/ordenes/'.$ordenes.'.php" class=""><span>Ver Ordenes de Trabajo</span></a></li>
                                   <li><a href="'.RAIZ.'/vista/ordenes/'.$mdpxs.'.php" class=""><span>Cargar Pasajeros</span></a></li>
                                  <li><a href="'.RAIZ.'/vista/ordenes/srvchrmv.php" class=""><span>Administrar ordenes Charter</span></a></li>';
              if ($_SESSION['permisos'][2] > 1){
                  $route = ($cantTripulacion > 2?'cpydiagramasur':'cpydiagrama');
                                          $menu.='<li><a href="'.RAIZ.'/vista/ordenes/'.$route.'.php" class=""><span>Copiar Diagrama</span></a></li>
                                          <li><a href="'.RAIZ.'/vista/ordenes/diagsrv.php" class=""><span>Diagramar Servicios</span></a></li>
                                          <li><a href="'.RAIZ.'/vista/ordenes/asscndord.php" class=""><span>Asignar Conductores</span></a></li>';
              }
                                          $menu.='<li><a href="#" class="parent"><span>Crear Nueva Orden</span></a>
                                             <div>
                                                  <ul>
                                                      <li><a href="'.RAIZ.'/vista/ordenes/ordenesn.php"><span>Crear Orden Eventual</span></a></li>
                                                      <li><a href="'.RAIZ.'/vista/ordenes/ordenecn.php"><span>Diagramar Servicio Existente</span></a></li>
                                                  </ul>
                                             </div>
                                          </li>';
              if ($_SESSION['permisos'][2] > 1){
                                          $menu.='<li><a href="#" class="parent"><span>Crear Vacio</span></a>
                                                 <div>
                                                      <ul>
                                                          <li><a href="'.RAIZ.'/vista/ordenes/ordenesempty.php"><span>Crear Vacio Eventual</span></a></li>
                                                      </ul>
                                                 </div>
                                          </li>
                                          <li><a href="'.RAIZ.'/vista/ordenes/'.$diagsrv.'.php"><span>Modificar Diagrama</span></a></li>';
              }

              //die(print_r($_SESSION['permisos']));
        if (($_SESSION['modaf'] == 2) && ($_SESSION['permisos'][2] >= 1)){
         // die('zoquetep]o');
          /*                                                          <li><a href="'.RAIZ.'/vista/ordenes/smlvac.php"><span>Simular Vacios</span></a></li>
                                                          <li><a href="'.RAIZ.'/vista/ordenes/modkmtpo.php"><span>Modificar Parametros</span></a></li>
                                                          <li><a href="'.RAIZ.'/vista/ordenes/smlvacv2.php"><span>Simular Vacios V 2.0</span></a></li>   */
          $menu.= '<li><a href="'.RAIZ.'/vista/ordenes/vrfdg.php"><span>Verificar Diagrama</span></a></li>';
        }
        if ($_SESSION['permisos'][2] > 1){
           $menu.='<li><a href="'.RAIZ.'/vista/ordenes/modstdg.php"><span>Mod. Estado Diagrama</span></a></li>';
           $menu.='<li><a href="#" class="parent"><span>Simulacion Vacios</span></a>
                                                 <div>
                                                      <ul>
 
                                                          <li><a href="'.RAIZ.'/vista/ordenes/smlvacv3.php"><span>Simular Vacios</span></a></li>                                                         
                                                      </ul>
                                                 </div>
                                          </li>';
        }
        if (($_SESSION['structure'] == 3) || ($_SESSION['structure'] == 4)){
           $menu.='<li><a href="'.RAIZ.'/vista/ordenes/closeordres.php"><span>Cerrar ordenes en bloque</span></a></li>
                   <li><a href="'.RAIZ.'/vista/ordenes/chords.php"><span>Reasignar Conductor/Interno</span></a></li>';
        }
        $menu.='</ul>
            </div>
        </li>';



        if ($_SESSION['permisos'][2] >= 1){
         $menu.='<li class="last"><a href="#"><span>Servicios</span></a>
                    <div>
                    <ul>
                    <li><a href="'.RAIZ.'/vista/ordenes/fdodef.php"><span>Definir feriados</span></a></li>
                    <li><a href="'.RAIZ.'/vista/ordenes/serpre/srvup.php"><span>Crear Servicio</span></a></li>
                    <li><a href="'.RAIZ.'/vista/servicios/ctrldiag.php"><span>Diagramas Base</span></a></li>
                    <li><a href="'.RAIZ.'/vista/rest/rescli.php"><span>Cargar Restricciones Clientes</span></a></li>
                    <li><a href="'.RAIZ.'/vista/servicios/agsrv.php"><span>Conf. Seguimiento Servicios</span></a></li>
                    <li>
                        <a href="#" class="parent"><span>Modificar Cronograma</span></a>
                           <div>
                                <ul>
                                    <li><a href="'.RAIZ.'/vista/ordenes/addsrvcro.php"><span>Agregar Servicios</span></a></li>';
                                    if ($cantTripulacion > 2){
                                       $menu.='<li><a href="'.RAIZ.'/vista/ordenes/addpstcro.php"><span>Agregar Marcas</span></a></li>';
                                    }
         $menu.='</ul>
                           </div>
                    </li>
                        <li><a href="'.RAIZ.'/vista/servicios/srvlist.php"><span>Ver Servicios Creados</span></a></li>
                        <li><a href="'.RAIZ.'/vista/servicios/srvpje.php"><span>Cargar Peaje/Cronograma</span></a></li>
            </ul></div>
        </li>';}
        }
        return $menu;
  }
  
  function menuRRHH(){
           $menu='';
          if ((array_key_exists(1, $_SESSION['permisos'])) || (array_key_exists(2, $_SESSION['permisos']))){
             $menu='<li>
                         <a href="#" class="parent"><span>RRHH</span></a>
                         <div>
                         <ul>
                             <li><a href="#" class="parent"><span>Personal</span></a>
                                    <div>
                                    <ul>';
                                    if (($_SESSION['permisos'][1] > 1) || ($_SESSION['permisos'][2] > 1)){
                                       $menu.='<li><a href="'.RAIZ.'/vista/rrhh/altaemp.php"><span>Alta</span></a></li>';
                                    }
                                    if ($_SESSION['permisos'][1] > 1){
                                       //$menu.='<li><a href="'.RAIZ.'/vista/rrhh/moddriv.php"><span>Baja/Modificaciones</span></a></li>';
                                    }
                                    $menu.='<li><a href="'.RAIZ.'/vista/rrhh/listrrhh.php"><span>Listado de Personal</span></a></li>';
                                    if (($_SESSION['permisos'][1] > 1) || ($_SESSION['permisos'][2] > 1)){
                                       $menu.='<li><a href="'.RAIZ.'/vista/rrhh/mjeups.php"><span>Enviar Mensaje</span></a></li>';
                                    if ($_SESSION['permisos'][1] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/iso/upnseg.php"><span>Subir Archivos</span></a></li>';
                                      /* $menu.='<li><a href="#" class="parent"><span>Incentivos</span></a>
                                               <div>
                                               <ul>
                                                   <li><a href="'.RAIZ.'/vista/rrhh/lstinc.php"><span>Ver Incentivos</span></a></li>
                                                   <li><a href="'.RAIZ.'/vista/rrhh/diaginc.php"><span>Configurar Parametros</span></a></li>
                                                   </ul>
                                               </div>
                                               </li>
                                               <li><a href="#" class="parent"><span>Politica Master Bus</span></a>
                                               <div>
                                               <ul>
                                               <li><a href="'.RAIZ.'/vista/iso/upnseg.php"><span>Subir Archivos</span></a></li>
                                                    </ul>
                                               </div>
                                               </li>';    */
                                       }
                                    }
                                    if ($_SESSION['permisos'][1] > 1){
                                       $menu.='<li><a href="#" class="parent"><span>Vacaciones Personal</span></a>
                                        <div>
                                        <ul>';
                                        if ($_SESSION['permisos'][1] > 2){
                                            $menu.='<li><a href="'.RAIZ.'/vista/rrhh/livacemp.php"><span>Liquidar Vacaciones</span></a></li>';
                                            $menu.='<li><a href="'.RAIZ.'/vista/rrhh/modvcemp.php"><span>Modificar Vacaciones</span></a></li>';
                                            $menu.='<li><a href="'.RAIZ.'/vista/rrhh/addsain.php"><span>Cargar Saldo Inicial</span></a></li>';
                                        }
                                        $menu.='<li><a href="'.RAIZ.'/vista/rrhh/stctactevac.php"><span>Cta. Cte. Vacaciones</span></a></li>
                                                <li><a href="'.RAIZ.'/vista/rrhh/stctactevacrs.php"><span>Listado Vacaciones</span></a></li>
                                        </ul>
                                        </div>
                                        </li>';
                                    }
                                    
                                    $menu.='<li><a href="#" class="parent"><span>Vencimientos</span></a>
                                                <div>
                                                <ul>';
                                    if ($_SESSION['permisos'][1] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/rrhh/upvtoemp.php"><span>Ingresar Vencimientos</span></a></li>
                                              <li><a href="'.RAIZ.'/vista/rrhh/vtoxcond.php"><span>Configurar Parametros</span></a></li>';
                                    }
                                    $menu.='<li><a href="'.RAIZ.'/vista/rrhh/vtoemp.php"><span>Ver Vencimientos</span></a></li>
                                             </ul>
                                               </div>
                                               </li>';
                                    if ($_SESSION['permisos'][2] >= 1){ //usuarios de trafico para diagramar francos
                                       $menu.='<li><a href="'.RAIZ.'/vista/rrhh/diagfer.php"><span>Diagramar Novedades</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/rrhh/diagferi.php"><span>Diagramar Feriados</span></a></li>';
                                             //  <li><a href="'.RAIZ.'/vista/rrhh/diagfer.php"><span>Diagramar Feriados</span></a></li>';
                                    }
                                    
                                                                  $menu.='<li><a href="#" class="parent"><span>Pedido de Explicacion</span></a>
                                    <div><ul>';
                                    if ($_SESSION['permisos'][4] > 1){
                                       $menu.='<li><a href="'.RAIZ.'/vista/segvial/updcgo.php"><span>Ingresar Pedido de Explicacion</span></a></li>
                                               <li><a href="'.RAIZ.'/vista/segvial/readcgo.php"><span>Ver Pedido de Explicacion Generados</span></a></li>';
                                    }

                   $menu.='</ul></div>
                           </li>';
                                    
                                    
                         $menu.='</ul>
                                 </div>
                                 </li>
                                 <li><a href="#" class="parent"><span>Novedades</span></a>
                                        <div>
                                        <ul>
                                            <li><a href="'.RAIZ.'/vista/rrhh/nvdas/nvups.php"><span>Ingresar Novedad</span></a></li>
                                            <li><a href="'.RAIZ.'/vista/rrhh/nvdas/modnvda.php"><span>Baja/Modificacion Novedades</span></a></li>
                                            <li><a href="'.RAIZ.'/vista/rrhh/nvdas/nvlist.php"><span>Listado de Novedades</span></a></li>
                                        </ul>
                                        </div>
                                 </li>';
                         if ($_SESSION['permisos'][1] > 1){
                            $menu.='<li>
                                        <a href="#" class="parent"><span>Certificados Medicos</span></a>
                                        <div>
                                        <ul>
                                            <li><a href="'.RAIZ.'/vista/rrhh/upcertmed.php"><span>Ingresar Certificado</span></a></li>
                                            <li><a href="'.RAIZ.'/vista/rrhh/certlist.php"><span>Ver Certificados</span></a></li>
                                        </ul>
                                        </div>
                                    </li>';
                            }
                         if ($_SESSION['permisos'][1] > 1){
                            $menu.='<li>
                                        <a href="#" class="parent"><span>Programa de Incentivos</span></a>
                                        <div>
                                        <ul>
                                            <li><a href="'.RAIZ.'/vista/rrhh/ictvo/geninc.php"><span>Ingresar</span></a></li>
                                        </ul>
                                        </div>
                                    </li>';
                            }
             $menu.='</ul>
                     </div>
                     </li>';
        }
        return $menu;
  }
  
  function menuTurismo(){
           $menu='<li><a href="#" class="parent"><span>Turismo</span></a>
                          <div><ul>
                                   <li><a href="#" class="parent"><span>Servicios</span></a>
                                          <div><ul>
                                                   <li><a href="'.RAIZ.'/vista/ordenes/ordtur.php"><span>Cargar Servicio</span></a></li>
                                                   <li><a href="'.RAIZ.'/vista/turismo/srvlst.php"><span>Listado Servicios</span></a></li>
                                          </ul></div>
                                   </li>
                                   <li><a href="#" class="parent"><span>Cta. Cte. Clientes</span></a>
                                          <div><ul>
                                                   <li><a href="'.RAIZ.'/vista/turismo/ldpgs.php"><span>Cargar Pagos</span></a></li>
                                                   <li><a href="'.RAIZ.'/vista/turismo/rctct.php"><span>Resumen Cta. Cte.</span></a></li>
                                          </ul></div>
                                   </li>
                                   <li><a href="#" class="parent"><span>Ordenes de Compra</span></a>
                                          <div><ul>
                                                   <li><a href="'.RAIZ.'/vista/turismo/addnroc.php"><span>Cargar Ordenes de compra</span></a></li>
                                          </ul></div>
                                   </li>

                                   
                          </ul></div>
                   </li>';                   
         /* if ((array_key_exists(1, $_SESSION['permisos'])) || (array_key_exists(2, $_SESSION['permisos']))){
<li><a href="#" class="parent"><span>Nuevo Modulo Turismo</span></a>
                                          <div><ul>
                                                   <li><a href="'.RAIZ.'/vista/turismo/nwprs.php"><span>Cargar Presupuesto</span></a></li>
                                                   <li><a href="'.RAIZ.'/vista/turismo/srvtrlst.php"><span>Listado Presupuestos</span></a></li>
                                                   <li><a href="'.RAIZ.'/vista/turismo/rctctt.php"><span>Ver Cta Cte Cliente</span></a></li>
                                          </ul></div>
                                   </li>

        }  */
        return $menu;
  }
  
  function menu(){
      $conn = conexcion();
      $sql = "SELECT moduloAfectado FROM usuariosxestructuras where (id_usuario = $_SESSION[userid]) and (id_estructura = $_SESSION[structure])";
      $result = mysql_query($sql, $conn);//  ejecutarSQL($sql);
      if ($row = mysql_fetch_array($result)){
         $modulo = $row['moduloAfectado'];
      }
      
      $sql = "SELECT id_informe FROM informesPorUsuario where id_usuario = $_SESSION[userid]";  //levanta la lista de informes a los cuales puede acceder el usuario
      $result = mysql_query($sql, $conn);
      $informes = array();
      while ($row = mysql_fetch_array($result)){
            $informes[]=$row[0];
      }
      
      $menu = '<br><span class="ui-widget">'.$_SESSION['structure_name'].' ('.strtoupper($_SESSION['apenomUser']).')</span>
               <img src="'.RAIZ.'/masterbus-logo.png" border="0" align="right">
               <br>
               <span class="ui-widget"><div id="reloj"></div></span>
               <hr align="tr">
               <div id="menu">
                         <ul class="menu">
                             <li>
                                 <a href="/vista" class="parent"><span>Inicio</span></a>
                             </li>';
        if (($modulo == 5) || ($modulo == 6)){
               $menu.='
                      <li>
                        <a href="#" class="parent"><span>Diagrama de Trabajo</span></a>
                           <div>
                              <ul>
                                <li><a href="'.RAIZ.'/vista/ordenes/ordenesl.php"><span>Ver Ordenes Trabajo</span></a></li>
                                <li><a href="'.RAIZ.'/vista/ordenes/ordlvd.php"><span>Ver Diagrama</span></a></li>
                              </ul>
                            </div>
                      </li>

                             <li>
                        <a href="#" class="parent"><span>Gestion Mantenimiento</span></a>
                           <div>
                                <ul>';
								if ($modulo <= 6){
  									$menu.='<li><a href="'.RAIZ.'/vista/taller/gstcgl.php"><span>Registrar Cargas Gasoil</span></a></li>';
                    $menu.='<li><a href="'.RAIZ.'/vista/taller/ingsgl.php"><span>Registrar Ingreso Gasoil</span></a></li>';         
                    $menu.='<li><a href="'.RAIZ.'/vista/taller/printpl.php"><span>Imprimir planilla Carga</span></a></li>';                             
								}
                if (in_array($_SESSION['userid'], array('25', '33', '17','112','131', '132'))){
                     $menu.='<li><a href="'.RAIZ.'/vista/taller/state.php"><span>Ver Km disponibles</span></a></li>                     
                             <li><a href="'.RAIZ.'/vista/taller/setque.php"><span>Configurar Parametros Unidades</span></a></li>';
                }
								if ($modulo == 6){
                    $menu.='<li><a href="'.RAIZ.'/vista/taller/gestionlavados.php"><span>Cargar Lavado</span></a></li>
											      <li><a href="'.RAIZ.'/vista/informes/cria/inflvd.php"><span>Ver Lavados Realizados</span></a></li>';
								}
									 
               $menu.='</ul>
                           </div>
                           </li>';

               $menu.='<li>
                         <a href="'.RAIZ.'/vista/iso/nrmtva/vwnsiso.php" class="parent"><span>Documentacion SGI</span></a>
						 </li>
						 <li>
                        <a href="'.RAIZ.'/modelo/acceso/logout.php" class="parent"><span>Cerrar Sesion</span></a>

                           
                           
                           
                                </li>
                                </ul>
                        </div>';
        }else
        {
           $menu.= menuTrafico();

        $menu.='</li>
                <li class="last"><a href="#"><span>Informes</span></a>
                    <div>
                    <ul>
                    <li>
                        <a href="#" class="parent"><span>Trafico</span></a>
                           <div>
                                <ul>                                    
                                    <li><a href="'.RAIZ.'/vista/informes/trafico/diagdia.php"><span>Diagrama diario</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/rrhh/stctacteff.php"><span>Gestionar Franco/Feriado</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/ordenes/vrfDgm.php"><span>Verificar Diagrama</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/trafico/connas.php"><span>Conductores sin ordenes asignadas</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/servicios/viewSrv.php"><span>Ver servicios creados</span></a></li>                                    
                                    <li><a href="'.RAIZ.'/vista/informes/cria/kmrecliun.php"><span>Km recorridos por Interno</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/graficos.php"><span>Diagramacion Internos</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/trafico/chint.php"><span>Internos Modificados</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/ordenes/viewplctrl.php"><span>Planilla control supervisores</span></a></li>';
         if ( in_array($_SESSION['userid'], array(2,7,17,25,69,24,33,61,74,7))){
            $menu.='<li><a href="'.RAIZ.'/vista/ordenes/sendinf.php"><span>Generar/Enviar Informes</span></a></li>';
         }
         $rrhhxc = "kmxcond.php";
         if ($_SESSION['modaf'] == 2){
            $menu.='<li><a href="'.RAIZ.'/vista/informes/trafico/modordssup.php"><span>Mod. Ordenes Superpuestas</span></a></li>';
            $menu.='<li><a href="'.RAIZ.'/vista/informes/trafico/sspcch.php"><span>Unidades superpuestas</span></a></li>';            
         }

         $menu.='<li><a href="'.RAIZ.'/vista/informes/trafico/pjeflto.php"><span>Peajes por Interno</span></a></li>';
         $menu.='</ul>
                           </div>
                    </li>
                    <li>
                        <a href="#" class="parent"><span>RRHH</span></a>
                           <div>
                                <ul>
                                    <li><a href="'.RAIZ.'/vista/informes/rrhh/'.$rrhhxc.'"><span>KM por conductor</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/rrhh/closeorders.php"><span>Horario Cierre Ordenes</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/rrhh/cnporcli.php"><span>Conductores por Cliente</span></a></li>
                                    <li>
                                    <a href="#" class="parent"><span>Informe Ausentismo</span></a>
                                       <div>
                                            <ul>
                                                <li><a href="'.RAIZ.'/vista/informes/cria/auscnd.php"><span>Configurar Informe</span></a></li>
                                                <li><a href="'.RAIZ.'/vista/informes/cria/auscndinf.php"><span>Generar Informe</span></a></li>
                                            </ul>
                                       </div>
                                    </li>
                                </ul>
                           </div>
                    </li>                    ';

           $menu.='<li>
                        <a href="#" class="parent"><span>Contaduria</span></a>
                           <div>
                                <ul>';
           if ($_SESSION['permiso'] > 8){
                             $menu.='<li>
                                         <a href="#" class="parent"><span>Facturacion</span></a>
                                         <div>
                                              <ul>
                                                  <li><a href="'.RAIZ.'/vista/servicios/valcro.php"><span>Cargar Precio Servicio</span></a></li>
                                                  <li><a href="'.RAIZ.'/vista/servicios/addart.php"><span>Agregar Articulo Nuevo</span></a></li>
                                                  <li><a href="'.RAIZ.'/vista/informes/cria/factmen.php"><span>Control Precio-Servicio</span></a></li>
                                                  <li><a href="'.RAIZ.'/vista/informes/cria/recintcli.php"><span>Facturacion Interno-Cliente</span></a></li>
                                                  <li><a href="'.RAIZ.'/vista/informes/cria/reccliint.php"><span>Facturacion Cliente-Interno</span></a></li>
                                                  <li><a href="'.RAIZ.'/vista/informes/cria/hsxsrv.php"><span>Horas Por Servicis</span></a></li>
                                                  <li>
                                                     <a href="#" class="parent"><span>Facturacion (Nuevo)</span></a>
                                                     <div>
                                                          <ul>
                                                              <li><a href="'.RAIZ.'/vista/servicios/newtrf.php"><span>Cargar Precio Servicio</span></a></li>
                                                              <li><a href="'.RAIZ.'/vista/servicios/addart.php"><span>Configurar Articulos</span></a></li>       
                                                              <li><a href="'.RAIZ.'/vista/informes/cria/factvta.php"><span>Nueva Factura Venta</span></a></li>
                                                              <li><a href="'.RAIZ.'/vista/informes/cria/viewfacts.php"><span>Ver facturas</a></li>
                                                              <li><a href="'.RAIZ.'/vista/servicios/admpli.php"><span>Planilla control cliente</a></li>
                                                              <li><a href="'.RAIZ.'/vista/informes/cria/resint.php"><span>Resumen de Venta</a></li>
                                                          </ul>
                                                     </div>
                                                  </li>                                                  
                                              </ul>
                                         </div>
                                     </li>';
                             $menu.='<li><a href="'.RAIZ.'/vista/informes/cria/ocucnd.php"><span>Ocupacion Conductores</span></a></li>
                                     <li><a href="'.RAIZ.'/vista/informes/cria/hsxclixcnd.php"><span>Hs. Conductor Cliente</span></a></li>
                                     <li><a href="'.RAIZ.'/vista/informes/cria/chkord.php"><span>Ver chequeo ordenes</span></a></li>
                                     <li><a href="'.RAIZ.'/vista/informes/cria/kmrecli.php"><span>Servicios por Cliente</span></a></li>
                                     <li><a href="'.RAIZ.'/vista/informes/cria/rpty.php"><span>Generar Informes</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/kmrecliun.php"><span>Km recorridos por Interno</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/condxint.php"><span>Conductores por Interno</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/lstuda.php"><span>Unidades utilizadas por cliente</span></a></li>';
           }
           elseif (in_array($_SESSION['userid'], array(123)))   {
              $menu.='<li><a href="'.RAIZ.'/vista/informes/cria/condxint.php"><span>Conductores por Interno</span></a></li>';
           }
           if (in_array(1, $informes))
              $menu.='<li><a href="'.RAIZ.'/vista/informes/cria/rpty.php"><span>Generar Informes</span></a></li>';
           
           $menu.='<li>
                      <a href="#" class="parent"><span>Peajes</span></a>
                         <div>
                              <ul>
                                  <li><a href="'.RAIZ.'/vista/informes/trafico/pjeflto.php"><span>Peajes Interno/Propietario</span></a></li>
                                  <li><a href="'.RAIZ.'/vista/informes/trafico/pjeintest.php"><span>Pasadas Interno Peaje</span></a></li>
                              </ul>
                         </div>                           
                  </li>';
           $menu.='<li><a href="'.RAIZ.'/vista/informes/cria/kmflcli.php"><span>Conduccion de Fleteros</span></a></li>
                   <li><a href="'.RAIZ.'/vista/informes/cria/kmfletero.php"><span>Km Fleteros</span></a></li>
                   <li><a href="'.RAIZ.'/vista/informes/cria/efcintcnt.php"><span>Confiabilidad Fleteros</span></a></li>
                  <li><a href="'.RAIZ.'/vista/informes/cria/kmturismo.php"><span>Km Turismo</span></a></li>';
           if ($_SESSION['permiso'] > 8){
                             $menu.='<li><a href="'.RAIZ.'/vista/informes/cria/hsxcond.php"><span>Hs. por condcutor</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/kmxstr.php"><span>KM Recorridos por Estructura</span></a></li>
                                     <li><a href="'.RAIZ.'/vista/informes/cria/kmxcndrs.php"><span>KM Recorridos por Conductor</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/hsxstr.php"><span>Horas Consumidas por Estructura</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/ordssup.php"><span>Informe Horas Superpuestas</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/peajescliente.php"><span>Peajes por cliente</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/informes/cria/kmxintres.php"><span>KM por Interno por Cliente</span></a></li>';

        }
        $menu.='</ul>
                           </div>
                    </li>';
        if ($_SESSION['permiso'] > 8){
                    $menu.='<li>
                         <a href="#" class="parent"><span>ISO</span></a>
                           <div>
                                <ul>
                                    <li><a href="'.RAIZ.'/vista/informes/iso/kmcndiso.php"><span>KM x Conductor</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/segvial/updcgo.php"><span>Formulario Descargos</span></a></li>
                                    <li><a href="'.RAIZ.'/vista/iso/nrmtva/upnsiso.php"><span>Subir Archivos</span></a></li>
                                </ul>
                           </div>
                    </li>';
        }
                    
        $menu.='   </ul>
                   </div>
        </li>';
        if ($_SESSION['permiso'] > 0){
           $menu.='<li><a href="#" class="parent"><span>Base de Datos</span></a>
            <div><ul>
                <li><a href="#" class="parent"><span>Clientes</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/vista/bd/clientesnew.php"><span>Alta</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/clientes/updclientes.php"><span>Baja/Modificacion Cliente</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/restcli.php"><span>Restricciones Cliente</span></a></li>
                    </ul></div>
                </li>
                <li><a href="#" class="parent"><span>Tablas</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/cityup.php"><span>Nueva Ciudad</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/classup.php"><span>Nueva Clase Servicio</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/turnup.php"><span>Nuevo Turno Servicio</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/typeup.php"><span>Nuevo Tipo Servicio</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/empup.php"><span>Nuevo Empleador</span></a></li>
                        <li><a href="'.RAIZ.'/vista/bd/tablas/cnsup.php"><span>Nuevo Codigo Novedad</span></a></li>
                    </ul></div>
                </li>
            </ul></div>
        </li>';
        }

        $menu.=menuTurismo();
        $menu.=menuSegVial();
        $menu.=menuRRHH();
        if (($_SESSION['permiso'] > 9) || (in_array($_SESSION['userid'], array(60))))
        {
//                         <li><a href="'.RAIZ.'/vista/informes/cria/aur/segcnd.php"><span>Ver X Conductor</span></a></li>        
           $menu.='<li><a href="#" class="parent"><span>Auditar</span></a>
            <div><ul>
                <li><a href="#" class="parent"><span>Ordenes</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/vista/informes/cria/aur/segord.php"><span>Ver Historial</span></a></li>

                    </ul></div>
                </li>

            </ul></div>
        </li>';
        }
        $menu.='<li class="last"><a href="'.RAIZ.'/vista/iso/nrmtva/vwnsiso.php"><span>Documentacion SGI</span></a></li>
                <li class="last"><a href="#"><span>Sesion</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/modelo/acceso/logout.php"><span>Cerrar sesion</span></a></li>

                <li><a href="#" class="parent"><span>Sistema</span></a>
                    <div><ul>
                        <li><a href="'.RAIZ.'/vista/rrhh/altaemp.php"><span>Ver Ultimos Ingresos</span></a></li>
                    </ul></div>
                </li>
                <li><a href="#" class="parent"><span>Normativa ISO</span></a>
                    <div>
                         <ul>';
                         
           $sql = "SELECT upper(texto) as link, n.file
                   FROM normativa n
                   ORDER BY texto";
           $trafico = "";
           $result = ejecutarSQL($sql, $conn);
              while($row = mysql_fetch_array($result)){
                         $menu.='<li><a href="/vista/iso/'.$row['file'].'" target="_blank"><span>'.$row['link'].'</span></a></li>';
              }
        $menu.='</ul>
                    </div>
                </li>
                <li><a href="#" class="parent"><span>Estructuras</span></a>
                    <div><ul>';
              $result = ejecutarSQL("SELECT e.id as est, e.nombre, us.apenom, us.id as user, permisos
                                     FROM usuariosxestructuras u
                                     inner join estructuras e on e.id = u.id_estructura
                                     inner join usuarios us on us.id = u.id_usuario
                                     where id_usuario = $_SESSION[userid]", $conn);

              while ($data = mysql_fetch_array($result)){
                        $menu.="<li><a href='/vista/index.php?v=$data[est]&s=$data[nombre]&t=$data[user]&n=$data[apenom]&pm=$data[permisos]'><span>$data[nombre]</span></a></li>";
              }
$menu.='</ul></div>
                </li>
            </ul></div>
        </li>
    </ul>
</div>'; }
        mysql_close($conn);
        print $menu;
  }
?>

