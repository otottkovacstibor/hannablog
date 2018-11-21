jQuery.noConflict()(function($){

	"use strict";

/* ===============================================
   ColorPicker
   =============================================== */

	$('.suevafree_color_picker').wpColorPicker();

/* ===============================================
   On off
   =============================================== */

	$('.on-off').live("change",function() {
		
		if ($(this).val() === "on" ) { 
			$('.hidden-element').css({'display':'none'});
		} 
		else { 
			$('.hidden-element').slideDown("slow");
		} 
	
	}); 

	$('input[type="checkbox"].on_off').live("change",function() { 
	
		if (!this.checked) { 
			$(this).parent('.iPhoneCheckContainer').parent('.suevafree_inputbox').next('.hidden-element').slideUp("slow");
		} else { 
			$(this).parent('.iPhoneCheckContainer').parent('.suevafree_inputbox').next('.hidden-element').slideDown("slow");
		} 
	
	}); 
	
/* ===============================================
   Upload media
   =============================================== */

	$('.suevafree_inputbox input.upload_button').live("click", function(e) {

		var custom_uploader;
		var attachmentId = "";

		attachmentId = $(this).prev('.upload_attachment').attr("id");
		
		e.preventDefault();

		if (custom_uploader) {
			custom_uploader.open(this);
			return;
		}

		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});

		custom_uploader.on('select', function() {
			
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$('input#' + attachmentId ).val(attachment.url);
		
		});

		custom_uploader.open();

});

/* ===============================================
   Tabs
   =============================================== */

	$( "#tabs.suevafree_metaboxes" ).tabs();

});