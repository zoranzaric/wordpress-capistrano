/*
 * WPtouch 1.9 -The WPtouch Admin Javascript File
 * This file holds all the default jQuery & Ajax functions for the theme
 * Copyright (c) 2008-2009 Duane Storey & Dale Mugford (BraveNewCode Inc.)
 * Licensed under GPL.
 *
 * Last Updated: Novemeber 7th, 2009
 */

$j = jQuery.noConflict();
jQuery(document).ready(function($j) {

	new Ajax_upload('#upload_button', {
		action: '../?wptouch=upload',
		autoSubmit: true,
		name: 'submitted_file',
		onSubmit: function(file, extension) { $j("#upload_progress").show(); },
		onComplete: function(file, response) { $j("#upload_progress").hide();
		$j('#upload_response').hide().html(response).fadeIn(); }
	});
	
	setTimeout(function() { 	
		jQuery.ajax({
			url: "../?wptouch=plugins",
			success: function(data) {$j("#wptouch-plugin-content").html(data).fadeIn(350);}
		});
	}, 1500);

	setTimeout(function() { $j('img.ajax-load').fadeOut(750); }, 2000);

	setTimeout(function() { $j("#wptouch-news-content").fadeIn(350); }, 1050);
	setTimeout(function() { $j("#wptouch-support-content").fadeIn(350); }, 1350);

	setTimeout(function() { $j('#wptouchupdated').fadeIn(350); }, 750);
	setTimeout(function() { $j('#wptouchupdated').fadeOut(350); }, 1750);

	jQuery('#header-text-color, #header-background-color, #header-border-color, #link-color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).val(hex);
			jQuery(el).ColorPickerHide();
			},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor( jQuery(this).attr('value') );
			}
		});

	$j("a.fancylink").fancybox({
		'padding':						10,
		'zoomSpeedIn':				250, 
		'zoomSpeedOut':			250,
		'zoomOpacity':				true, 
		'overlayShow':				false,
		'frameHeight':				320,
		'frameWidth':				450,
		'hideOnContentClick': 	true
	});
});