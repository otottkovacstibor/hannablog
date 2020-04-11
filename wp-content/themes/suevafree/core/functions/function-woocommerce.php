<?php

/**
 * List of all WooCommerce hooks
 */

if ( ! function_exists( 'suevafree_woocommerce_init' ) ) {

	function suevafree_woocommerce_init() {
		
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		if ( suevafree_setting ('suevafree_woocommerce_cross_sell_cart') == "on" )
			add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
			
		if ( suevafree_setting ('suevafree_woocommerce_related_products') == "off" )
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		
		if ( suevafree_setting ('suevafree_woocommerce_upsell_products') == "off" )
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);

	}

	add_action( 'init', 'suevafree_woocommerce_init' );

}

/**
 * Woocommerce restore page templates
 */

if ( ! function_exists( 'suevafree_restore_page_templates' ) ) {

	function suevafree_restore_page_templates( $page_templates, $theme, $post ) {
		
		if ( suevafree_is_woocommerce_active() ) {
	
			$shop_page_id = wc_get_page_id( 'shop' );
	
			if ( $post && absint( $shop_page_id ) === absint( $post->ID ) ) {
				
				$page_templates = array(
					 'template-left-sidebar.php' => esc_html__( 'Left Sidebar','suevafree'),
					 'template-right-sidebar.php' => esc_html__( 'Right Sidebar','suevafree'),
					 'template-one-page.php' => 'One Page'
				 );
			}
	
		}
		
		return $page_templates;
	
	}

	add_filter( 'theme_page_templates', 'suevafree_restore_page_templates', 11, 3 );

}

/**
 * Woocommerce remove breadcrumbs
 */

if ( ! function_exists( 'suevafree_remove_breadcrumbs' ) ) {

	function suevafree_remove_breadcrumbs() {
    	
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
	
	}

	add_action( 'init', 'suevafree_remove_breadcrumbs' );

}

/**
 * Woocommerce header cart
 */

if ( ! function_exists( 'suevafree_header_cart' ) ) {

	function suevafree_header_cart() {

		if ( suevafree_is_woocommerce_active() && ( !suevafree_setting('suevafree_woocommerce_header_cart') || suevafree_setting('suevafree_woocommerce_header_cart') == "on" ) ) :
		
	?>

            <div class="header-cart">
            
                <a class="cart-contents" href="<?php echo esc_url(WC()->cart->get_cart_url()); ?>" title="<?php esc_attr_e( 'View your shopping cart','suevafree' ); ?>">
                    <i class="fa <?php echo esc_attr(suevafree_setting('suevafree_woocommerce_header_icon', 'fa-shopping-basket'));?>"></i>
                    <span class="cart-count"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->cart_contents_count, 'suevafree' ), WC()->cart->cart_contents_count ); ?></span>  
                </a>
                            
                <div class="header-cart-widget">
                
                    <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                
                </div>
                
            </div>
    
	<?php

		endif;

	}
	
}

if ( ! function_exists( 'suevafree_cart_link_fragment' ) ) {

	function suevafree_cart_link_fragment( $fragments ) {
	
		ob_start();

?>
		<a class="cart-contents" href="<?php echo esc_url(WC()->cart->get_cart_url()); ?>" title="<?php esc_attr_e( 'View your shopping cart','suevafree' ); ?>">
			<i class="fa <?php echo esc_attr(suevafree_setting('suevafree_woocommerce_header_icon', 'fa-shopping-basket'));?>"></i>
			<span class="cart-count"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->cart_contents_count, 'suevafree' ), WC()->cart->cart_contents_count ); ?></span>  
		</a>
        
<?php

		$fragments['a.cart-contents'] = ob_get_clean();
		
		return $fragments;
	
	}
	
	add_filter( 'woocommerce_add_to_cart_fragments', 'suevafree_cart_link_fragment' );

}

/**
 * Woocommerce before content
 */

if (!function_exists('suevafree_woocommerce_before_main_content')) {

	function suevafree_woocommerce_before_main_content() { 
	
		if ( is_product() ) {
			
			$classes = "product-wrapper" ;
	
		} else {
	
			$classes = "product-wrapper products-list" ;
	
		}

		do_action( 'suevafree_top_sidebar', 'top-sidebar-area');
		do_action( 'suevafree_header_sidebar', 'header-sidebar-area');
		
?>
	
	<div class="container">
	
		<div class="row">
		
			<div class="<?php echo suevafree_template('span') . " " . suevafree_template('sidebar') . " " . $classes; ?>" >
	
<?php
	
	}
	
	add_action('woocommerce_before_main_content', 'suevafree_woocommerce_before_main_content', 10);

}

/**
 * Woocommerce after content
 */

if (!function_exists('suevafree_woocommerce_after_main_content')) {
	
	function suevafree_woocommerce_after_main_content() { ?>
	
			</div>
			
			<?php 
			
				if ( suevafree_template('span') == "col-md-8" ) :

					do_action('suevafree_side_sidebar', 'side-sidebar-area' );
					
				endif;
				
			?>
	
		</div>
		
	</div>

<?php

		do_action( 'suevafree_full_sidebar', 'full-sidebar-area');

	}
	
	add_action('woocommerce_after_main_content', 'suevafree_woocommerce_after_main_content', 10);

}

/**
 * Replace woocommerce_get_product_thumbnail function
 */

if ( ! function_exists( 'suevafree_get_wc_product_thumbnail' ) ) {
	
	function suevafree_get_wc_product_thumbnail( $size = 'woocommerce_thumbnail', $deprecated1 = 0, $deprecated2 = 0 ) {
		
		global $post, $product;
		$imgSize = apply_filters( 'single_product_archive_thumbnail_size', $size);

		if ( $product ) {
			return (suevafree_setting('suevafree_linkable_product_thumbnails') == 'on') ? '<a href="' . get_permalink( $post->ID ) . '">' . $product->get_image( $imgSize ) . '</a>' : $product->get_image( $imgSize );
		} else {
			return '';
		}
		
	}
	
}

/**
 * Replace wc_get_gallery_image_html function
 */

if ( ! function_exists( 'suevafree_wc_get_gallery_image_html' ) ) {
	
	function suevafree_wc_get_gallery_image_html( $attachment_id, $main_image = false ) {
		$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
		$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
		$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
		$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
		$image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
			'title'                   => get_post_field( 'post_title', $attachment_id ),
			'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
			'data-src'                => $full_src[0],
			'data-large_image'        => $full_src[0],
			'data-large_image_width'  => $full_src[1],
			'data-large_image_height' => $full_src[2],
			'class'                   => $main_image ? 'wp-post-image' : '',
		) );

		if ( suevafree_setting('suevafree_gallery_lightbox', 'prettyphoto') == 'prettyphoto') :
			
			return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="product-thumbnail woocommerce-product-gallery__image"><a data-rel="prettyPhoto[product-gallery]" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
			
		else :
			
			return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="product-thumbnail woocommerce-product-gallery__image"><a class="swipebox" data-rel="[product-gallery]" href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
			
		endif;

	}

}

?>