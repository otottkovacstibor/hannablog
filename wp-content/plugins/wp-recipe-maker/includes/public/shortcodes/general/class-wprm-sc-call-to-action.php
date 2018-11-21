<?php
/**
 * Handle the Call to Action shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/general
 */

/**
 * Handle the Call to Action shortcode.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/general
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Call_to_Action extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-call-to-action';
	public static $attributes = array(
		'style' => array(
			'default' => 'simple',
			'type' => 'dropdown',
			'options' => array(
				'simple' => 'Simple'
			),
		),
		'padding' => array(
			'default' => '10px',
			'type' => 'size',
		),
		'margin' => array(
			'default' => '0px',
			'type' => 'size',
		),
		'background_color' => array(
			'default' => '',
			'type' => 'color',
		),
		'icon' => array(
			'default' => 'instagram',
			'type' => 'icon',
		),
		'icon_color' => array(
			'default' => '#333333',
			'type' => 'color',
			'dependency' => array(
				'id' => 'icon',
				'value' => '',
				'type' => 'inverse',
			),
		),
		'header_color' => array(
			'default' => '#333333',
			'type' => 'color',
		),
		'text_color' => array(
			'default' => '#333333',
			'type' => 'color',
		),
		'link_color' => array(
			'default' => '#3498db',
			'type' => 'color',
		),
		'header' => array(
			'default' => 'Tried this recipe?',
			'type' => 'text',
		),
		'action' => array(
			'default' => 'instagram',
			'type' => 'dropdown',
			'options' => array(
				'instagram' => 'Instagram',
				'twitter' => 'Twitter',
				'facebook' => 'Facebook',
				'pinterest' => 'Pinterest',
				'custom' => 'Custom Link',
			),
		),
		'social_text' => array(
			'default' => 'Mention %handle% or tag %tag%!',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
				'type' => 'inverse',
			),
		),
		'social_handle' => array(
			'default' => 'WPRecipeMaker',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
				'type' => 'inverse',
			),
		),
		'social_tag' => array(
			'default' => 'wprecipemaker',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
				'type' => 'inverse',
			),
		),
		'custom_text' => array(
			'default' => 'Check out %link%!',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
			),
		),
		'custom_link_url' => array(
			'default' => 'http://bootstrapped.ventures/wp-recipe-maker/',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
			),
		),
		'custom_link_text' => array(
			'default' => 'WP Recipe Maker',
			'type' => 'text',
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
			),
		),
		'custom_link_target' => array(
			'default' => '_blank',
			'type' => 'dropdown',
			'options' => array(
				'_self' => 'Open in same tab',
				'_blank' => 'Open in new tab',
			),
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
			),
		),
		'custom_link_nofollow' => array(
			'default' => 'dofollow',
			'type' => 'dropdown',
			'options' => array(
				'dofollow' => 'Do not add nofollow attribute',
				'nofollow' => 'Add nofollow attribute',
			),
			'dependency' => array(
				'id' => 'action',
				'value' => 'custom',
			),
		),
	);

	/**
	 * Output for the shortcode.
	 *
	 * @since	4.0.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		// Show teaser for Premium only shortcode in Template editor.
		$output = '';
		if ( ! WPRM_Addons::is_active( 'premium' ) ) {
			if ( ! $atts['is_template_editor_preview'] ) {
				return '';
			} else {
				$output .= '<div class="wprm-template-editor-premium-only">The Call to Action is only available in <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/">WP Recipe Maker Premium</a>.</div>';
			}
		}

		// Get optional icon.
		$icon = '';
		if ( $atts['icon'] ) {
			$icon = WPRM_Icon::get( $atts['icon'], $atts['icon_color'] );

			if ( $icon ) {
				$icon = '<span class="wprm-recipe-icon wprm-call-to-action-icon">' . $icon . '</span> ';
			}
		}

		// Custom container style.
		$style = '';
		$style .= 'color: ' . $atts['text_color'] . ';';
		$style .= $atts['background_color'] ? 'background-color: ' . $atts['background_color'] . ';' : '';
		$style .= 'margin: ' . $atts['margin'] . ';';
		$style .= 'padding-top: ' . $atts['padding'] . ';';
		$style .= 'padding-bottom: ' . $atts['padding'] . ';';

		// Output.
		$output .= '<div class="wprm-call-to-action wprm-call-to-action-' . $atts['style'] . '" style="' . $style . '">';
		$output .= $icon;
		$output .= '<span class="wprm-call-to-action-text-container">';

		// Optional Header.
		if ( $atts['header'] ) {
			$style = 'color: ' . $atts['header_color'] . ';';
			$output .= '<span class="wprm-call-to-action-header" style="' . $style . '">' . $atts['header'] . '</span>';
		}

		// Social URLs
		$social_urls = array(
			'instagram' => array(
				'handle' => 'https://www.instagram.com/',
				'tag' => 'https://www.instagram.com/explore/tags/',
			),
			'twitter' => array(
				'handle' => 'https://twitter.com/',
				'tag' => 'https://twitter.com/hashtag/',
			),
			'facebook' => array(
				'handle' => 'https://www.facebook.com/',
				'tag' => 'https://www.facebook.com/hashtag/',
			),
			'pinterest' => array(
				'handle' => 'https://www.pinterest.com/',
				'tag' => 'https://www.pinterest.com/search/pins/?rs=hashtag_closeup&q=%23',
			),
		);

		// Main CTA text.
		$output .= '<span class="wprm-call-to-action-text">';
		switch ( $atts['action'] ) {
			case 'instagram':
			case 'twitter':
			case 'facebook':
			case 'pinterest':
				$handle = $atts['social_handle'] ? '<a href="' . $social_urls[ $atts['action'] ]['handle'] . urlencode( $atts['social_handle'] ) . '" target="_blank" style="color: ' . $atts['link_color'] . '">@' . $atts['social_handle'] . '</a>' : '';
				$tag = $atts['social_tag'] ? '<a href="' . $social_urls[ $atts['action'] ]['tag'] . urlencode( $atts['social_tag'] ) . '" target="_blank" style="color: ' . $atts['link_color'] . '">#' . $atts['social_tag'] . '</a>' : '';

				$text = $atts['social_text'];
				$text = str_ireplace( '%handle%', $handle, $text );
				$text = str_ireplace( '%tag%', $tag, $text );

				$output .= $text;
				break;
			case 'custom':
				$url = $atts['custom_link_url'] ? esc_url_raw( $atts['custom_link_url'] ) : '#';
				$nofollow = 'nofollow' === $atts['custom_link_nofollow'] ? ' rel="nofollow"' : '';
				$link = $atts['custom_link_text'] ? '<a href="' . $url . '" target="' . $atts['custom_link_target']. '"' . $nofollow . '>' . $atts['custom_link_text'] . '</a>' : '';

				$text = $atts['custom_text'];
				$text = str_ireplace( '%link%', $link, $text );

				$output .= $text;
				break;
		}
		$output .= '</span>';

		$output .= '</span>';
		$output .= '</div>';

		return apply_filters( parent::get_hook(), $output, $atts );
	}
}

WPRM_SC_Call_to_Action::init();