<?php
?>

<!-- Navbar -->
<nav id="barramenu"	class="navbar navbar-inverse navbar-fixed-top headroom"	role="navigation">
	<div class="container">		
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target="#listanavbar">
				<span class="sr-only">Men&uacute;</span>
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand logo" href="<?php echo URL_INICIO;?>">
				<img src="imgs/logos/logo_sinfondo2.png" class="img-responsive img-rounded" alt="Inicio" title="Inicio" >
			</a>
		</div>

		<div id="listanavbar" class="navbar-collapse collapse ">
			<ul class="nav navbar-nav pull-right">
				<li id="listanav-inicio"><a role="menuitem"
					href="<?php echo URL_INICIO;?>"><i class="fa fa-home"></i> Inicio</a></li>
				<li id="listanav-nosotros"><a role="menuitem"
					href="<?php echo URL_NOSOTROS;?>">La Empresa</a></li>
				<li id="listanav-servicios" class="dropdown"><a
					href="<?php echo URL_SERVICIOS;?>" class="dropdown-toggle"
					data-toggle="dropdown">Servicios<b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li id="listanav-charter"><a href="<?php echo URL_CHARTER;?>">Charters</a></li>
						<li id="listanav-traslados"><a href="<?php echo URL_TRASLADOS;?>">Traslados</a></li>
						<li id="listanav-turismo"><a href="<?php echo URL_TURISMO;?>">Turismo</a></li>
					</ul></li>
				<li id="listanav-flotas"><a role="menuitem"
					href="<?php echo URL_FLOTAS;?>">Flotas</a></li>
				<li id="listanav-novedades"><a role="menuitem"
					href="<?php echo URL_NOVEDADES;?>">Novedades</a></li>
				<li id="listanav-contacto"><a role="menuitem"
					href="<?php echo URL_CONTACTO;?>">Contacto</a></li>
			</ul>

		</div>
		<!-- /.navbar-collapse -->

	</div>
	<!-- /.nav container -->

</nav>
<!-- /.navbar -->