<?php

class WPURP_Template_Recipe_Image extends WPURP_Template_Block {

    public $editorField = 'recipeImage';
    public $thumbnail;
    public $crop = false;

    public function __construct( $type = 'recipe-image' )
    {
        parent::__construct( $type );
    }

    public function thumbnail( $thumbnail )
    {
        $this->thumbnail = $thumbnail;
        return $this;
    }

    public function crop( $crop )
    {
        $this->crop = $crop;
        return $this;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        if( !isset( $this->thumbnail ) ) $this->thumbnail = 'full';

        // Post Image ID
        $custom_image_id = get_post_meta( $recipe->ID(), 'wpupg_custom_image_id', true );
        if( $args['template_type'] == 'recipe_grid' && $custom_image_id && get_post_type( $custom_image_id ) == 'attachment' ) {
            $post_image_id = $custom_image_id;
        } else {
            $post_image_id = $recipe->image_ID();
        }

        $thumb = $this->get_thumb( $recipe->ID(), $post_image_id, $this->thumbnail, $args );

        if(!$thumb) return ''; // No recipe image found

        $image_url = $thumb[0];
        if( is_null( $image_url ) ) return '';

        // Check image size unless a specific thumbnail was specified
        if( is_array( $this->thumbnail ) ) {

            $new_width = $this->thumbnail[0];
            $new_height = $this->thumbnail[1];

            if( $thumb[1] && $thumb[2] ) { // Use image size if passed along
                $width = $thumb[1];
                $height = $thumb[2];
            } else { // Or look it up for ourselves otherwise
                $size = getimagesize( $image_url );
                $width = $size[0];
                $height = $size[1];
            }

            // Don't distort the image
            $undistored_height = floor( $new_width * ( $height / $width ) );
            $this->add_style( 'height', $undistored_height.'px' );

            // Get correct thumbnail size
            $correct_thumb = array(
                $new_width,
                $undistored_height
            );

            $thumb = $this->get_thumb( $recipe->ID(), $post_image_id, $correct_thumb, $args );

            $image_url = $thumb[0];

            // Cropping the image
            if( $this->crop ) {
                $this->add_style( 'overflow', 'hidden', 'outer' );
                $this->add_style( 'max-width', $new_width.'px', 'outer' );
                $this->add_style( 'max-height', $new_height.'px', 'outer' );

                if( $new_height < $undistored_height ) {
                    $margin = -1 * ( 1 - $new_height / $undistored_height ) * 100/2;
                    $this->add_style( 'margin-top', $margin.'%' );
                    $this->add_style( 'margin-bottom', $margin.'%' );

                    $this->add_style( 'width', '100%' );
                    $this->add_style( 'height', 'auto' );
                } elseif ( $new_height > $undistored_height ) {
                    // We need a larger image
                    $larger_width = $new_height * ($new_width / $undistored_height);
                    $larger_thumb = array(
                        $larger_width,
                        $new_height
                    );

                    $thumb = $this->get_thumb( $recipe->ID(), $post_image_id, $larger_thumb, $args );

                    $image_url = $thumb[0];

                    $margin = ( $new_width - $larger_width ) / 2;
                    $this->add_style( 'margin-left', $margin.'px' );
                    $this->add_style( 'margin-right', $margin.'px' );
                    $this->add_style( 'width', $larger_width.'px' );
                    $this->add_style( 'max-width', $larger_width.'px' );
                    $this->add_style( 'height', $new_height.'px' );

                }
            }
        } else if( $this->thumbnail == 'full' ) {
            // Get better thumbnail size based on max possible block size
            $height = $args['max_height'] == 9999 ? 0 : $args['max_height'];
            $correct_thumb = array(
                $args['max_width'],
                $height,
            );

            $thumb = $this->get_thumb( $recipe->ID(), $post_image_id, $correct_thumb, $args );

            if( isset( $thumb[1] ) && $thumb[1] >= $args['max_width'] ) {
                $image_url = $thumb[0];
            }
        }

        $full_image_url = $recipe->image_url( 'full' );

        $args['desktop'] = $args['desktop'] && $this->show_on_desktop;
        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="image"' : '';

        $title_tag = WPUltimateRecipe::option( 'recipe_image_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $recipe->image_ID() ) ) : esc_attr( $recipe->title() );
        $alt_tag = WPUltimateRecipe::option( 'recipe_image_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $recipe->image_ID(), '_wp_attachment_image_alt', true ) ) : esc_attr( $recipe->title() );

        $output = $this->before_output();
        ob_start();
?>
<div<?php echo $this->style( 'outer' ); ?>>
    <?php if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) { ?>
        <a href="<?php echo $full_image_url; ?>" rel="lightbox" title="<?php echo $title_tag; ?>">
            <img src="<?php echo $image_url; ?>"<?php echo $meta; ?> alt="<?php echo $alt_tag; ?>" title="<?php echo $title_tag; ?>"<?php echo $this->style(); ?> />
        </a>
    <?php } else { ?>
        <img src="<?php echo $image_url; ?>"<?php echo $meta; ?> alt="<?php echo $alt_tag; ?>" title="<?php echo $title_tag; ?>"<?php echo $this->style(); ?> />
    <?php } ?>
</div>
<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }

    private function get_thumb( $post_id, $image_id, $size, $args )
    {
        $custom_image_url = get_post_meta( $post_id, 'wpupg_custom_image', true );
        $custom_image_size = $custom_image_url ? @getimagesize( $custom_image_url ) : array();

        if( $args['template_type'] == 'recipe_grid' && isset( $custom_image_size[0] ) && is_int( $custom_image_size[0] ) && isset( $custom_image_size[1] ) && is_int( $custom_image_size[1] ) ) {
            $thumb = array(
                $custom_image_url,
                $custom_image_size[0],
                $custom_image_size[1],
                false,
            );
        } else {
            $thumb = wp_get_attachment_image_src( $image_id, $size );
        }

        return $thumb;
    }
}