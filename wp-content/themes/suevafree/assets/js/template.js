/*
 * jQuery SuevaFree theme functions file
 * https://www.themeinprogress.com
 *
 * Copyright 2019, ThemeinProgress
 * Licensed under MIT license
 * https://opensource.org/licenses/mit-license.php
 */

jQuery.noConflict()(function($){

	"use strict";

/* ===============================================
   FIXED HEADER
   =============================================== */

	function suevafree_header() {
		
		var body_width = $(window).width();
		
		var header = $('#header-wrapper.fixed-header .suevafree-general-menu ul').innerHeight();
		var submenu = $('.suevafree-menu ul > li').innerHeight();
		var demostore = $('p.demo_store').innerHeight();
		var adminbar = $('body.logged-in #wpadminbar').innerHeight();
		
		if ( body_width >= 992 ){
		
			$('#header-wrapper.fixed-header').css({'height': header + demostore});
			$('#header-wrapper.fixed-header #logo').css({'line-height': header + 'px' });
			$('#header-wrapper.fixed-header #logo').css({'max-height': header + 'px' });
			$('#header-wrapper.fixed-header #header').css({'height': header});
			$('#header-wrapper.fixed-header .suevafree-general-menu').css({'height': header});
			$('.suevafree-menu ul > li > ul').css({'top': submenu });
			$('#header-wrapper.fixed-header #header').css({'top': demostore + adminbar });

		} else {
			
			$('#header-wrapper.fixed-header').css({'height':'auto'});
			$('#header-wrapper.fixed-header #logo').css({'line-height': '90px' });
			$('#header-wrapper.fixed-header #logo').css({'max-height': '90px' });
			$('#header-wrapper.fixed-header #header').css({'top': 0 });
			$('#header-wrapper.fixed-header #header').css({'height':'auto'});
			$('#header-wrapper.fixed-header .suevafree-general-menu').css({'height':'auto'});
		
		}

	}
	
	$( window ).load(suevafree_header);
	$( window ).resize(suevafree_header);

/* ===============================================
   BODY NICESCROLL /
   =============================================== */

	function suevafree_bodynicescroll() {
	
		$("body.nicescroll").niceScroll({
			cursorwidth: "12px",
			background: "#fafafa",
			cursorcolor: "#c1c1c1",
			scrollspeed:100,
			zindex:999999,
			railpadding: {
				top: 0,
				left: 0,
				bottom: 0,
				right: 0
			}
		});
	
	}

/* ===============================================
   HEADER /
   =============================================== */

	$(window).load(function() {

		suevafree_bodynicescroll();
		
		$("#scroll-sidebar").niceScroll(".wrap", {
			cursorwidth: "5px",
			cursorborder: "1px solid #333",
			railpadding: {
				top: 0,
				left: 0,
				bottom: 0,
				right: 0
			}
		});

		$('.suevafree-vertical-menu ul > li .sf-sub-indicator, .suevafree-vertical-menu ul > li > ul > li .sf-sub-indicator ').click(function(){
			setTimeout(function(){
			  $("#scroll-sidebar").getNiceScroll().resize();
			}, 500);
		});

		var pw = $(window).width();
		
		$("#header .navigation").click(function() {

			$('#overlay-body').fadeIn(600).addClass('visible');
			$('body').addClass('overlay-active');
			$('#wrapper').addClass('open-sidebar');
			$("body.nicescroll").getNiceScroll().remove();

		});

		if ( pw < 992 ) {

			$("#sidebar-wrapper .navigation").click(function() {
	
				$('#overlay-body').fadeOut(600);
				$('body').removeClass('overlay-active');
				$('#wrapper').removeClass('open-sidebar');
				suevafree_bodynicescroll();

			});

			$("#overlay-body").swipe({
	
				swipeRight:function(event, direction, distance, duration, fingerCount) {
	
					$('#overlay-body').fadeOut(600);
					$('body').removeClass('overlay-active');
					$('#wrapper').removeClass('open-sidebar');
					suevafree_bodynicescroll();
				
				},
	
				threshold:0
		
			});
		
		} else if ( pw > 992 ) {

			$("#sidebar-wrapper .navigation, #overlay-body").click(function() {
	
				$('#overlay-body').fadeOut(600);
				$('body').removeClass('overlay-active');
				$('#wrapper').removeClass('open-sidebar');
				suevafree_bodynicescroll();

			});

		}
		
	});

/* ===============================================
   HEADER CART
   ============================================= */
	
	$('div.header-cart').hover(
		
		function () {
			$(this).children('div.header-cart-widget').stop(true, true).fadeIn(100);
		}, 
		function () {
			$(this).children('div.header-cart-widget').stop(true, true).fadeOut(400);		
		}
			
	);

/* ===============================================
   MOBILE MENU
   =============================================== */
	
	$(window).load(function() {

		$("#mobile-scroll").niceScroll(".mobilemenu-box", {
			cursorwidth: "5px",
			cursorborder: "1px solid #333",
			railpadding: {
				top: 0,
				left: 0,
				bottom: 0,
				right: 0
			}
		});

		$('.suevafree-vertical-menu ul > li .sf-sub-indicator, .suevafree-vertical-menu ul > li > ul > li .sf-sub-indicator ').click(function(){
			setTimeout(function(){
			  $("#mobile-scroll").getNiceScroll().resize();
			}, 500);
		});

		var pw = $(window).width();
		
		$("#header .mobile-navigation").click(function() {

			$('#overlay-body').fadeIn(600).addClass('visible');
			$('body').addClass('overlay-active');
			$('#wrapper').addClass('open-sidebar');
			$("body.nicescroll").getNiceScroll().remove();

		});

		if ( pw < 992 ) {

			$("#mobile-wrapper .mobile-navigation, #overlay-body").click(function() {
	
				$('#overlay-body').fadeOut(600);
				$('body').removeClass('overlay-active');
				$('#wrapper').removeClass('open-sidebar');
				suevafree_bodynicescroll();

			});

			$("#overlay-body").swipe({
	
				swipeRight:function() {
	
					$('#overlay-body').fadeOut(600);
					$('body').removeClass('overlay-active');
					$('#wrapper').removeClass('open-sidebar');
					suevafree_bodynicescroll();

				},
	
				threshold:0
		
			});
		
		} else if ( pw > 992 ) {

			$("#mobile-wrapper .mobile-navigation, #overlay-body").click(function() {
				$('#overlay-body').fadeOut(600);
				$('body').removeClass('overlay-active');
				$('#wrapper').removeClass('open-sidebar');
				suevafree_bodynicescroll();
			});

		}
		
	});

/* ===============================================
   TINYNAV MENU
   =============================================== */

	if ( $('.suevafree-general-menu ul:first .current-menu-item').length ) { 
	
		$('.tinynav-menu ul:first').tinyNav({
			active: 'current-menu-item',
		});

	} else {
	
		$('.tinynav-menu ul:first').tinyNav({
			header: 'Select an item',
		});

	}

/* ===============================================
   VERTICAL MENU
   =============================================== */

	$('.suevafree-vertical-menu ul > li').each(function(){
    	
		if( $('ul', this).length > 0 ) {
        
			$(this).children('a').append('<span class="sf-sub-indicator"> <i class="fa fa-plus"></i> </span>'); 
		
		}
	
	});
    
	$('.suevafree-vertical-menu ul > li .sf-sub-indicator, .suevafree-vertical-menu ul > li > ul > li .sf-sub-indicator ').click(function(e){

		e.preventDefault();

		if($(this).closest('a').next('ul.sub-menu').css('display') === 'none' ) {	

			$(this).html('<i class="fa fa-times"></i>');
			
		} else {	
			
			$(this).html('<i class="fa fa-plus"></i>');
			
		}
			
		$(this).closest('a').next('ul.sub-menu').stop(true,false).slideToggle('slow');

	});

/* ===============================================
   FOOTER
   =============================================== */

	function suevafree_footer() {
	
		var footer_h = $('#footer').innerHeight();
		$('#wrapper').css({'padding-bottom':footer_h});
	
	}
	
	$( window ).load(suevafree_footer);
	$( document ).ready(suevafree_footer);
	$( window ).resize(suevafree_footer);

/* ===============================================
   OVERLAY
   =============================================== */

	$('.gallery img').hover(function(){
		$(this).animate({ opacity: 0.50 },{queue:false});
	}, 
	function() {
		$(this).animate({ opacity: 1.00 },{queue:false});
	});
	

/* ===============================================
   Tipsy
   =============================================== */

	$('.social-buttons a').tipsy({fade:true, gravity:'s'});
	
/* ===============================================
   Scroll to Top Plugin
   =============================================== */

	$(window).scroll(function() {
		
		if( $(window).scrollTop() > 400 ) {
			$('#back-to-top').fadeIn(500);
		} else {
			$('#back-to-top').fadeOut(500);
		}
		
	});

	$('#back-to-top').click(function(){
		$.scrollTo(0, 'slow');
		return false;
	});

/* ===============================================
   MASONRY
   =============================================== */

	function suevafree_masonry() {

		$('.masonry').imagesLoaded(function () {

			$('.masonry').masonry({
				itemSelector: '.masonry-item',
				isAnimated: true
			});

		});

	}

/* ===============================================
   MASONRY
   =============================================== */
   
	$(document).ready(function(){
		suevafree_masonry();
	});

	$(window).resize(function(){
		suevafree_masonry();
	});
	
/* ===============================================
   Prettyphoto
   =============================================== */

	if( $().prettyPhoto ) {
		suevafree_lightbox();
	}

	function suevafree_lightbox() {
	
		$("a[data-rel^='prettyPhoto']").prettyPhoto({
			animationSpeed:'fast',
			slideshow:5000,
			theme:'pp_default',
			show_title:false,
			overlay_gallery: false,
			deeplinking: false,
			social_tools: false
		});
	
	}

/* ===============================================
   Swipebox gallery
   =============================================== */

	$(document).ready(function(){
		
		var counter = 0;

		$('div.gallery').each(function(){
			
			counter++;
			
			$(this).find('.swipebox').attr('data-rel', 'gallery-' + counter );
		
		});
		
		$('.swipebox').swipebox();

	});

});          