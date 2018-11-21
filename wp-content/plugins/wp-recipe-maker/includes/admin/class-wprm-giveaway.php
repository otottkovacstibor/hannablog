<?php
/**
 * Responsible for promoting the giveaway.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.11.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 */

/**
 * Responsible for promoting the giveaway.
 *
 * @since      1.11.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Giveaway {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.11.0
	 */
	public static function init() {
		$now = new DateTime();
		$giveaway_start = new DateTime( '2018-11-06 10:00:00', new DateTimeZone( 'Europe/Brussels' ) );
		$giveaway_end = new DateTime( '2018-11-22 10:00:00', new DateTimeZone( 'Europe/Brussels' ) );

		if ( $giveaway_start < $now && $now < $giveaway_end ) {
			add_action( 'admin_menu', array( __CLASS__, 'add_submenu_page' ), 99 );
			add_action( 'wprm_modal_notice', array( __CLASS__, 'modal_notice' ) );
		}
	}

	/**
	 * Add the Giveaway menu page.
	 *
	 * @since    1.11.0
	 */
	public static function add_submenu_page() {
		add_submenu_page( 'wprecipemaker', 'Giveaway', '~ Plugin Giveaway! ~', 'manage_options', 'wprm_giveaway', array( __CLASS__, 'page_template' ) );
	}

	/**
	 * Template for the giveaway page.
	 *
	 * @since    1.11.0
	 */
	public static function page_template() {
		echo '<div class="wrap">';
		echo '<h1>Plugin Giveaway</h1>';
		echo '<script src="https://static.airtable.com/js/embed/embed_snippet_v1.js"></script><iframe class="airtable-embed airtable-dynamic-height" src="https://airtable.com/embed/shrJNq8xN0gOqfEiY?backgroundColor=green" frameborder="0" onmousewheel="" width="100%" height="1535" style="background: transparent; border: 1px solid #ccc;"></iframe>';
		echo '</div>';
	}

	/**
	 * Show a notice in the modal.
	 *
	 * @since    1.11.0
	 */
	public static function modal_notice() {
		if ( ! WPRM_Addons::is_active( 'premium' ) ) {
			echo '<div class="wprm-giveaway-notice">';
			echo '<strong>Feeling lucky?</strong> Win plugins in our <a href="' . esc_url( admin_url( 'admin.php?page=wprm_giveaway' ) ) . '" target="_blank">Black Friday Giveaway</a>!';
			echo '</div>';
		}
	}
}

WPRM_Giveaway::init();
