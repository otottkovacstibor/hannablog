<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'suevafree_admin_notice' ) ) {

	class suevafree_admin_notice {
	
		/**
		 * Constructor
		 */
		 
		public function __construct( $fields = array() ) {

			if ( 
				!get_option( 'suevafree-dismissed-notice') &&
				version_compare( PHP_VERSION, SUEVAFREE_MIN_PHP_VERSION, '>=' )
			) {

				add_action( 'admin_notices', array(&$this, 'admin_notice') );
				add_action( 'admin_head', array( $this, 'dismiss' ) );
			
			}

		}

		/**
		 * Dismiss notice.
		 */
		
		public function dismiss() {

			if ( isset( $_GET['suevafree-dismiss'] ) && check_admin_referer( 'suevafree-dismiss-action' ) ) {
		
				update_option( 'suevafree-dismissed-notice', intval($_GET['suevafree-dismiss']) );
				remove_action( 'admin_notices', array(&$this, 'admin_notice') );
				
			} 
		
		}

		/**
		 * Admin notice.
		 */
		 
		public function admin_notice() {
			
		?>
			
            <div class="notice notice-warning is-dismissible">
            
            	<p>
            
            		<strong>

						<?php
                        
                            esc_html_e( 'Upgrade to the premium version of Sueva, to enable 600+ Google Fonts, Unlimited sidebars, Portfolio section and much more. ', 'suevafree' ); 
                            
                            printf( 
                                '<a href="%1$s" class="dismiss-notice">' . esc_html__( 'Dismiss this notice', 'suevafree' ) . '</a>', 
                                esc_url( wp_nonce_url( add_query_arg( 'suevafree-dismiss', '1' ), 'suevafree-dismiss-action'))
                            );
                            
                        ?>
                    
                    </strong>
                    
            	</p>
                    
            	<p>
            	
            		<a target="_blank" href="<?php echo esc_url( 'https://www.themeinprogress.com/sueva/?ref=2&campaign=sueva-notice' ); ?>" class="button button-primary"><?php esc_html_e( 'Upgrade to Sueva Premium', 'suevafree' ); ?></a>
                
            	</p>

            </div>
		
		<?php
		
		}

	}

}

new suevafree_admin_notice();

?>