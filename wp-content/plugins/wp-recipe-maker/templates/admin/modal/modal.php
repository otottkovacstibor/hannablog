<?php
/**
 * Template for the WP Recipe Maker modal.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/modal
 */

?>

<div class="wprm-modal-container">
	<div class="wprm-modal wp-core-ui">
		<button type="button" class="button-link wprm-modal-close"><span class="wprm-modal-icon"><span class="screen-reader-text"><?php esc_html_e( 'Close Modal', 'wp-recipe-maker' ); ?></span></span></button>

		<div class="wprm-modal-content">
			<div class="wprm-frame wp-core-ui">
				<div class="wprm-frame-menu">
					<div class="wprm-menu">
						<?php
						$active_menu = '';
						foreach ( $menu as $menu_item => $options ) {
							$active = isset( $options['default'] ) && $options['default'];
							$active_class = $active ? ' active' : '';
							$label = isset( $options['label'] ) ? $options['label'] : '';
							$default_tab = isset( $options['default_tab'] ) ? $options['default_tab'] : '';

							if ( $active ) {
								$active_menu = $label;
							}

							echo '<a href="#" class="wprm-menu-item' . esc_attr( $active_class ) . '" data-menu="' . esc_attr( $menu_item ) . '" data-tab="' . esc_attr( $menu_item ) . '-' . esc_attr( $default_tab ) . '">' . esc_html( $label ) . '</a>';
						}
						?>
						<div class="wprm-menu-hidden">
							<?php echo esc_html__( "You're currently editing a recipe.", 'wp-recipe-maker' ) . ' ' . esc_html__( 'Use the "WP Recipe Maker" button to access other features.', 'wp-recipe-maker' ); ?>
						</div>
					</div>
				</div>
				<div class="wprm-frame-title">
					<h1><?php echo esc_html( $active_menu ); ?><span class="dashicons dashicons-arrow-down"></span></h1>
				</div>

				<div class="wprm-frame-router">
					<?php
					foreach ( $menu as $menu_item => $options ) {
						$active = isset( $options['default'] ) && $options['default'];
						$active_class = $active ? ' active' : '';
						$default_tab = $active && isset( $options['default_tab'] ) ? $options['default_tab'] : '';

						echo '<div id="wprm-menu-' . esc_attr( $menu_item ) . '" class="wprm-router' . esc_attr( $active_class ) . '">';

						foreach ( $options['tabs'] as $tab => $tab_options ) {
							$tab_uid = $menu_item . '-' . $tab;
							$tab_class = $default_tab === $tab ? ' active' : '';
							$label = isset( $tab_options['label'] ) ? $tab_options['label'] : '';
							$callback = isset( $tab_options['callback'] ) ? $tab_options['callback'] : '';
							$init = isset( $tab_options['init'] ) ? $tab_options['init'] : '';
							$button = isset( $tab_options['button'] ) ? $tab_options['button'] : __( 'Insert & Close', 'wp-recipe-maker' );

							echo '<a href="#" class="wprm-menu-item' . esc_attr( $tab_class ) . '" data-tab="' . esc_attr( $tab_uid ) . '" data-callback="' . esc_attr( $callback ) . '" data-init="' . esc_attr( $init ) . '" data-button="' . esc_attr( $button ) . '">' . esc_html( $label ) . '</a>';
						}

						echo '</div>';
					}
					?>
				</div>
				<div class="wprm-frame-content">
					<?php
					foreach ( $menu as $menu_item => $options ) {
						$active = isset( $options['default'] ) && $options['default'];
						$default_tab = $active && isset( $options['default_tab'] ) ? $options['default_tab'] : '';

						foreach ( $options['tabs'] as $tab => $tab_options ) {
							$tab_uid = $menu_item . '-' . $tab;
							$tab_class = $default_tab === $tab ? ' active' : '';
							$label = isset( $tab_options['label'] ) ? $tab_options['label'] : '';
							$template = isset( $tab_options['template'] ) ? $tab_options['template'] : '';

							echo '<div id="wprm-tab-' . esc_attr( $tab_uid ) . '" class="wprm-frame-content-tab' . esc_attr( $tab_class ) . '">';

							if ( file_exists( $template ) ) {
								include( $template );
							}

							echo '</div>';
						}
					}
					?>
				</div>

				<div class="wprm-frame-toolbar">
					<div class="wprm-toolbar">
						<div class="wprm-toolbar-primary search-form">
							<button type="button" class="button wprm-button button-primary button-large wprm-button-action"><?php esc_html_e( 'Insert & Close', 'wp-recipe-maker' ); ?></button>
							<button type="button" class="button wprm-button button-primary button-large wprm-button-action-save"><?php esc_html_e( 'Save', 'wp-recipe-maker' ); ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="wprm-modal-backdrop"></div>

</div>
