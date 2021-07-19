<?php
?>
<!----start-top-nav-script---->
		<script>
			$(function() {
				var pull 		= $('#pull');
					menu 		= $('nav ul');
					menuHeight	= menu.height();
				$(pull).on('click', function(e) {
					e.preventDefault();
					menu.slideToggle();
				});
				$(window).resize(function(){
	        		var w = $(window).width();
	        		if(w > 320 && menu.is(':hidden')) {
	        			menu.removeAttr('style');
	        		}
	    		});
			});
		</script>
<!----//End-top-nav-script---->
<!-----#barramenu---->
	<!----- header---->
	<div id="barramenu" class=" navbar-fixed-top headroom" >
		<div class="container">
			<div class="logo">
				<a href="<?php echo URL_INICIO;?>">
					<img src="imgs/logos/logo_sinfondo_168x60.png" class="" alt="<?php echo WEB_TITULO;?>" title="<?php echo WEB_TITULO;?>" >
				</a>
			</div>
			<!----start-top-nav---->
			 <nav class="top-nav">
			 	 <?php require_once ('inc-nav-cliente.php');?>
				<ul class="top-nav">
					<li id="listanav-inicio" class="active">
						<a role="menuitem"	href="<?php echo URL_INICIO;?>">Inicio</a>
					</li>
					<li id="listanav-nosotros">
						<a role="menuitem" href="<?php echo URL_NOSOTROS;?>">La Empresa</a>
					</li>					 
					<li id="listanav-charter">
						<a href="<?php echo URL_CHARTER;?>">Charters</a>
					</li>
					<li id="listanav-traslados">
						<a href="<?php echo URL_TRASLADOS;?>">Traslados</a>
					</li>
					<li id="listanav-turismo">
						<a href="<?php echo URL_TURISMO;?>">Turismo</a>
					</li>					 
					<li id="listanav-flotas">
						<a role="menuitem" href="<?php echo URL_FLOTAS;?>">Flotas</a>
					</li>					 
					<li id="listanav-contacto">
						<a role="menuitem" href="<?php echo URL_CONTACTO;?>">Contacto</a>
					</li>
					
				</ul>
				
				<a href="#" id="pull"><img src="imgs/iconos/nav-icon.png" title="Men&uacute;" /></a>
			</nav>
			<div class="clearfix"> </div>
			
		</div>
	</div>
	  <!----- /.header---->
<!-----/#barramenu---->	  