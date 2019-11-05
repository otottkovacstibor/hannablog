/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {

	"use strict";

	function navigation(navigationID) {

		var container, button, dropdown, icon, screenreadertext, parentLink, menu, submenu, links, i, len;
	
		container = document.getElementById(navigationID);
		if ( ! container ) {
			return;
		}
	
		button = container.getElementsByTagName( 'button' )[0];
		if ( 'undefined' === typeof button ) {
			return;
		}
	
		menu = container.getElementsByTagName( 'nav' )[0];
	
		screenreadertext = document.createElement( 'span' );
		screenreadertext.classList.add( 'screen-reader-text' );
		screenreadertext.textContent = accessibleNavigationScreenReaderText.expandMain;
		button.appendChild( screenreadertext );
	
		parentLink = container.querySelectorAll( '.menu-item-has-children, .page_item_has_children' );
	
		for ( i = 0, len = parentLink.length; i < len; i++ ) {
			var dropdown = document.createElement( 'button' ),
				submenu = parentLink[i].querySelector( '.sub-menu' ),
				icon = document.createElement( 'span' ),
				screenreadertext = document.createElement( 'span' );
	
			icon.classList.add( 'dashicons' );
			icon.setAttribute( 'aria-hidden', 'true' );
	
			screenreadertext.classList.add( 'screen-reader-text' );
			screenreadertext.textContent = accessibleNavigationScreenReaderText.expandChild;
	
			parentLink[i].insertBefore( dropdown, submenu );
			dropdown.classList.add( 'dropdown-toggle' );
			dropdown.setAttribute( 'aria-expanded', 'false' );
			dropdown.appendChild( icon );
			dropdown.appendChild( screenreadertext );
	
			dropdown.onclick = function() {
				var parentLink = this.parentElement,
					submenu = parentLink.querySelector( '.sub-menu' ),
					screenreadertext = this.querySelector( '.screen-reader-text' );
	
				if ( -1 !== parentLink.className.indexOf( 'toggled-on' ) ) {
					parentLink.className = parentLink.className.replace( ' toggled-on', '' );
					this.setAttribute( 'aria-expanded', 'false' );
					submenu.setAttribute ( 'aria-expanded', 'false');
					screenreadertext.textContent = accessibleNavigationScreenReaderText.expandChild;
				} else {
					parentLink.className += ' toggled-on';
					this.setAttribute( 'aria-expanded', 'true' );
					submenu.setAttribute ( 'aria-expanded', 'true');
					screenreadertext.textContent = accessibleNavigationScreenReaderText.collapseChild;
				}
			};
		}
	
		// Hide menu toggle button if menu is empty and return early.
		if ( 'undefined' === typeof menu ) {
			button.style.display = 'none';
			return;
		}
	
		menu.setAttribute( 'aria-expanded', 'false' );
			if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
				menu.className += ' nav-menu';
			}
	
		button.onclick = function() {
			screenreadertext = this.querySelector( '.screen-reader-text' );
			if ( -1 !== container.className.indexOf( 'toggled' ) ) {
				container.className = container.className.replace( ' toggled', '' );
				button.setAttribute( 'aria-expanded', 'false' );
				screenreadertext.textContent = accessibleNavigationScreenReaderText.expandMain;
				menu.setAttribute( 'aria-expanded', 'false' );
			} else {
				container.className += ' toggled';
				button.setAttribute( 'aria-expanded', 'true' );
				screenreadertext.textContent = accessibleNavigationScreenReaderText.collapseMain;
				menu.setAttribute( 'aria-expanded', 'true' );
			}
		};
	
		// Get all the link elements within the primary menu.
	
		links = menu.getElementsByTagName( 'a' );
	
		// Each time a menu link is focused or blurred, toggle focus.
		for ( i = 0, len = links.length; i < len; i++ ) {
			links[i].addEventListener( 'focus', toggleFocus, true );
			links[i].addEventListener( 'blur', toggleFocus, true );
		}
	
		/**
		 * Sets or removes .focus class on an element.
		 */
		function toggleFocus() {
			var self = this;
	
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( -1 === self.className.indexOf( 'nav-menu' ) ) {
	
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					
					if ( -1 !== self.className.indexOf( 'focus' ) ) {
						self.className = self.className.replace( ' focus', '' );
					} else {
						self.className += ' focus';
					}
				}
				self = self.parentElement;
			}
		}

	}
	
	navigation('header');

} )();
