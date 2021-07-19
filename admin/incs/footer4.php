 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.3";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script src="js/showup.js"></script>
 
<script type="text/javascript">
      $("#modalPresu").click(function(ev)
      {
                    ev.preventDefault();
                    $("#idmodal").modal();
        }
                );
        
        </script>
<style>

/**
 * Docs Buttons
 */

/* Demo buttons are all <button> tags, so
 * lets style these directly.
 */
button {
  float: right !important;
}


/* Fixed button, bottom right */
.btn-fixed-bottom {
  position: fixed;
  bottom: 30px;
  width: 5px;
  right: 20px;
  display: none;
}


/* Toggles navbar classes */
.btn-hide-show {
  margin-right: 10px;
}


/* Light theme */
.btn-light {
  color: #ae0000 !important;
  font-size: 28px;
  /*background-color: rgba(0, 0, 0,.1);*/
}
.btn-light:hover {
  //color: #fff !important;
  opacity: 0.9;
  /*background-color: rgba(0, 0, 0,.25);*/
}

/* Dark theme */
.btn-dark {
  color: #fff;
  /*background-color: rgba(0, 0, 0,.5);*/
}
.btn-dark:hover {
  color: #fff;
  background-color: rgba(0, 0, 0,.9);
}



/* Buttons displayed throughout the content */
.btn-showup {
  position: relative;
  color: #fff;
  font-weight: normal;

}
.btn:focus,
.btn-showup:hover,
.btn-showup:focus {
  color: #fff;
  outline: none;
  
}


    
/*    
  8. Footer
 */
 
 ul.entries {
  padding: 0px;
  margin: 0px;
  list-style-type: none;
}

.footer-area {
	width: 100%;
  	padding-top: 25px;
  	//margin-top: 35px;
  	//border-top: 1px solid #ae0000;
  	-webkit-box-shadow: 3px 0px 18px 1px rgba(0,0,0,0.37);
	-moz-box-shadow: 3px 0px 18px 1px rgba(0,0,0,0.27);
	box-shadow: 3px 0px 18px 1px rgba(0,0,0,0.27);
	position:absolute;
	z-index: -9999;
  	font-size: 13px;
  	background-color: #232323;
}
.footer-area a {
  color: #fff;
}
.footer-area a:hover {
  text-decoration: none;
}
.footer-area .widget .widget-title {
  font-size: 13px;
  text-transform: uppercase;
  padding-bottom: 15px;
  border-bottom: 1px solid #FFFFFF;
  color: rgb(178, 47, 47);
}
.footer-area .footer-top {
  margin-bottom: 20px;
}
.footer-area .footer-bottom {
  border-top: 1px solid transparent;
  background-color: #191919;
  text-align: center;
  color: #fff;
  font-size: 13px;
  line-height: 18px;
 
}
.footer-area .footer-bottom p {
  margin-bottom: 10px;
  margin-top: 10px;
}

 .widget {
  margin-bottom: 20px;
  position: relative;
  }
  
  .icon-2x {
  position: relative;
  top: 3px;
  margin-right: 7px;
}

.entries {
  *zoom: 1;
}
.entries:before,
.entries:after {
  display: table;
  line-height: 0;
  content: "";
}
.entries:after {
  clear: both;
}
.links li {
  margin-bottom: 6px;
  text-decoration: none;
}

user agent stylesheetli {
  display: list-item;
  text-align: -webkit-match-parent;
}
.entry-header {
  margin-bottom: 20px;
}
.entry-header .small {
  font-size: 11px;
  text-transform: uppercase;
}
.entry-header .small:first-child {
  margin-bottom: 2px;
}
.entry-header .entry-meta.pull-right {
  margin-top: 5px;
  margin-right: 20px;
  margin-left: 20px;
  -webkit-opacity: 1;
  -moz-opacity: 1;
  opacity: 1;
  filter: alpha(opacity=100);
}
.entry-header-overlay {
  position: relative;
  margin-bottom: 10px;
}
.entry-header-overlay .entry-header {
  position: absolute;
  z-index: 4;
  bottom: 0px;
  left: -15px;
  right: -15px;
  margin-bottom: 0px;
  padding: 8px 15px 5px;
}
.entry-title {
  font-weight: 300;
  margin: 0px 0px 20px;
}
.entry-header .entry-title {
  margin-bottom: 0px;
}
.entry-meta {
  font-size: 12px;
  margin-bottom: 10px;
  -webkit-opacity: 0.6;
  -moz-opacity: 0.6;
  opacity: 0.6;
  filter: alpha(opacity=60);
}
.entry-header .entry-meta {
  margin-bottom: 0px;
}
.entry-header .entry-meta:first-child {
  margin-bottom: 5px;
}
.entry-thumbnail {
  overflow: hidden;
  position: relative;
}
.entry-thumbnail img {
  min-width: 100%;
  width: auto;
  height: auto;
}


</style>



