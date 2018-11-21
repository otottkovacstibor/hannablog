<?php
/**
 * Template to be used for the print page.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.3
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/public
 */

?>
<!DOCTYPE html>
<html <?php echo get_language_attributes(); ?>>
	<head>
		<title><?php echo $recipe->name(); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="robots" content="noindex">
		<?php if ( WPRM_Settings::get( 'metadata_pinterest_disable_print_page' ) ) : ?>
			<meta name="pinterest" content="nopin" />
		<?php endif; ?>
		<?php wp_site_icon(); ?>


		<?php echo WPRM_Template_Manager::get_template_styles( $recipe, 'print' ); ?>
		<style>body { position: relative; padding-bottom: 30px; } #wprm-print-footer { position: absolute; bottom: 0; left: 0; right: 0; text-align: center; font-size: 0.8em; }</style>
		<link rel="stylesheet" type="text/css" href="<?php echo WPRM_URL . 'dist/public-' . WPRM_Settings::get( 'recipe_template_mode' ) . '.css?ver=' . WPRM_VERSION; ?>"/>
		<?php
		if ( WPRM_Addons::is_active( 'premium' ) ) :
			$filename = 'public-' . strtolower( WPRMP_BUNDLE );
		?>
			<link rel="stylesheet" type="text/css" href="<?php echo WPRMP_URL . 'dist/' . $filename . '.css?ver=' . WPRM_VERSION; ?>"/>
		<?php endif; ?>
		<?php if ( ! WPRM_Settings::get( 'print_show_recipe_image' ) ) : ?>
			<style>.wprm-recipe-image { display: none !important }</style>
		<?php endif; ?>
		<?php if ( ! WPRM_Settings::get( 'print_show_instruction_images' ) ) : ?>
			<style>.wprm-recipe-instruction-image { display: none !important }</style>
		<?php endif; ?>
		<?php echo WPRM_Assets::custom_css( 'print' ); ?>
		<?php if ( WPRM_Settings::get( 'print_css' ) ) : ?>
			<style><?php echo WPRM_Settings::get( 'print_css' ); ?></style>
		<?php endif; ?>


		<?php
		$scripts = '';
		if ( WPRM_Addons::is_active( 'premium' ) ) {
			$scripts .= '<script src="' . includes_url( '/js/jquery/jquery.js' ) . '"></script>';
			$scripts .= '<script>var wprmp_public = { settings : { recipe_template_mode: "' . WPRM_Settings::get( 'recipe_template_mode' ) . '", features_adjustable_servings : true } };</script>';
			$scripts .= '<script src="' . WPRMP_URL . 'dist/print.js"></script>';

			// Localize ingredients.
			$ingredients = $recipe->ingredients_without_groups();

			foreach ( (array) $ingredients as $key => $value ) {
				if ( ! is_scalar( $value ) ) continue;
				$ingredients[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
			}
			$scripts .= '<script>var wprmpuc_ingredients = ' . wp_json_encode( $ingredients ) . ';</script>';
			$scripts .= '<script>function set_print_servings(servings) { WPRecipeMakerPremium.print.set_print_servings(servings); }; function set_print_system(system) { WPRecipeMakerPremium.print.set_print_system(system); };</script>';
		} else {
			$scripts .= '<script>function set_print_servings(servings) {}; function set_print_system(system) {};</script>';
		}
		echo $scripts;
		?>
	</head>
	<body class="wprm-print<?php echo is_rtl() ? ' rtl' : ''; ?>" data-recipe=" <?php echo esc_attr( $recipe_id ); ?>">
		<?php echo WPRM_Template_Manager::get_template( $recipe, 'print' ); ?>
	</body>
</html>