<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<article <?php wc_product_class( 'product-container masonry-item col-md-4', $product); ?>>

	<div class="product-thumbnail">
        
        <?php echo suevafree_get_wc_product_thumbnail(); ?>
        
	</div>

    <div class="product-content">
    
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
        
			<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
        
 			<h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                
			<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
        
		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	
    </div>
    
</article>