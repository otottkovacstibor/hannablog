<?php
/**
 * Show a FAQ in the backend menu.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 */

/**
 * Show a FAQ in the backend menu.
 *
 * @since      1.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Admin_Menu_Faq {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'redirect' ) );
		add_action( 'admin_head-wp-recipe-maker_page_wprm_faq', array( __CLASS__, 'add_support_widget' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_submenu_page' ), 22 );
	}

	/**
	 * Redirect to FAQ page if this plugin was activated by itself.
	 *
	 * @since    1.2.0
	 */
	public static function redirect() {
		// Check if a single plugin was just activated.
		if ( isset( $_GET['activate'] ) ) { // Input var okay.
			// Make sure it was our plugin that was just activated.
			if ( get_option( 'wprm_activated', false ) ) {
				delete_option( 'wprm_activated' );

				wp_safe_redirect( admin_url( 'admin.php?page=wprm_faq' ) );
				exit();
			}
		}
	}

	/**
	 * Add our support widget to the page.
	 *
	 * @since    1.0.0
	 */
	public static function add_support_widget() {
		require_once( WPRM_DIR . 'templates/admin/menu/support-widget.php' );
	}

	/**
	 * Add the FAQ & Support submenu to the WPRM menu.
	 *
	 * @since    1.0.0
	 */
	public static function add_submenu_page() {
		add_submenu_page( 'wprecipemaker', __( 'FAQ & Support', 'wp-recipe-maker' ), __( 'FAQ & Support', 'wp-recipe-maker' ), 'manage_options', 'wprm_faq', array( __CLASS__, 'page_template' ) );
	}

	/**
	 * Get the template for this submenu.
	 *
	 * @since    1.0.0
	 */
	public static function page_template() {
		require_once( WPRM_DIR . 'templates/admin/menu/faq.php' );
	}
}

WPRM_Admin_Menu_Faq::init();
