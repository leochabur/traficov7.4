jQuery(document).ready(function($) {

	$(".headroom").headroom({
		"tolerance": 20,
		"offset": 50,
		"classes": {
			"initial": "animated",
			"pinned": "slideDown",
			"unpinned": "slideUp"
		},
		
	});
	
	//goto top
	$('.gototop').click(function(event) {
		event.preventDefault();
		$('html, body').animate({
			scrollTop: $("body").offset().top
		}, 500);
	});	
	
	$(this).on("scroll", function(){
		$(".contact-info ").hide();
		 if($(this).scrollTop() == 0){
			$(".contact-info ").show();
		}
	});


});