<footer id="footer" class="footer-area">

			<div class="footer-top container">
	
				<div class="row">

					<div class="widget col-xs-12 col-sm-4 col-lg-4">

						<h4 class="widget-title">Empresa</h4>

						<ul class="entries links links-2-cols">
							<li><a href="<?php echo URL_INICIO;?>">Inicio</a></li>
							<li><a href="<?php echo URL_NOSOTROS;?>">La Empresa</a></li>
							<li><a href="<?php echo URL_CHARTER;?>">Charters</a></li>
							<li><a href="<?php echo URL_TRASLADOS;?>">Traslados</a></li>
							<li><a href="<?php echo URL_TURISMO;?>">Turismo</a></li>
							<li><a href="<?php echo URL_FLOTAS;?>">Flota</a></li>
							<li><a href="<?php echo URL_NOVEDADES;?>">Novedades</a></li>
							
						</ul>

					</div><!--/.col-3-->

					<div class="clearfix visible-xs"></div>
                                        
                                          <div class="widget col-xs-12 col-sm-4 col-lg-4">

						<h4 class="widget-title">Información</h4>

						<ul class="entries links links">
							<li><a role="menuitem" href="<?php echo URL_NOSOTROS;?>">Nosotros</a></li>
							<li><a role="menuitem" href="<?php echo URL_CONTACTO;?>">Contacto</a></li>
                                                        <li><a id="modalPresu" href="#" data-toggle="modal" data-target="#contact">Solicite Presupuesto</a></li>
                                                        
                                                        
                                                        
                                                      
  


                                                        
						<h4 class="widget-title">Encontranos </h4>	
							<li><a target="_blanck" href="https://twitter.com/empsantarita"><i class="fa fa-twitter fa-2x"></i> </a>
							<a target="_blanck" href="#"><i class="fa fa-google-plus fa-2x"></i></a></li>
						</ul>

					</div><!--/.col-3-->
					

					

					<div class="clearfix visible-xs"></div>
                                        
                                        
                                      
                                        <div class="widget col-xs-12 col-sm-4 col-lg-4">

						<h4 class="widget-title">Seguinos</h4>

						<ul class="entries links">
                                                   
                                                        <li><div class="fb-page" data-href="https://www.facebook.com/EmpSantaRita" data-width="280" data-hide-cover="false" data-show-facepile="false" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/EmpSantaRita"><a href="https://www.facebook.com/EmpSantaRita">Empresa Santa Rita</a></blockquote></div></div></li>
						</ul>

					</div><!--/.col-3-->
				</div><!--row.-->

			</div>
 <!-- Back-to-top Example -->
      <a href="#" class="btn back-to-top btn-light btn-fixed-bottom"> <span class="glyphicon glyphicon-chevron-up"></span> </a>
			<div class="footer-bottom">

				<div class="container aligncenter">

                                    <p>© 2015 <a target="_blanck" href="http://www.dynhop.com/">Dynhop</a>. Todos los derechos reservados. </p><p>

				</p></div>

			</div>

		</footer>


<div class="modal fade" id="contact" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal">
                <div class="modal-header">
                     <img src="<?php echo URL_WEB;?>imgs/logos/logo_sinfondo_168x60.png" height="50px" class="" alt="<?php echo WEB_TITULO;?>" title="<?php echo WEB_TITULO;?>" style="float:left; vertical-align: top; " >
                    <h4 style="text-align: right; margin-top:15px;">Solicite Presupuesto</h4>
                    <br>
                    Complete los campos a continuación 
con los datos del viaje que desea realizar y presione el boton Enviar al final del formulario. Nos pondremos en contacto con Ud. a la brevedad. 
                </div>
                <div class="modal-body">
                    
                    <div class="form-group">

                        <label for="contact-origen" class="col-lg-2 control-label">* Origen:</label>
                        <div class="col-lg-10">

                            <input type="text" class="form-control" id="contact-origen" placeholder="Origen:">

                        </div>

                    </div>
                    
                    <div class="form-group">

                        <label for="contact-destino" class="col-lg-2 control-label">* Destino:</label>
                        <div class="col-lg-10">

                            <input type="text" class="form-control" id="contact-name" placeholder="Destino">

                        </div>

                    </div>
                    
                    <div class="form-group">

                        <label for="contact-fechaS" class="col-lg-2 control-label">*Salida:</label>
                        <div class="col-lg-4">

                            <input type="datetime-local" class="form-control" id="contact-fechaS" placeholder="fecha de Salida">

                        </div>
                         
                                 

                    
                        <label for="contact-fechaR" class="col-lg-2 control-label">*Regreso:</label>
                        <div class="col-lg-4">

                            <input type="datetime-local" class="form-control" id="contact-fechaR" placeholder="fecha de Regreso">

                        </div>
                        
                        

                      

                        </div>

                   
                    <div class="divisor100modal"><br></div>
                     <div class="form-group">

                        <label for="contact-pasajeros" class="col-lg-2 control-label">*Pasajeros:</label>
                        <div class="col-lg-2">

                            <input type="number" class="form-control" id="contact-name" placeholder="N°">

                        </div>

                    </div>
                    
                     <div class="form-group">

                        <label for="contact-desc" class="col-lg-2 control-label">*Descripcion:</label>
                        <div class="col-lg-10 col-md-4">

                            <textarea type="text" class="form-control" id="contact-name" placeholder="Descripcion del Viaje"></textarea>

                        </div>

                    </div>
                    <div class="divisor100modal"><br></div>
                    <div class="form-group">

                        <label for="contact-name" class="col-lg-2 control-label">* Nombre:</label>
                        <div class="col-lg-10">

                            <input type="text" class="form-control" id="contact-name" placeholder="Nombre">

                        </div>

                    </div>
                     <div class="form-group">

                        <label for="contact-mail" class="col-lg-2 control-label">* Email:</label>
                        <div class="col-lg-10">

                            <input type="email" class="form-control" id="contact-email" placeholder="you@example.com">

                        </div>

                    </div>
                     <div class="form-group">

                        <label for="contact-tel" class="col-lg-2 control-label">* Telefono:</label>
                        <div class="col-lg-10">

                            <input type="number" class="form-control" id="contact-name" placeholder="Telefono">

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <p style=" text-align: left;">* (Campos obligatorios)</p>
                    <button class="btn btn-default col-md-2" data-dismiss="modal">Cerrar</button> 
                    <button class="btn btn-primary col-md-4" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>      
