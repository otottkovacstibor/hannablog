<?php

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
 
if (!function_exists('suevafree_required_plugins')) {
	
	function suevafree_required_plugins() {

		$plugins = array(

			array(
				'name'      => 'SuevaFree Essential Kit',
				'slug'      => 'suevafree-essential-kit',
				'required'  => false,
			),

			array(
				'name'      => 'WIP Custom Login',
				'slug'      => 'wip-custom-login',
				'required'  => false,
			),

			array(
				'name'      => 'WIP WooCarousel Lite',
				'slug'      => 'wip-woocarousel-lite',
				'required'  => false,
			),
	
			array(
				'name'      => 'Widget Importer & Exporter',
				'slug'      => 'widget-importer-exporter',
				'required'  => false,
			),
	
			array(
				'name'      => 'Regenerate Thumbnails',
				'slug'      => 'regenerate-thumbnails',
				'required'  => false,
			),

		);
	
		tgmpa( $plugins );
	}
	
	add_action( 'tgmpa_register', 'suevafree_required_plugins' );

}

?>