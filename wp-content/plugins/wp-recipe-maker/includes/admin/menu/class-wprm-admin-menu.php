<?php
/**
 * Responsible for showing the WPRM menu in the WP backend.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/menu
 */

/**
 * Responsible for showing the WPRM menu in the WP backend.
 *
 * @since      1.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin/menu
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Admin_Menu {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu_page' ) );
	}

	/**
	 * Add WPRM to the wordpress menu.
	 *
	 * @since    1.0.0
	 */
	public static function add_menu_page() {
		// Base64 encoded svg icon.
		$icon = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDI0IDI0Ij48ZyA+DQo8cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNMTAsMEM5LjQsMCw5LDAuNCw5LDF2NEg3VjFjMC0wLjYtMC40LTEtMS0xUzUsMC40LDUsMXY0SDNWMWMwLTAuNi0wLjQtMS0xLTFTMSwwLjQsMSwxdjhjMCwxLjcsMS4zLDMsMywzDQp2MTBjMCwxLjEsMC45LDIsMiwyczItMC45LDItMlYxMmMxLjcsMCwzLTEuMywzLTNWMUMxMSwwLjQsMTAuNiwwLDEwLDB6Ii8+DQo8cGF0aCBkYXRhLWNvbG9yPSJjb2xvci0yIiBmaWxsPSIjZmZmZmZmIiBkPSJNMTksMGMtMy4zLDAtNiwyLjctNiw2djljMCwwLjYsMC40LDEsMSwxaDJ2NmMwLDEuMSwwLjksMiwyLDJzMi0wLjksMi0yVjENCkMyMCwwLjQsMTkuNiwwLDE5LDB6Ii8+DQo8L2c+PC9zdmc+';
		add_menu_page( 'WP Recipe Maker', 'WP Recipe Maker', WPRM_Settings::get( 'features_manage_access' ), 'wprecipemaker', array( 'WPRM_Manage', 'page_template' ), 'data:image/svg+xml;base64,' . $icon, 58 );
	}
}

WPRM_Admin_Menu::init();
