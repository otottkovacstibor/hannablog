<?php
/**
 * Handle icons.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.3.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Handle icons.
 *
 * @since      3.3.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Icon {

	/**
	 * Get the icon.
	 *
	 * @since	3.3.0
	 * @param	mixed $keyword_or_url Keyword or URL for the icon.
	 * @param	mixed $color Color to return the icon in.
	 */
	public static function get( $keyword_or_url, $color = false ) {
		$icon = false;

		if ( ! $keyword_or_url ) {
			return $icon;
		}

		if ( file_exists( WPRM_DIR . 'assets/icons/' . $keyword_or_url . '.svg' ) ) {
			ob_start();
			include( WPRM_DIR . 'assets/icons/' . $keyword_or_url . '.svg' );
			$icon = ob_get_contents();
			ob_end_clean();
		} else {
			$icon = file_get_contents( $keyword_or_url );
		}

		if ( $color ) {
			$icon = preg_replace( '/#[0-9a-f]{3,6}/mi', $color, $icon );
		}

		return $icon;
	}
	
	/**
	 * Get all icons.
	 *
	 * @since	4.0.0
	 */
	public static function get_all() {
		$icons = array();

		$dir = WPRM_DIR . 'assets/icons';

		if ( $handle = opendir( $dir ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				preg_match( '/(.*?).svg$/', $file, $match );
				if ( isset( $match[1] ) ) {
					$id = $match[1];

					$icons[ $id ] = array(
						'id' => $id,
						'name' => ucwords( str_replace( '-', ' ', $id ) ),
						'url' => WPRM_URL . 'assets/icons/' . $match[0],
					);
				}
			}
		}

		return $icons;
	}
}
