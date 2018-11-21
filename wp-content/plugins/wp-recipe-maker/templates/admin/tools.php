<?php
/**
 * Template for tools page.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin
 */

?>

<div class="wrap wprm-tools">
		<h1><?php esc_html_e( 'WP Recipe Maker Tools', 'wp-recipe-maker' ); ?></h1>
		<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Find Parent Posts', 'wp-recipe-maker' ); ?>
				</th>
				<td>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wprm_finding_parents' ) ); ?>" class="button" id="tools_finding_parents"><?php esc_html_e( 'Find Parent Posts', 'wp-recipe-maker' ); ?></a>
					<p class="description" id="tagline-tools_finding_parents">
						<?php esc_html_e( 'Go through all posts and pages on your website to find and link recipes to their parent.', 'wp-recipe-maker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Find Recipe Ratings', 'wp-recipe-maker' ); ?>
				</th>
				<td>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wprm_finding_ratings' ) ); ?>" class="button" id="tools_finding_ratings"><?php esc_html_e( 'Find Recipe Ratings', 'wp-recipe-maker' ); ?></a>
					<p class="description" id="tagline-tools_finding_ratings">
						<?php esc_html_e( 'Go through all recipes on your website to find any missing ratings.', 'wp-recipe-maker' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Reset Settings', 'wp-recipe-maker' ); ?>
				</th>
				<td>
				<a href="#" class="button" id="tools_reset_settings"><?php esc_html_e( 'Reset Settings to Default', 'wp-recipe-maker' ); ?></a>
					<p class="description" id="tagline-tools_reset_settings">
						<?php esc_html_e( 'Try using this if the settings page is not working at all.', 'wp-recipe-maker' ); ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
</div>
