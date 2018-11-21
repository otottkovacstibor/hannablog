<?php
/**
 * Template to be used as a fallback when the plugin is deactivated.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/public
 */

?>
<!--WPRM Recipe <?php echo esc_html( $recipe->id() ); ?>-->
<?php
if ( count( $args ) > 0 ) {
	$fallback_args = array();
	foreach ( $args as $key => $value ) {
		$fallback_args[] = esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
	}
	echo '<!--' . implode( ' ', $fallback_args ) . '-->';
}
?>
<div class="wprm-fallback-recipe">
	<h2 class="wprm-fallback-recipe-name"><?php echo esc_html( $recipe->name() ); ?></h2>
	<?php
	if ( $recipe->image_url() ) {
		echo '<img class="wprm-fallback-recipe-image" src="' . esc_attr( $recipe->image_url() ) . '"/>';
	}
	?>
	<p class="wprm-fallback-recipe-summary">
		<?php echo wp_kses_post( $recipe->summary() ); ?>
	</p>
	<div class="wprm-fallback-recipe-ingredients">
		<?php
		$ingredients = $recipe->ingredients();

		foreach ( $ingredients as $ingredient_group ) {
			if ( $ingredient_group['name'] ) {
				echo '<h4>'. esc_html( $ingredient_group['name'] ) . '</h4>';
			}

			if ( count( $ingredient_group['ingredients'] ) > 0 ) {
				echo '<ul>';

				foreach ( $ingredient_group['ingredients'] as $ingredient ) {
					echo '<li>';
					if ( $ingredient['amount'] ) {
						echo esc_html( $ingredient['amount'] ) . ' ';
					}
					if ( $ingredient['unit'] ) {
						echo esc_html( $ingredient['unit'] ) . ' ';
					}
					echo esc_html( $ingredient['name'] );
					if ( $ingredient['notes'] ) {
						echo ' (' . esc_html( $ingredient['notes'] ) . ')';
					}
					echo '</li>';
				}

				echo '</ul>';
			}
		}
		?>
	</div>
	<div class="wprm-fallback-recipe-instructions">
		<?php
		$instructions = $recipe->instructions();

		foreach ( $instructions as $instruction_group ) {
			if ( $instruction_group['name'] ) {
				echo '<h4>'. esc_html( $instruction_group['name'] ) . '</h4>';
			}

			if ( count( $instruction_group['instructions'] ) > 0 ) {
				echo '<ol>';

				foreach ( $instruction_group['instructions'] as $instruction ) {
					echo '<li>';
					if ( $instruction['text'] ) {
						echo wp_kses_post( $instruction['text'] );
					}

					if ( $instruction['image'] ) {
						$thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
						$image_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';

						if ( $instruction['text'] && $image_url ) {
							echo '<br />';
						}

						if ( $image_url ) {
							echo '<img src="' . esc_attr( $image_url ) . '"/>';
							echo '<br />';
						}
					}
					echo '</li>';
				}

				echo '</ol>';
			}
		}
		?>
	</div>
	<div class="wprm-fallback-recipe-notes">
		<?php echo wp_kses_post( $recipe->notes() ); ?>
	</div>
</div>
<!--End WPRM Recipe-->
