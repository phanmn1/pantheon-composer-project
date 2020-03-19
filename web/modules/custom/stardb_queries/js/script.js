(function($, Drupal, drupalSettings) {
  Drupal.behaviors.yourbehavior = {
    attach: function (context, settings) {
		 
			jQuery.getScript("https://code.jquery.com/ui/1.12.0/jquery-ui.js")
				//it worked! do something!
				.done(function(){
					$("#from-date").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					});
					$("#to-date").datepicker({
						showOn: "button",
					    buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
					    buttonImageOnly: true,
					    buttonText: "Select date", 
						changeMonth: true,
						changeYear: true
					}); 

					var url = $(location).attr('href');
					var regexURL = /adult_passage_counts/gi;

					if(regexURL.test(url)){
						console.log('Match')
						$('.sticky-header').css('font-size', '0.57em'); 
					}

				})

			
	 }
    
  };
})(jQuery, Drupal, drupalSettings);