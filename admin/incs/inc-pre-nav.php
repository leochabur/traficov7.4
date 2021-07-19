<?php

?>
 
<div class="pre-header">
        <div class="container">
            <div class="row">
                <!-- BEGIN TOP BAR LEFT PART -->
                <div class="col-md-6 col-sm-6 additional-shop-info">
                    <ul class="list-unstyled list-inline">
                        <li><i class="fa fa-phone"></i><span>(02223)-444640 / 443706</span></li>
                        <li><i class="fa fa-envelope-o"></i><span><a href="mailto:info@empresasantarita.com.ar" title="Contact&aacute;nos">info@empresasantarita.com.ar</a></span></li>
                    </ul>
                </div>
                <!-- END TOP BAR LEFT PART -->
                <!-- BEGIN TOP BAR MENU -->
                <div class="col-md-6 col-sm-6 additional-nav">
                    <ul class="list-unstyled list-inline pull-right">
                    <?php if (isset($Cliente) && $clienteLogueado) { ?>
					  		
			                 <li><a href="<?php echo URL_CLIENTE."perfil";?>"><i class="fa fa-user fa-fw"></i><?php echo $Cliente->getNombre();?></a></li>   
			                        <li>
			                            <a href="<?php echo URL_CLIENTE."consulta";?>"><i class="fa fa-fw fa-list"></i> Reservas</a>
			                        </li> 
			                  		                        
			                        <li>
					  					<a href="<?php echo $redirCliLogout;?>"><i class="fa fa-power-off fa-fw"></i>Salir</a>
			                         </li>
			                     
			  		<?php }else{ ?>							  		
					  		<li><a href="<?php echo $redirCliLogin;?>">Ingresar</a></li>
                        	<li><a href="<?php echo $redirCliLogin;?>">Registrarme</a></li>
			  		<?php }?>
                        
                    </ul>
                </div>
                <!-- END TOP BAR MENU -->
            </div>
        </div>        
    </div>