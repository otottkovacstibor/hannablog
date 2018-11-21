<?php
/**
 * Asks for feedback.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.27.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 */

/**
 * Asks for feedback.
 *
 * @since      1.27.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/admin
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Feedback {

	/**
	 * Register actions and filters.
	 *
	 * @since    1.27.0
	 */
	public static function init() {
		add_action( 'wprm_modal_notice', array( __CLASS__, 'modal_notice' ) );

		add_action( 'wp_ajax_wprm_feedback', array( __CLASS__, 'ajax_give_feedback' ) );
	}

	/**
	 * Show a notice in the modal.
	 *
	 * @since    1.27.0
	 */
	public static function modal_notice() {
		if ( current_user_can( 'manage_options' ) && '' === get_user_meta( get_current_user_id(), 'wprm_feedback', true ) ) {
			$count = wp_count_posts( WPRM_POST_TYPE )->publish;

			if ( 23 <= intval( $count ) ) {
				echo '<div class="wprm-feedback-notice">';
				echo '<strong>Wow, you\'ve published ' . esc_html( $count ) . ' recipes!</strong><br/>Are you enjoying our plugin so far?<br/>';
				echo '<button id="wprm-feedback-stop" class="button button-small">Stop asking me</button> <button id="wprm-feedback-no" class="button button-primary button-small">No...</button> <button id="wprm-feedback-yes" class="button button-primary button-small">Yes!</button>';
				echo '</div>';
			}
		}
	}

	/**
	 * Give feedback via AJAX.
	 *
	 * @since    1.27.0
	 */
	public static function ajax_give_feedback() {
		if ( check_ajax_referer( 'wprm', 'security', false ) ) {
			$answer = isset( $_POST['answer'] ) ? sanitize_text_field( wp_unslash( $_POST['answer'] ) ) : ''; // Input var okay.
			update_user_meta( get_current_user_id(), 'wprm_feedback', $answer );
		}

		wp_die();
	}
}

WPRM_Feedback::init();
