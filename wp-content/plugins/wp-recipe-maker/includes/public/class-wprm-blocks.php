<?php
/**
 * Handle Gutenberg Blocks.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.1.2
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 */

/**
 * Handle Gutenberg Blocks.
 *
 * @since      3.1.2
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_Blocks {

	/**
	 * Register actions and filters.
	 *
	 * @since	3.1.2
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_recipe_block' ) );

		add_filter( 'block_categories', array( __CLASS__, 'block_categories' ) );
	}

	/**
	 * Register block categories.
	 *
	 * @since	3.2.0
	 * @param	array $categories Existing block categories.
	 */
	public static function block_categories( $categories ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'wp-recipe-maker',
					'title' => 'WP Recipe Maker',
				),
			)
		);
	}

	/**
	 * Register recipe block.
	 *
	 * @since	3.1.2
	 */
	public static function register_recipe_block() {
		if ( function_exists( 'register_block_type' ) ) {
			$block_settings = array(
				'attributes' => array(
                    'id' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
                    'template' => array(
                        'type' => 'string',
                        'default' => '',
                    ),
                    'updated' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
				),
				'render_callback' => array( __CLASS__, 'render_recipe_block' ),
			);
			register_block_type( 'wp-recipe-maker/recipe', $block_settings );

			$block_settings = array(
				'attributes' => array(
                    'id' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
                    'align' => array(
                        'type' => 'string',
                        'default' => 'left',
                    ),
				),
				'render_callback' => array( __CLASS__, 'render_nutrition_label_block' ),
			);
			register_block_type( 'wp-recipe-maker/nutrition-label', $block_settings );

			$block_settings = array(
				'attributes' => array(
                    'id' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
                    'text' => array(
                        'type' => 'string',
                        'default' => __( 'Jump to Recipe', 'wp-recipe-maker' ),
                    ),
				),
				'render_callback' => array( __CLASS__, 'render_jump_to_recipe_block' ),
			);
			register_block_type( 'wp-recipe-maker/jump-to-recipe', $block_settings );
			
			$block_settings = array(
				'attributes' => array(
                    'id' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
                    'text' => array(
                        'type' => 'string',
                        'default' => __( 'Jump to Video', 'wp-recipe-maker' ),
                    ),
				),
				'render_callback' => array( __CLASS__, 'render_jump_to_video_block' ),
			);
			register_block_type( 'wp-recipe-maker/jump-to-video', $block_settings );

			$block_settings = array(
				'attributes' => array(
                    'id' => array(
                        'type' => 'number',
                        'default' => 0,
                    ),
                    'text' => array(
                        'type' => 'string',
                        'default' => __( 'Print Recipe', 'wp-recipe-maker' ),
                    ),
				),
				'render_callback' => array( __CLASS__, 'render_print_recipe_block' ),
			);
			register_block_type( 'wp-recipe-maker/print-recipe', $block_settings );
        }
	}

	/**
	 * Render the recipe block.
	 *
	 * @since	3.1.2
	 * @param	mixed $atts Block attributes.
	 */
	public static function render_recipe_block( $atts ) {
		$output = '';

		// Only do this for the Gutenberg Preview.
		if ( isset( $GLOBALS['wp']->query_vars['rest_route'] ) && '/wp/v2/block-renderer/wp-recipe-maker/recipe' === $GLOBALS['wp']->query_vars['rest_route'] ) {
			if ( isset( $atts['template'] ) && $atts['template'] ) {
				$template = WPRM_Template_Manager::get_template_by_slug( $atts['template'] );
			} else {
				// Use default single recipe template.
				$template = WPRM_Template_Manager::get_template_by_type( 'single' );
				$atts['template'] = $template['slug'];
			}

			// Output style.
			if ( 'modern' === $template['mode'] ) {
				$output .= '<style type="text/css">' . WPRM_Template_Manager::get_template_css( $template ) . '</style>';
			}
		}

		$output .= WPRM_Shortcode::recipe_shortcode( $atts );

		return $output;
	}

	/**
	 * Render the nutrition label block.
	 *
	 * @since	3.1.2
	 * @param	mixed $atts Block attributes.
	 */
	public static function render_nutrition_label_block( $atts ) {
		return WPRM_SC_Nutrition_Label::shortcode( $atts );
	}

	/**
	 * Render the jump to recipe block.
	 *
	 * @since	3.1.2
	 * @param	mixed $atts Block attributes.
	 */
	public static function render_jump_to_recipe_block( $atts ) {
		return WPRM_SC_Jump::shortcode( $atts );
	}

	/**
	 * Render the jump to video block.
	 *
	 * @since	3.2.0
	 * @param	mixed $atts Block attributes.
	 */
	public static function render_jump_to_video_block( $atts ) {
		return WPRM_SC_Jump_Video::shortcode( $atts );
	}

	/**
	 * Render the print recipe block.
	 *
	 * @since	3.1.2
	 * @param	mixed $atts Block attributes.
	 */
	public static function render_print_recipe_block( $atts ) {
		return WPRM_SC_Print::shortcode( $atts );
	}
}

WPRM_Blocks::init();
