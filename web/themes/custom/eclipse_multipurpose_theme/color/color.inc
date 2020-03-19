<?php

/**
 * @file
 * Lists available colors and color schemes for the Bartik theme.
 */

$info = array(

  // Available colors and color labels used in theme.
  'fields' => array(
    'globalfontcolor'=> t('Global font color'),
    'bodybgcolor'=> t('Body background color'),
    'primarycolor'=> t('Primary color'),
    'primarycolordark'=> t('Primary hover color'),
    'primarybtncolor'=> t('Primary button text color'),    
    'topmenu' => t('Top bar background color'),
    'topmenucolor' => t('Top bar text color'),
    'socialmediacolor' => t('Social media color'),
    'slidersbg' => t('Slider background color'),
    'slidercolor' => t('Slider text color'),
    'topwidgetbg' => t('Top widget background color'),
    'topwidgetcolor' => t('Top widget text color'),
    'featuredbg' => t('Featured background color'),
    'featuredcolor' => t('Featured color'),
    'featuredtitlecolor' => t('Featured title color'),
    'testimonialsbg' => t('Testimonials background color'),
    'testimonialscolor' => t('Testimonials text color'),    
    'pricehighlightbg' => t('Price highlight background color'),
    'pricehighlightcolor' => t('Price highlight text color'),
    'footerbg' => t('Footer background color'),
    'footercolor' => t('Footer text color'),
    'copyrightbg' => t('Copyright background color'),
    'copyrightcolor' => t('Copyright text color'),
  ),
  // Pre-defined color schemes.
  'schemes' => array(
    'default' => array(
      'title' => t('Zymphonies (default)'),
      'colors' => array(
        'globalfontcolor'=> '#333333',
        'bodybgcolor'=> '#ffffff',
        'primarycolor'=> '#0072bc',
        'primarycolordark'=> '#0164a5',
        'primarybtncolor'=> '#fffffc',        
        'topmenu' => '#f9f9f9',
        'topmenucolor' => '#000000',
        'socialmediacolor' => '#303030',
        'slidersbg'=> '#0073bc',
        'slidercolor' => '#fefefe',
        'topwidgetbg'=> '#fdfdfd',
        'topwidgetcolor'=> '#3e3e3e',
        'featuredbg' => '#27262c',
        'featuredcolor' => '#949398',
        'featuredtitlecolor' => '#fafafa',
        'testimonialsbg' => '#0a66c2',
        'testimonialscolor' => '#fcfcfc',        
        'pricehighlightbg' => '#027ac7',
        'pricehighlightcolor' => '#fffdfb',
        'footerbg' => '#3a3a3a',
        'footercolor' => '#cbcbcb',
        'copyrightbg' => '#050505',
        'copyrightcolor' => '#afafaf',
      ),
    ),
    'blackandwhite' => array(
      'title' => t('Zymphonies - Black & White'),
      'colors' => array(
        'globalfontcolor'=> '#090909',
        'bodybgcolor'=> '#ffffff',
        'primarycolor'=> '#000000',
        'primarycolordark'=> '#181918',
        'primarybtncolor'=> '#fffffc',        
        'topmenu' => '#000000',
        'topmenucolor' => '#ffffff',
        'socialmediacolor' => '#ffffff',
        'slidersbg'=> '#3d3d3d',
        'slidercolor' => '#fefefe',
        'topwidgetbg'=> '#fdfdfd',
        'topwidgetcolor'=> '#3e3e3e',
        'featuredbg' => '#27262c',
        'featuredcolor' => '#ffffff',
        'featuredtitlecolor' => '#fafafa',
        'testimonialsbg' => '#545454',
        'testimonialscolor' => '#fcfcfc',        
        'pricehighlightbg' => '#464646',
        'pricehighlightcolor' => '#ffffff',
        'footerbg' => '#3a3a3a',
        'footercolor' => '#ffffff',
        'copyrightbg' => '#333333',
        'copyrightcolor' => '#afafaf',
      ),
    ),
    'nature' => array(
      'title' => t('Zymphonies - Nature'),
      'colors' => array(
        'globalfontcolor'=> '#090909',
        'bodybgcolor'=> '#ffffff',
        'primarycolor'=> '#148200',
        'primarycolordark'=> '#167305',
        'primarybtncolor'=> '#fffffc',        
        'topmenu' => '#0c4d00',
        'topmenucolor' => '#9effb5',
        'socialmediacolor' => '#9effb5',
        'slidersbg'=> '#6aff4d',
        'slidercolor' => '#005c10',
        'topwidgetbg'=> '#a9ff99',
        'topwidgetcolor'=> '#3e3e3e',
        'featuredbg' => '#116b00',
        'featuredcolor' => '#ffffff',
        'featuredtitlecolor' => '#fafafa',
        'testimonialsbg' => '#116b00',
        'testimonialscolor' => '#ffffff',        
        'pricehighlightbg' => '#26b70b',
        'pricehighlightcolor' => '#ffffff',
        'footerbg' => '#21a808',
        'footercolor' => '#ffffff',
        'copyrightbg' => '#0d4802',
        'copyrightcolor' => '#ffffff',
      ),
    ),
    'reddish' => array(
      'title' => t('Zymphonies - Reddish'),
      'colors' => array(
        'globalfontcolor'=> '#090909',
        'bodybgcolor'=> '#ededed',
        'primarycolor'=> '#d10200',
        'primarycolordark'=> '#8a0100',
        'primarybtncolor'=> '#fffffc',        
        'topmenu' => '#6b0b00',
        'topmenucolor' => '#ffffff',
        'socialmediacolor' => '#ffffff',
        'slidersbg'=> '#c21d00',
        'slidercolor' => '#ffffff',
        'topwidgetbg'=> '#dbdbdb',
        'topwidgetcolor'=> '#3e3e3e',
        'featuredbg' => '#380100',
        'featuredcolor' => '#ffffff',
        'featuredtitlecolor' => '#fafafa',
        'testimonialsbg' => '#d10200',
        'testimonialscolor' => '#ffffff',
        'pricehighlightbg' => '#d10200',
        'pricehighlightcolor' => '#ffffff',
        'footerbg' => '#990100',
        'footercolor' => '#ffffff',
        'copyrightbg' => '#4d0100',
        'copyrightcolor' => '#ffffff',
      ),
    ),
  ),

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => array(
    'css/colors.css',
  ),

  // Files to copy.
  'copy' => array(
    'logo.svg',
  ),

  // Preview files.
  'preview_library' => 'eclipse_multipurpose_theme/color.preview',
  'preview_html' => 'color/preview.html',

  // Attachments.
  '#attached' => [
    'drupalSettings' => [
      'color' => [
        // Put the logo path into JavaScript for the live preview.
        'logo' => theme_get_setting('logo.url', 'eclipse_multipurpose_theme'),
      ],
    ],
  ],
  
);
