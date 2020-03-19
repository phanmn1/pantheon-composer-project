/* javascript hide/show menu
*/ 

(function ($) {

    var overallHeight = $(window).height();
	var overallWidth = $(window).width();
    
        $('.sidebarTab').click(function() {
            var sidebarTabIndex = $(this).index();
            console.log(sidebarTabIndex);
            if (!$(this).hasClass('sidebarTabOn') && sidebarTabIndex != 2) {
                $('.sidebarTabOn').removeClass('sidebarTabOn');
                $(this).addClass('sidebarTabOn');
                $(this).closest('.sidebar').removeClass('sidebar1 sidebar2 sidebar3 sidebar4').addClass('sidebar' + (sidebarTabIndex + 1));
                $('.sidebarContent').hide();
                $('.sidebarContent').eq(sidebarTabIndex).show();
            } else {
                // console.log(overallWidth)
                //if (overallWidth >=992 && overallWidth <= 1441) {
                    $(this).removeClass('sidebarTabOn');
                    $('.sidebarContent').eq(sidebarTabIndex).hide();
                //}
            }
        });

        $(".sidebarTab2").click(function(){
            window.location = '/user/login';
        })

        $(".sidebarTab3").click(function(){
            window.location = '/contact/request_login_form'
        })

        $('.scroll-down, .subbasin-anchor').click(function() {
            $('html, body').animate({
                scrollTop: $('.iframe-top').offset().top
            }, 1000)
        });
    
} (jQuery));