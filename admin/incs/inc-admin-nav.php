<?php
?>

<!-- Navbar -->
<nav id="barramenuadmin" class="navbar  navbar-fixed-top " role="navigation">
 
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#listanavbar">
				<span class="sr-only">Men&uacute;</span>
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span> 
				<span class="icon-bar"></span>
			</button>
			<div class="logo">
				<a class="navbar-brand" href="<?php echo URL_ADMIN;?>">
					<img src="imgs/logos/logo_sinfondo_168x60.png" class="img-responsive img-rounded" alt="Inicio" title="Inicio" >
				</a>
			</div>
		</div>
		<!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                 
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php //echo $Usuario->getNombre();?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo URL_ADMIN."usuario-perfil.php";?>"><i class="fa fa-fw fa-user"></i> Perfil</a>
                        </li>
                        
                        <li>
                            <a href="<?php echo URL_ADMIN."usuario-pass.php";?>"><i class="fa fa-fw fa-gear"></i> Config</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?php echo URL_ADMIN."logout.php";?>"><i class="fa fa-fw fa-power-off"></i> Cerrar sesi&oacute;n</a>
                        </li>
                    </ul>
                </li>
            </ul>
		<div id="listanavbar" class="navbar-collapse collapse ">
			<ul class="nav navbar-nav side-nav">
				<li id="listanav-inicio">
					<a href="<?php echo URL_ADMIN;?>"> Inicio</a></li>

				<li id="listanav-charter">
					<a href="">Gestionar Usuarios</a></li>
				<li >
                        <a href="javascript:;" data-toggle="collapse" data-target="#listanav-turismo">Noticias <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="listanav-turismo" class="collapse">
                           <li>
                            <a href="<?php echo URL_ADMIN."alta-turismo.php";?>">Crear Noticia</a>
                        </li>
                        
                        <li>
                            <a href="<?php echo URL_ADMIN."lista-turismo.php";?>">Ver todas</a>
                        </li>
                        </ul>
                </li>
				 
				<li >
                        <a href="javascript:;" data-toggle="collapse" data-target="#listanav-novedades">Novedades <i class="fa fa-fw fa-caret-down"></i></a>
                        <ul id="listanav-novedades" class="collapse">
                           <li>
                            <a href="<?php echo URL_ADMIN."alta-novedad.php";?>">Crear novedad</a>
                        </li>
                        
                        <li>
                            <a href="<?php echo URL_ADMIN."lista-novedades.php";?>">Ver todas</a>
                        </li>
                        </ul>
                </li>
				 
			</ul>

		</div>
		<!-- /.navbar-collapse -->

	 
</nav>
<!-- /.navbar -->
