(function($, Drupal, drupalSettings) {
  Drupal.behaviors.scrolltop = {
    attach: function (context, settings) {
		 $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
		});

		$('.scrollup').click(function () {
			$("body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
				
		 }
    
  };
})(jQuery, Drupal, drupalSettings);