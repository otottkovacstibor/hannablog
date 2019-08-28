<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* Socials */
/*-----------------------------------------------------------------------------------*/ 

if (!function_exists('suevafree_copyright_function')) {

	function suevafree_copyright_function() {

?>

		<p>

			<?php 
		
				if ( suevafree_setting('suevafree_copyright_text')) :
							
					echo wp_filter_post_kses(suevafree_setting('suevafree_copyright_text'));
								
				else:
							
					esc_html_e('Copyright ', 'suevafree');
					echo esc_html(get_bloginfo('name'));
					echo esc_html( date_i18n( __( ' Y', 'suevafree' )));
							
				endif;
							
			?>

			<a href="<?php echo esc_url('https://www.themeinprogress.com/'); ?>" target="_blank"><?php printf( esc_html__( ' | Theme by %s', 'suevafree' ), 'ThemeinProgress' ); ?></a>
			<a href="<?php echo esc_url('http://wordpress.org/'); ?>" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'suevafree' ); ?>" rel="generator"><?php printf( esc_html__( ' | Proudly powered by %s', 'suevafree' ), 'WordPress' ); ?></a>
                            
		</p>

<?php
		
	}
	
	add_action( 'suevafree_copyright', 'suevafree_copyright_function', 10, 2 );

}

?>