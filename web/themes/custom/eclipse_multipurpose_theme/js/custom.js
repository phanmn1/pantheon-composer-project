/** 
	* Filename:     custom.js
	* Version:      1.0.0 (2017-02-04)
	* Website:      http://www.zymphonies.com
	* Description:  Global Script
	* Author:		Zymphonies Team
					support@zymphonies.com
**/

function testimonials_owl(){
	jQuery('.views-field-field-testimonials .field-content').owlCarousel({
		items: 1,
		margin:10,
		dots: true,
		autoPlay: 3000,
		navigation : true,
		responsive : {
			500:{ items: 1, dots: true, navigation : true },
			700:{ items: 1, dots: true, navigation : true },
			900:{ items: 1, dots: true, navigation : true }
		}
	});
}

function clients_owl(){
	jQuery('.path-frontpage .field--name-field-clients').owlCarousel({
		items: 2,
		margin:10,
		dots: true,
		autoPlay: 3000,
		navigation : true,
		responsive : {
			500:{ items: 2, dots: true, navigation : true },
			700:{ items: 3, dots: true, navigation : true },
			900:{ items: 4, dots: true, navigation : true }
		}
	});
}

function innerpagebanner_owl(){
	jQuery('.field--name-field-banner').owlCarousel({
		items: 1,
		margin:10,
		dots: true,
		autoPlay: 3000,
		navigation : true,
	});
}

function job_application(){

	var applyBtn = jQuery('.field--name-field-apply-now- a');
	
	jQuery(applyBtn).text('Apply Now');
	
	jQuery(applyBtn).addClass('btn btn-success');

	// SAVE JOB CODE IN LOCALSTORAGE
	jQuery('.field--name-field-apply-now- a').click(function(){
		// STORE IN LOCALSTORAGE
		var jobCode = jQuery('.field--name-field-job-code .field__item').text();
		localStorage.setItem("jobCode", jobCode);
	});

	var getJobCode = localStorage.getItem("jobCode")
	jQuery('.field--name-field-job-code- input.form-text').val(getJobCode);
	jQuery('.contact-message-apply-now-form .form-actions .form-submit:first-child').val('Apply Now');
}

function gallery(){

	jQuery(".field--name-field-galleryimage .field__item a").colorbox({rel:'group1'});

	jQuery('.field--name-field-galleryimage .field__item').each(function(){
		var galleryImg = jQuery(this).find('img').attr('alt');
		jQuery(this).find('a').append('<i class="gallery-zoom fa fa-search"></i>' + '<h2 class="galleryimage-description">' + galleryImg + '</h2>');
	});

	jQuery('.views-field-field-galleryimage li').each(function(){
		var galleryImg = jQuery(this).find('img').attr('alt');
		jQuery(this).find('a').append('<i class="gallery-zoom fa fa-search"></i>' + '<h2 class="galleryimage-description">' + galleryImg + '</h2>');
	});
}

function mobile_menu(){

	// MOBILE MENU
	jQuery('.navbar-toggle').click(function(){
		jQuery('body').addClass('leftSpace');
		jQuery('.region-primary-menu').addClass('menuLeftSpace');
	});

	jQuery('.mobile-menu-close-btn').click(function(){
		jQuery('body').removeClass('leftSpace');
		jQuery('.region-primary-menu').removeClass('menuLeftSpace');
	});

	// MAIN MENU
	jQuery('#main-menu').smartmenus();

	// MOBILE DROPDOWN MENU
	if ( jQuery(window).width() < 767) {
		jQuery(".region-primary-menu li a:not(.has-submenu)").click(function () {
			jQuery('.region-primary-menu').hide();
	    });
	    jQuery('.wow').addClass('wow-removed').removeClass('wow');
	}
	else{
		jQuery('.wow-removed').addClass('wow').removeClass('wow-removed');
	}

}

function slider(){
	jQuery('.flexslider').flexslider({
    	animation: "slide"	
    });
}

wow = new WOW({
	boxClass: 'wow',
	animateClass: 'animated',
	offset: 0,
	mobile: false,
	live: true
});


// DOM READY
jQuery(document).ready(function(jQuery){	

	// REMOVE ANIMATION
	jQuery('.animation-off .wow, .not-front .wow').removeClass('wow');

	// ONLOAD FUNCTION
	wow.init();	
	testimonials_owl();
	clients_owl();
	innerpagebanner_owl();
	job_application();
	gallery();
	mobile_menu();	
	slider();
	
});

// WINDOW READY
jQuery(window).on('load', function() {
	jQuery(".pageloading").fadeOut(500);
});
