<?php

require_once('base.inc.php');  
$webTitulo = WEB_TITULO . " - Contacto";
$webDescrip = WEB_DESCRIP;
?>
<!DOCTYPE html>
<html>
<head>

	<title><?php echo $webTitulo;?></title>
    <base href="<?php echo RUTA_BASE;?>"  target="_top"/>
        
    <?php require_once ('incs/inc-meta.php');?>   
   
    <?php require_once ('incs/inc-css.php');?>
    <link rel="stylesheet" href="<?php echo URL_WEB;?>css/intlTelInput.css">
    <link rel="stylesheet" href="<?php echo URL_WEB;?>css/bootstrap-datetimepicker.min.css">
    
    <style>
        
     
.google-maps {
position: relative;
padding-bottom: 20%; // This is the aspect ratio
height: 0;
overflow: hidden;

}
.google-maps iframe {
position: absolute;
top: 0;
left: 0;
width: 100% !important;
height: 300px !important;
margin-top: 50px;
pointer-events: none;
}

#contact_form
{
    
    width: 50%;
    
    
}
.row {
	margin:0 auto;
}

body.modal-open, 
.modal-open .navbar-fixed-top, 
.modal-open .navbar-fixed-bottom {
  margin-right: 0;
}
.divisor100modal{
	background:#D5D5D5;
	width:100%; 
	height: 1px;
  	display: inline-block;
  	 	
}
.modal {
  bottom: 25px;
  right: 25%;
  padding: 0;
  width: auto;
  margin: 0 auto;
  /* background-color: #ffffff; */
  /* border: 1px solid #999999; */
  /* border: 1px solid rgba(0, 0, 0, 0.2); */
  border-radius: 6px;
  /* -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5); */
  /* box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5); */
  /* background-clip: padding-box; */

.modal.container {
  max-width: none;
}
.button {
    
    float: left !important; 
}
.modal-backdrop {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 1040;
}
.input-append .add-on:last-child, .input-append .btn:last-child, .input-append .btn-group:last-child > .dropdown-toggle {
  -webkit-border-radius: 0 4px 4px 0;
  -moz-border-radius: 0 4px 4px 0;
  border-radius: 0 4px 4px 0;
}
.input-append .add-on, .input-append .btn, .input-append .btn-group {
  margin-left: -1px;
}
.input-append .add-on, .input-prepend .add-on, .input-append .btn, .input-prepend .btn, .input-append .btn-group > .dropdown-toggle, .input-prepend .btn-group > .dropdown-toggle {
  vertical-align: top;
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  border-radius: 0;
}
.input-append .add-on, .input-prepend .add-on {
  display: inline-block;
  width: auto;
  height: 20px;
  min-width: 16px;
  padding: 4px 5px;
  font-size: 14px;
  font-weight: normal;
  line-height: 20px;
  text-align: center;
  text-shadow: 0 1px 0 #ffffff;
  background-color: #eeeeee;
  border: 1px solid #ccc;
}
Inherited from 
.input-append, .input-prepend {
  margin-bottom: 5px;
  font-size: 0;
  white-space: nowrap;
}

.icon-time {
  background-position: -48px -24px;
}
[class^="icon-"], 

[class*=" icon-"] {
  display: inline-block;
  width: 14px;
  height: 14px;
  margin-top: 1px;
  line-height: 14px;
  vertical-align: text-top;
  background-image: url("imgs/glyphicons-halflings.png");
  background-position: 14px 14px;
  background-repeat: no-repeat;}
  
  .icon-white,
.nav-pills > .active > a > [class^="icon-"],
.nav-pills > .active > a > [class*=" icon-"],
.nav-list > .active > a > [class^="icon-"],
.nav-list > .active > a > [class*=" icon-"],
.navbar-inverse .nav > .active > a > [class^="icon-"],
.navbar-inverse .nav > .active > a > [class*=" icon-"],
.dropdown-menu > li > a:hover > [class^="icon-"],
.dropdown-menu > li > a:hover > [class*=" icon-"],
.dropdown-menu > .active > a > [class^="icon-"],
.dropdown-menu > .active > a > [class*=" icon-"],
.dropdown-submenu:hover > a > [class^="icon-"],
.dropdown-submenu:hover > a > [class*=" icon-"] {
  background-image: url("../img/glyphicons-halflings-white.png");

}
    </style>
       <?php require_once ('incs/inc-js.php');?>
       
    <script src="<?php echo URL_WEB;?>js/headroom.min.js"></script>
	<script src="<?php echo URL_WEB;?>js/jQuery.headroom.min.js"></script>
	<script src="<?php echo URL_WEB;?>js/principal.js"></script>

        <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
        
        
        <script>
    
    $('.google-maps').click(function () {
    $('.google-maps iframe').css("pointer-events", "auto");
});
    
    </script>
    
</head>

<body >
	<?php require_once ('incs/inc-pre-nav.php');?>
    <?php require_once ('incs/inc-nav2.php');?>
   
      
    <div class="container">
        
        <div id="servicios"   >
			<div class="container">
				<div class="row ">
					<div class="col-xs-12 col-sm-12 col-md-12 titulo-encabezado text-center">
						<h2><span>Contacto</span></h2>
						<p>Dejanos tu consulta. <span>Santa Rita</span> agradece tu visita.</p>	
						
					</div>
                </div>
                    
         	</div>
            <div><span class="divisor100"></span>	</div>
        </div>
        
      <div  class="row">
      	
      	<div class="container">
      	
	      	<div id="" class="col-sm-7 col-md-7 col-md-offset-1">
	         <p>Complete el formulario con sus datos y nos pondremos en contacto a la brevedad. Muchas gracias!</p>
	          <form role="form" id="feedbackForm">
	            <div class="form-group">
	              <label class="control-label" for="name">Nombre *</label>
	              <div class="input-group">
	                <input type="text" class="form-control" id="name" name="name" placeholder="Escriba su nombre" />
	                <span class="input-group-addon"><i class="glyphicon glyphicon-unchecked form-control-feedback"></i></span>
	              </div>
	              <span class="help-block" style="display: none;">Por favor escriba su nombre</span>
	            </div>
                      <div class="form-group">
	              <label class="control-label" for="phone">Telefono *</label>
	              <div class="input-group">
	                <input type="text" class="form-control" id="name" name="phone" placeholder="Telefono" />
	                <span class="input-group-addon"><i class="glyphicon glyphicon-unchecked form-control-feedback"></i></span>
	              </div>
	              <span class="help-block" style="display: none;">Por favor escriba su Telefono</span>
	            </div>
	          <!--  <div class="form-group">
	              <label class="control-label" for="phone">Tel&eacute;fono</label>
	              <input type="tel" class="form-control optional" id="phone" name="phone" placeholder="Ingrese un tel&eacute;fono de contacto (Opcional)"/>              
	            </div>-->
	  
	            <div class="form-group">
	              <label class="control-label" for="email">Razon de contacto *</label>
	              <select name="reason" class="form-control">
	                <option value="Consulta">Consulta</option>
	                <option value="Empresa">Empresa</option>
	                <option value="Viajes">Viajes</option>
	              </select>
	              <span class="help-block" style="display: none;">Escriba una dirección de Email válida.</span>
	            </div>
	            <div class="form-group">
	              <label class="control-label" for="email">Email *</label>
	              <div class="input-group">
	                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" />
	                <span class="input-group-addon"><i class="glyphicon glyphicon-unchecked form-control-feedback"></i></span>
	              </div>
	              <span class="help-block" style="display: none;">Escriba una dirección de Email válida.</span>
	            </div>
	            <div class="form-group">
	              <label class="control-label" for="message">Mensaje *</label>
	              <div class="input-group">
	                <textarea rows="5" cols="30" class="form-control" id="message" name="message" placeholder="Mensaje" ></textarea>
	                <span class="input-group-addon"><i class="glyphicon glyphicon-unchecked form-control-feedback"></i></span>
	              </div>
	              <span class="help-block" style="display: none;">Escriba un mensaje.</span>
	            </div>
	            <img id="captcha" src="lib/vender/securimage/securimage_show.php" alt="CAPTCHA Image" />
	            <a href="#" onclick="document.getElementById('captcha').src = 'lib/vender/securimage/securimage_show.php?' + Math.random(); return false" class="btn btn-info btn-sm">Cargar otra</a><br/>
	            <div class="form-group" style="margin-top: 10px;">
	              <label class="control-label" for="captcha_code">Texto sin Imagen *</label>
	              <div class="input-group">
	                <input type="text" class="form-control" name="captcha_code" id="captcha_code" placeholder="es humano?." />
	                <span class="input-group-addon"><i class="glyphicon glyphicon-unchecked form-control-feedback"></i></span>
	              </div>
	              <span class="help-block" style="display: none;">Introduzca el codigo de la imagen</span>
	            </div>
	            <span class="help-block" style="display: none;">Inserte el codigo de seguridad</span>
	            <button type="submit" id="feedbackSubmit" class="btn btn-primary btn-lg" data-loading-text="enviando..." style="">Enviar</button>
	          </form>
	          
	        </div><!--#contact_form-->
	        
	        <div class="col-xs-12 col-sm-4 col-md-4 ">
	        	<div class="col-md-12 margin-bottom-30">	        		
		        	<h4><i class="fa fa-info fa-fw"></i>Reservas e Informes</h4>
		        	<div><span class="divisor100"></span></div>
		        	<p><i class="fa fa-phone fa-fw"></i> Tel: (02223) - 443706 / 444640 </p>
		        	<p><i class="fa fa-phone fa-fw"></i> Nextel: 276*207</p>
	        	</div>
	        	 
	        	<div class="col-md-12 margin-bottom-30">	 
	        		<h4>Viajes especiales</h4>
	        		<div><span class="divisor100"></span></div>
	        		    
<a id='modalPresu' href="#"><span data-toggle="modal" data-target="#contact">Solicite Presupuesto</span></a>
 
	        	</div>
	        	<div class="col-md-12 ">	 
	        		<h4><i class="fa fa-clock-o fa-fw"></i>Horario de Atenci&oacute;n oficina</h4>
	        		<div><span class="divisor100"></span></div>
	        		<p>Lunes a Viernes: 07:30 a 21:00</p>
	      			<p>Sabados: 09:00 a 20:30</p>
	      			<p>Domingos y Feriados: 17:30 a 20:30</p> 
 
	        	</div>
	        	
	        	
	        </div>
      	</div>
      
          
      </div><!--/row-->
    
    </div><!--/.container-->
    
    <div class="google-maps">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d52182.227310071656!2d-58.233915!3d-35.17185075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95a2b62b6b035fd9%3A0xace318c4995fd0e!2sCnel.+Brandsen%2C+Buenos+Aires!5e0!3m2!1ses!2sar!4v1432692673405" height="300" frameborder="0" style="border:0"></iframe>       
    </div>  
                 
                 
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
   <?php require_once ('incs/footer4.php');?>  
    
    
    <script src="<?php echo URL_WEB;?>js/intlTelInput.min.js"></script>
    <script src="<?php echo URL_WEB;?>js/contact-form.js"></script>
      
   <script type="text/javascript">
         
    $(document).ready(function() {
            
    	$('#listanav-contacto').addClass('active');
            
    });

    </script>
    <script type="text/javascript">
      $("#modalPresu").click(function(ev)
      {
                    ev.preventDefault();
                    $("#idmodal").modal();
        }
                );
        
        </script>
        
   <script type="text/javascript">
            $(function() {
             $.fn.datetimepicker.defaults = {
                maskInput: true,           // disables the text input mask
                pickDate: true,            // disables the date picker
                pickTime: true,            // disables de time picker
                pick12HourFormat: false,   // enables the 12-hour format time picker
                pickSeconds: true,         // disables seconds in the time picker
                startDate: -Infinity,      // set a minimum date
                endDate: Infinity          // set a maximum date
};
  });
</script>
<script type="text/javascript"
     src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.min.js">
    </script>
    <script type="text/javascript"
     src="http://tarruda.github.com/bootstrap-datetimepicker/assets/js/bootstrap-datetimepicker.pt-BR.js">
    </script>
    
</body>

</html>
