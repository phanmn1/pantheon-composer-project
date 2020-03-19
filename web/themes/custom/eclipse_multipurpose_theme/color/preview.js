/**
 * @file
 * Preview for the Bartik theme.
 */
 
(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.color = {
    logoChanged: false,
    callback: function (context, settings, $form) {

      // Change the logo to be the real one.
      if (!this.logoChanged) {
        $('.color-preview .color-preview-logo img').attr('src', drupalSettings.color.logo);
        this.logoChanged = true;
      }
      
      // Remove the logo if the setting is toggled off.
      if (drupalSettings.color.logo === null) {
        $('div').remove('.color-preview-logo');
      }

      var $colorPreview = $form.find('.color-preview');
      var $colorPalette = $form.find('.js-color-palette');

      // Global font color
      $colorPreview.css( 'color', $colorPalette.find('input[name="palette[globalfontcolor]"]').val());
      $colorPreview.css( 'background-color', $colorPalette.find('input[name="palette[bodybgcolor]"]').val());

      // Top Bar
      $colorPreview.find('.top-menu').css('background-color', $colorPalette.find('input[name="palette[topmenu]"]').val());
      $colorPreview.find('.top-menu').css('color', $colorPalette.find('input[name="palette[topmenucolor]"]').val());

      // Primary Color
      $colorPreview.find('.slider-description').css('background-color', $colorPalette.find('input[name="palette[slidersbg]"]').val());
      $colorPreview.find('.slider-description').css('color', $colorPalette.find('input[name="palette[slidercolor]"]').val());

      $colorPreview.find('h2.slider-title, .flexslider .more-link').css('background-color', $colorPalette.find('input[name="palette[slidercolor]"]').val());
      $colorPreview.find('h2.slider-title, .flexslider .more-link').css('color', $colorPalette.find('input[name="palette[slidersbg]"]').val());

      // $colorPreview.find('.slider-caption').css('background-color', $colorPalette.find('input[name="palette[slidersbg]"]').val());
      // $colorPreview.find('.slider-caption *').css('color', $colorPalette.find('input[name="palette[slidercolor]"]').val());

      $colorPreview.find('a').css('color', $colorPalette.find('input[name="palette[primarycolor]"]').val());

      // Top Widget
      $colorPreview.find('.topwidget .region').css( 'background-color', $colorPalette.find('input[name="palette[topwidgetbg]"]').val());
      $colorPreview.find('.topwidget .region').css( 'color', $colorPalette.find('input[name="palette[topwidgetcolor]"]').val());

      $colorPreview.find('.topwidget .region h2, .topwidget .region h3').css('color', $colorPalette.find('input[name="palette[topwidgetcolor]"]').val());

      // Featured
      $colorPreview.find('.featured').css('background-color', $colorPalette.find('input[name="palette[featuredbg]"]').val());
      $colorPreview.find('.featured').css('color', $colorPalette.find('input[name="palette[featuredcolor]"]').val());
      $colorPreview.find('.featured h2').css('color', $colorPalette.find('input[name="palette[featuredtitlecolor]"]').val());

      // Testimonials
      $colorPreview.find('.testimonials').css('background-color', $colorPalette.find('input[name="palette[testimonialsbg]"]').val());
      $colorPreview.find('.testimonials').css( 'color', $colorPalette.find('input[name="palette[testimonialscolor]"]').val());

      // Price highlight
      $colorPreview.find('.pricing_item.featured').css( 'background-color', $colorPalette.find('input[name="palette[pricehighlightbg]"]').val());  
      $colorPreview.find('.pricing_item.featured').css( 'color', $colorPalette.find('input[name="palette[pricehighlightcolor]"]').val());  

      // Footer Widget
      $colorPreview.find('.footerwidget').css('background-color', $colorPalette.find('input[name="palette[footerbg]"]').val());
      $colorPreview.find('.footerwidget').css('color', $colorPalette.find('input[name="palette[footercolor]"]').val());

      // Copyright
      $colorPreview.find('.copyright').css('background-color', $colorPalette.find('input[name="palette[copyrightbg]"]').val());
      $colorPreview.find('.copyright').css('color', $colorPalette.find('input[name="palette[copyrightcolor]"]').val());
      
      $colorPreview.find('a.btn').css('color', $colorPalette.find('input[name="palette[primarybtncolor]"]').val());
      $colorPreview.find('a.btn').css('background-color', $colorPalette.find('input[name="palette[primarycolor]"]').val());
      
    }
    
  };

})(jQuery, Drupal, drupalSettings);
