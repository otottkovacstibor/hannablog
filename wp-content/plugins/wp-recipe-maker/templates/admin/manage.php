<?php
/**
 * Template for the manage pages.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.9.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin
 */

// Subpage.
$sub = isset( $_GET['sub'] ) ? sanitize_key( wp_unslash( $_GET['sub'] ) ) : ''; // Input var okay.

$tabs = apply_filters( 'wprm_manage_tabs', array(
	'recipes' => __( 'Recipes', 'wp-recipe-maker' ),
	'ingredients' => __( 'Ingredients', 'wp-recipe-maker' ),
) );

if ( ! array_key_exists( $sub, $tabs ) ) {
	$sub = 'recipes';
}
?>

<div class="wrap wprm-manage">
	<h2><?php esc_html_e( 'Manage', 'wp-recipe-maker-premium' ); ?></h2>

	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab => $label ) {
			$url = add_query_arg( 'sub', $tab, admin_url( 'admin.php?page=wprecipemaker' ) );
			$active = $sub === $tab ? ' nav-tab-active' : '';

			echo '<a href="' . esc_url( $url ) . '" class="nav-tab' . esc_attr( $active ) . '">' . esc_html( $label ) . '</a>';
		}
		?>
	</h2>

	<?php do_action( 'wprm_manage_page', $sub ); ?>
</div>

<?php
$menu = WPRM_Modal::get_modal_menu();
require_once( WPRM_DIR . 'templates/admin/modal/modal.php' );
?>
