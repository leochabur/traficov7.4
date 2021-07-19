<?php

?>
<ul id="topnav-cliente" class="top-nav">
	<li id="listanav-cliente" class="dropdown">
		<?php if (isset($Cliente) && $clienteLogueado) { ?>
		  		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-fw"></i> <?php echo $Cliente->getNombre();?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php echo URL_CLIENTE."consulta";?>"><i class="fa fa-fw fa-list"></i> Reservas</a>
                        </li> 
                        <li>
                            <a href="<?php echo URL_CLIENTE."consulta";?>"><i class="fa fa-fw fa-gear"></i> Config</a>
                        </li>
                        <li class="divider"></li>
                        <li>
		  					<a role="menuitem" href="<?php echo $redirCliLogout;?>"><i class="fa fa-power-off fa-fw"></i>Salir</a>
                         </li>
                    </ul>
  		<?php }else{ ?>
				  		<a role="menuitem" href="<?php echo $redirCliLogin;?>"><i class="fa fa-user "></i>&nbsp;<strong>Ingresar</strong></a>
  		<?php }?>
                    
	</li>
				  	 
</ul>