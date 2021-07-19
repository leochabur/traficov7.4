<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
require_once ('sesion.php');

if (!$UsuarioSesion->getSesionBd()){
	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");

if (isset($_POST) && isset($_POST["txthora"])){
	$postHorario = $_POST["txthora"];
	$idDiaCharter = $_POST["txtiddia"];
	if (!empty($idDiaCharter)){
		$bd = new BdConexion();
		$sqlAlta = "INSERT INTO charter_dia_horario (IdDia, Horario)
					VALUES (:iddia,:horario ); ";
		$bd->query($sqlAlta);
		$bd->bind(':iddia', $idDiaCharter);
		$bd->bind(':horario', $postHorario);
		$bd->execute();
		$bd = null;
	}
}

if (isset($_POST) && isset($_POST["txtdias"])){
	$postDias = $_POST["txtdias"];
	$idCharter = $_POST["txtidcharter"];
	if (!empty($idCharter)){
		 
		$bd = new BdConexion();
		$sqlAlta = "INSERT INTO charter_dia (IdCharter, Dia)";
		$sqlAlta.= " VALUES (:idcharter,:dia) ";
		$bd->query($sqlAlta);
		foreach($postDias as $postDia){
			 
			$bd->bind(':dia', $postDia);
		}
		$bd->bind(':idcharter', $idCharter);
		
		$bd->execute();
		$bd = null;
		 
	}
}

if (isset($_POST) && isset($_POST["txtparada"])){
	$postParada = $_POST["txtparada"];
	$idDiaCharter = $_POST["txtiddia"];
	if (!empty($idDiaCharter)){
			
		$bd = new BdConexion();
		$sqlAlta = "INSERT INTO charter_parada (IdDiaHorario, Direccion, Lat, Lng)";
		$sqlAlta.= " VALUES (:iddiahorario,:direccion, :lat, :lng) ";
		$bd->query($sqlAlta);
		 
		$bd->bind(':iddiahorario', $idDiaCharter);
		$bd->bind(':direccion', $postParada);
		$bd->bind(':lat', null);
		$bd->bind(':lng', null);
		$bd->execute();
		$bd = null;
			
	}
}


$bd = new BdConexion();

$bd->query("SELECT Id, Origen, Destino FROM charter; ");
$bd->execute();
$listaCharters = $bd->getFilas(); 
 
$bd = null;

$webTitulo = WEB_TITULO . " - Charters";

?>
<!DOCTYPE html>
<html>
<head>

    <?php require_once ($raiz.'incs/inc-meta.php');?>
    
    <title><?php echo $webTitulo;?></title>
	<base href="<?php echo RUTA_BASE;?>" target="_top" />
    
    <?php require_once ('incs/inc-admin-css.php');?> 
            
    <?php require_once ('incs/inc-admin-js.php');?>   
 
	
</head>

<body>
	<div id="contenedor">
    	<?php require_once ('incs/inc-admin-nav.php');?>
     
		<!-- Admin -->
		<div id="adminpanel">
			<div class="container-fluid ">
				 <div class="row">
				 	<div class="col-xs-12 col-sm-12 col-md-12">
						<h1 class="page-header">Charters <small>Horarios y paradas</small></h1>				 		
					</div>
				</div>
				 
				<div class="row">
				  	<div class="col-sm-12 alert alert-success">
				  		<p>Haga click en un horario de salida para ver el recorrido</p>
				  	</div>
					<?php 
					foreach($listaCharters as $charter)
					{
					?>
						
					<div class="col-sm-6 col-md-6">
						<!-- #Panel Charter -->
						
					 
						<div id="panelcharter<?php echo $charter["Id"];?>" class="panel panel-danger">
							<div class="panel-heading">
								<h3 class="panel-title">Origen: <?php echo $charter["Origen"];?></h3>
								<h3 class="panel-title">Destino: <?php echo $charter["Destino"];?></h3>								
							</div>
							
							<div class="panel-body">
								
								<div class="panel-group" role="tablist">
								<?php 

								$sqlCharterDias ="SELECT a.Id, a.Dia 
												FROM charter_dia a 
												WHERE a.IdCharter = :idcharter; ";

								$bd = new BdConexion();
								$bd->query($sqlCharterDias);
								$bd->bind(":idcharter", $charter["Id"]);
								$bd->execute();
								$charterDias = $bd->getFilas();
								
								foreach($charterDias as $dias)
								{
									$lista1 = "dias".$dias["Id"];
									$lista = "listachart".$charter["Id"]."dia".$dias["Id"];
									
								?>
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" >
											<h4 class="panel-title"  >
												<a class="collapsed" data-toggle="collapse" id="<?php echo $lista1?>"
													href="<?php echo "#".$lista;?>" aria-expanded="false"
													aria-controls="<?php echo $lista;?>"><i class="fa fa-calendar fa-fw"></i> <?php echo $dias["Dia"];?> </a>
												<button type="button" title="Agregar horario" data-iddia="<?php echo $dias["Id"];?>" class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#modalhorario"><i class="fa fa-plus fa-fw"></i> Horario</button>
												<div class="clearfix"></div>
											</h4>
										</div>
										<?php 
										$sqlCharterHorarios ="SELECT b.Id, b.Horario
												FROM charter_dia_horario  b
												WHERE b.IdDia = :iddia; ";
										
										$bd->query($sqlCharterHorarios);
										$bd->bind(":iddia", $dias["Id"]);
										$bd->execute();
										$charterHorarios = $bd->getFilas();
										?>
										<div id="<?php echo $lista;?>" class="panel-collapse"
												role="tabpanel" aria-labelledby="<?php echo $lista1;?>"
												aria-expanded="false">
											<div class="list-group">
											
											 <?php 
											foreach($charterHorarios as $horario)
											{											
											?>										 
											<div class="list-group-item" id="recorrido<?php echo $horario["Id"];?>">
												<a class="recorridos" href="#"  title="Ver recorrido"
													data-idhorario="<?php echo $horario["Id"];?>"
													data-orig="<?php echo $charter["Origen"];?>"
													data-dest="<?php echo $charter["Destino"];?>"><i class="fa fa-clock-o fa-fw"></i>Salida a las: <?php echo $horario["Horario"];?></a>
												<a class="recorridobaja pull-right" href="#" title="Borrar horario" data-idhorario="<?php echo $horario["Id"];?>"><i class="fa fa-close"></i></a>
											
											</div>
											
											<?php } ?>			 
											</div>
											<!-- /.list-group -->
										</div>
										
									</div>
									<!-- /#panel-group1 -->
								<?php 	
								}
								$bd = null;
								?>
								 
								</div>
								<!-- /.panel-group -->
		
							</div>
							<div class="panel-footer">
								<button type="button" title="Agregar nuevo dia" class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#modaldia" data-charter="<?php echo $charter["Id"]?>"><i class="fa fa-plus fa-fw"></i> D&iacute;a/d&iacute;as</button>
								<div class="clearfix"></div>
							</div>
						</div>
						<!-- /#Panel 1 -->
					</div>
					
					<?php 
					}	 // FIN CHARTER			
					?>
						
					 
				</div>
                <!-- /.row -->
                <div class="row">
                	<div class="col-sm-4 col-md-4">
						<div class="panel panel-warning">
							<div class="panel-heading">
								<h3 class="panel-title">Info. recorrido</h3>
							</div>
							<div class="panel-body">
			 					<div id="paradas"> 
		 							<button id="addparada" type="button" disabled="disabled" title="Agregar parada" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalparada" data-idhorario=""><i class="fa fa-plus fa-fw"></i> Parada</button>
			 						
									<!-- /.list-group -->
			 						<div id="infoParadas" class="list-group"></div>
			 					</div>
								<div id="recorrido">
			 						<h4>Info. del recorrido</h4>
			 						<div id="infoRecorrido"></div>
			 					</div>			 
		 						
		 					 
							</div>
							<div class="panel-footer">
								 
							</div>
						</div>
		
					</div>
					<div class="col-sm-8 col-md-8">
						<div class="panel panel-warning">
							<div class="panel-heading">
								<h3 class="panel-title">Recorrido</h3>
							</div>
							<div class="panel-body">
		
								<div id="map_canvas" style="width: 100%; height: 500px;"></div>
								 
							</div>
							 
						</div>
		
					</div>
				</div>
                <!-- /.row -->
			</div>
		</div>
		<!-- /#adminpanel-->
		
		<div id="modaldia" class="modal fade">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Agregar nuevo dia <span id="modaldiacharter"></span></h4>
			      </div>
			      <div class="modal-body">
			       	<form id="frmcharterdia" method="POST" role="form">
			       	<input type="hidden" name="txtidcharter" id="txtidcharter" value="">
			       	 <select name="txtdias[]" id="txtdias" class="" multiple>
					      <option value="Lunes a Viernes">Lunes a Viernes</option>
					      <option value="Sabados">Sabados</option>
					      <option value="Domingos">Domingos</option>
					      <option value="Feriados">Feriados</option>
					  </select>
			       	</form>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			        <button id="btnguardardia" name="btnguardardia" type="button" class="btn btn-primary" >Guardar</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			<div id="modalhorario" class="modal fade">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Agregar horario <span id="modaldiacharterdia"></span></h4>
			      </div>
			      <div class="modal-body">
		        	<form id="frmcharterhorario" method="POST" role="form">
			        	<input type="hidden" name="txtiddia" id="txtiddia" value="">
			        	 <div class="form-group col-sm-12">
                                <label class="col-sm-4 control-label " for="txthora">Horario:</label>
                                <div class="col-sm-3 input-group">
	                                       <span class="input-group-addon"><i class="fa fa-clock-o"></i></span> 
	                                       <input name="txthora" id="txthora" type="time" class="form-control  " placeholder="Ingrese el horario">
                                 </div>
                                
                            </div>				       	 
				       	  
	                       <div class="clearfix"></div>         
				       	 
			       	</form>
			      </div>
			      <div class="modal-footer">
		      	 	 
			        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			        <button id="btnguardarhorario" name="btnguardarhorario" type="button" class="btn btn-primary"  >Guardar</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
			
			<div id="modalparada" class="modal fade">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Agregar parada <span id="modaldiacharterdia"></span></h4>
			      </div>
			      <div class="modal-body">
			      <p>Ingrese la calle correspondiente a la parada del charter</p>
		        	<form id="frmcharterparada" method="POST" role="form">
			        	<input type="hidden" name="txtiddia" id="txtiddia" value="">
			        	 <div class="form-group ">
                                <label class="control-label " for="txtparada">Ubicaci&oacute;n:</label>                               
	                         	<input name="txtparada" id="txtparada" type="text" class="form-control  " placeholder="Ingrese la ubicacion">                              
                            </div>				       	 
				       	  
	                       <div class="clearfix"></div>         
				       	 
			       	</form>
			      </div>
			      <div class="modal-footer">
		      	 	 
			        <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>
			        <button id="btnguardarwaypnt" name="btnguardarwaypnt" type="button" class="btn btn-primary"  >Guardar</button>
			      </div>
			    </div><!-- /.modal-content -->
			  </div><!-- /.modal-dialog -->
			</div><!-- /.modal -->
		
	</div> 
	<!-- /#Contenedor-->
	
    <script type="text/javascript">

    function getParadas(id){
        
    	
    }
    
    $("body").on('click', '.paradabaja',function(ev){
		ev.preventDefault();
		 
		var id = $(this).data("idparada");
		 
		$.post("admin/funciones/post-bajaparada.php", {id: id}, function(data){
		
			if (data.bajaok){			 
				 	$("#parada"+id).remove();
			}
		}, "json");
		 
		
	});  
	
    $(document).ready(function() {
            
    	loadMapScript();

    	$(".recorridos").click(function(ev){
			ev.preventDefault();
			
			var orig = $(this).data("orig");
			var dest = $(this).data("dest");		
			var id = $(this).data("idhorario");
			$("#addparada").attr("disabled", false);
			$("#addparada").data('idhorario', id);
   		  	 
			$.get("admin/funciones/get-charter-paradas.php", {id: id}, function(data){
				var paradas = data;
				$("#infoParadas").html("");
				var html="";
				 for(var i in paradas){
					html+= '<div class="list-group-item" id="parada'+paradas[i].Id+'"><a >'+paradas[i].Direccion+'</a><a class="paradabaja pull-right" href="#" title="Borrar" data-idparada="'+paradas[i].Id+'"><i class="fa fa-close"></i></a></div>';
							
		    	  }
				$("#infoParadas").html(html);
				
				calcRoute(orig,dest, paradas);
				
			}, "json");
			
			 
			
    	}); 
    	 
    	$(".recorridobaja").click(function(ev){
			ev.preventDefault();
			 
			var id = $(this).data("idhorario");
			 
			$.post("admin/funciones/post-bajahorario.php", {id: id}, function(data){
			
				if (data.bajaok){			 
 				 	$("#recorrido"+id).remove();
				}
			}, "json");
			
			 
			
    	});  
    	
    	
    	$('#modaldia').on('show.bs.modal', function (event) {
    		  var button = $(event.relatedTarget) 
    		  var idcharter = button.data('charter');
    		   
    		  var modal = $(this);
    		  modal.find('#txtidcharter').val(idcharter);
    		  
    		});

    	$('#modalhorario').on('show.bs.modal', function (event) {
			  var button = $(event.relatedTarget)  ;
			  var idhorario = button.data('iddia');
			  var modal = $(this);
			  modal.find('#txtiddia').val(idhorario);
			}) ;   

    	$('#modalparada').on('show.bs.modal', function (event) {
			  var button = $(event.relatedTarget)  ;
			  var idhorario = button.data('idhorario');
			  var modal = $(this);
			  modal.find('#txtiddia').val(idhorario);
			}) ;   

      	
		$("#btnguardardia").click(function(ev){
			ev.preventDefault();
			$("#frmcharterdia").submit();
			return true;
		});
		
		$("#btnguardarhorario").click(function(ev){
			ev.preventDefault();
			$("#frmcharterhorario").submit();
			return true;
		});

		$("#btnguardarwaypnt").click(function(ev){
			ev.preventDefault();
			$("#frmcharterparada").submit();
			return true;
		});
            
    });
     
    </script>
     <script type="text/javascript">
      
     var directionsDisplay;    
     var map;
     var laplata ;
     var cnelbrands ;
     var capfed ;
     
      function initialize_map() {

    	  laplata =   new google.maps.LatLng(-34.922041, -57.954715);
    	     cnelbrands = new google.maps.LatLng(-35.169816, -58.231383);
    	     capfed = new google.maps.LatLng(-34.636519, -58.377283);
    	 
    	directionsDisplay = new google.maps.DirectionsRenderer();
        
        var mapOptions = {
          center: laplata,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
       
    
        directionsDisplay.setMap(map);
      }
      
      // async
      function loadMapScript() {
    	  var script = document.createElement("script");
    	  script.type = "text/javascript";
    	  script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyB5XdPi6kIICmqW7jzikv6klvXLuiFBkgU&sensor=false&callback=initialize_map";
    	  document.body.appendChild(script);
    	}
  	
      function calcRoute(orig, dest, paradas) {
    	  var directionsService = new google.maps.DirectionsService();
    	  
    	  var waypts = [];
    	  
    	  for(var i in paradas){
        	  var lat = null;
        	  var lng = null;
        	  var waypt = null;
        	  if (paradas[i].Lat !== null && paradas[i].Long !== null){
				lat = paradas[i].Lat;
				lng = paradas[i].Lng;
				waypt = new google.maps.LatLng(lng, lng);
        	  }
        	  if (paradas[i].Direccion !== null ){
        		  waypt = paradas[i].Direccion;
        	  }	 
    		  
    		  waypts.push({
                  location: waypt,
                  stopover: true
                  });
    	  }
    	  
      
    	  var request = {
    	    origin : orig,
    	    destination : dest,
    	    waypoints: waypts,
    	    travelMode: google.maps.TravelMode.DRIVING
    	  };
    	  
    	  directionsService.route(request, function(result, status) {
        	  
    	    if (status == google.maps.DirectionsStatus.OK) {
    	    	var route = result.routes[0];
 					
	    	      for(var i in result.routes){
	    	     
	    	         var mylegs=result.routes[i].legs
	    	        
	    	         var strinfo=" Origen: " + orig+ "</br>"; 
	    	         strinfo += " Destino: " + dest+ "</br></br>"; 
	    	          
	    	         for(var j = 0; j < mylegs.length; j++){
	    	        	 var routeSegment = j + 1;
	    	        	 strinfo += '<b>Segmento: ' + routeSegment + '</b><br>'; 
	    	        	 strinfo += "<b>Desde</b>: " + mylegs[j].start_address + " </br> ";
	    	        	 strinfo += "<b>Hasta</b>: " + mylegs[j].end_address + " </br> ";
	    	        	 strinfo += "<b>Distancia</b>: " + mylegs[j].distance.text+ " </br> ";
	    	        	 strinfo += "<b>Duracion</b>: " + mylegs[j].duration.text+ " </br> </br>";
	    	         }
	    	         
	    	         $("#infoRecorrido").html(strinfo);
	    	      }
    	      
    	      	directionsDisplay.setDirections(result);
    	      
    	    }
    	  });
    	}
    	 
    </script>

</body>

</html>
