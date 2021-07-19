<?php

?>
 <div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-sign-in fa-fw"></i>Ingres&aacute; con los datos de tu cuenta
							</div> 
						</div>
						<div class="portlet-body">
							<div class="col-lg-12 ">     
			                 	<p class="text-muted">Completa los campos con tu usuario y contrasena para ingresar.</p>
			            	</div>
							<form id="frmlogin" class="form-horizontal" role="form">
	                            <div class="form-group">        
	                            	<label for="txtclienteuser" class="control-label col-sm-3"  >Usuario:</label>                         
	                                <div class="col-sm-9">
	                                	<input type="text" class="form-control" id="txtclienteuser" placeholder="Usuario..." required>
                                	</div>
                                	 
	                              </div>
	                            <div class="form-group">           
                                    <label for="txtclientepass" class="control-label col-sm-3">Contrase&ntilde;a:</label>
	                                <div class="col-sm-9">
	                                	<input type="password" class="form-control" id="txtclientepass" placeholder="Contrase&ntilde;a..." required >
                                	</div>
	                            </div>  
	                             
	                            <div class="col-md-offset-3">  
									<a href="<?php echo URL_CLIENTE. "recuperar-clave";?>">¿Olvidaste tu contrase&ntilde;a?</a>
									<button id="btningresar" type="submit" class="btn btn-lg btn-primary pull-right" data-loading-text="Ingesando...">Ingresar</button> 
	                            </div> 
	                              <div class="clearfix"></div>
	                        </form>
	                        <div class="col-sm-12 ">	 
	                         	<output id="resultlogin" class="text-muted text-center"></output> 
	                         </div>
					             <div class="clearfix"></div>
						</div>
					</div>