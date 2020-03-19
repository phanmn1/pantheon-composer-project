(function($, Drupal, drupalSettings) {
  Drupal.behaviors.yourbehavior2 = {
    attach: function (context, settings) {
		 
			jQuery.getScript("https://code.jquery.com/ui/1.12.0/jquery-ui.js")
				//it worked! do something!
				.done(function(){
					
					$(".datepicker-class-link").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					});

					
					/*
					$("#qa-demo-to").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					});
					
					$("#qa-demo-from-modal").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					});
					$("#qa-demo-to-modal").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					});
					*/
				})

				//$(".timepicker-class-link").wickedpicker();
	 }
    
  };
})(jQuery, Drupal, drupalSettings);