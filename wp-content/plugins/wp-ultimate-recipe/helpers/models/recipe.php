<?php

class WPURP_Recipe {

    private $post;
    private $meta;
    private $fields = array(
        'recipe_custom_template',
        'recipe_title',
        'recipe_alternate_image',
        'recipe_description',
        'recipe_rating',
        'recipe_servings',
        'recipe_servings_type',
        'recipe_prep_time',
        'recipe_prep_time_text',
        'recipe_cook_time',
        'recipe_cook_time_text',
        'recipe_passive_time',
        'recipe_passive_time_text',
        'recipe_ingredients',
        'recipe_instructions',
        'recipe_notes',
        'recipe_video_id',
        'recipe_video_embed',
        'recipe_video_thumb',
    );

    public function __construct( $post )
    {
        // Get associated post
        if( is_object( $post ) && $post instanceof WP_Post ) {
            $this->post = $post;
        } else if( is_numeric( $post ) ) {
            $this->post = get_post( $post );
        } else {
            throw new InvalidArgumentException( 'Recipes can only be instantiated with a Post object or Post ID.' );
        }

        // Add custom fields to recipe fields if there are any
        $custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
        if( $custom_fields_addon ) {
            $fields = $this->fields;

            $custom_fields = $custom_fields_addon->get_custom_fields();

            foreach( $custom_fields as $key => $custom_field ) {
                $fields[] = $key;
            }

            $this->fields = $fields;
        }

        // Get metadata
        $this->meta = get_post_custom( $this->post->ID );
    }

    public function is_present( $field )
    {
        $nutrition_field = '';
        if( substr( $field, 0, 16 ) == 'recipe_nutrition' ) {
            $nutrition_field = substr( $field, 17 );
            $field = 'recipe_nutrition';
        }
        
        switch( $field ) {
            case 'recipe_image':
                return $this->image_ID();

            case 'recipe_featured_image':
                return get_post_thumbnail_id( $this->ID() ) != '';

            case 'recipe_ingredients':
                return $this->has_ingredients();

            case 'recipe_instructions':
                return $this->has_instructions();

            case 'recipe_post_content':
                return trim( $this->post_content() ) != '';

            case 'recipe_rating':
                if( WPUltimateRecipe::is_addon_active( 'user-ratings' ) && WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' ) != 'disabled' ) {
                    // Recipe rating is always present when user ratings are enabled
                    return true;
                } else {
                    // Not present if rating = 0 (not displayed)
                    $val = $this->meta($field);
                    return isset( $val ) && trim( $val ) != '' && $val != '0';
                }

            case 'recipe_rating_author':
                $val = $this->meta( 'recipe_rating' );
                return isset( $val ) && trim( $val ) != '' && $val != '0';

            case 'recipe_nutrition':
                $val = $this->nutritional( $nutrition_field );
                return isset( $val ) && trim( $val ) != '';

            default:
                $val = $this->meta($field);
                return isset( $val ) && trim( $val ) != '';
        }
    }

    public function meta( $field )
    {
        if( isset( $this->meta[$field] ) ) {
            return $this->meta[$field][0];
        }

        return null;
    }

    public function fields()
    {
        return $this->fields;
    }

    public function output( $type = 'recipe', $template = 'default' )
    {
        if( $type == 'recipe' && $template == 'default' && !is_null( $this->template() ) ) {
            $template = $this->template();
        }

        $template = WPUltimateRecipe::get()->template( $type, $template );
        $template->output( $this, $type );
    }

    public function output_string( $type = 'recipe', $template = 'default' )
    {
        ob_start();
        $this->output( $type, $template );
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function has_ingredients()
    {
        $ingredients = $this->ingredients();
        return !empty( $ingredients );
    }

    public function has_instructions()
    {
        $instructions = $this->instructions();
        return !empty( $instructions );
    }

    public function get_time_meta_string( $amount, $unit )
    {
        $meta = false;
        $amount = floatval( $amount );

        if( strtolower( $unit ) == strtolower( __( 'minute', 'wp-ultimate-recipe' ) )
            || strtolower( $unit ) == strtolower( __( 'minutes', 'wp-ultimate-recipe' ) )
            || strtolower( $unit ) == 'min'
            || strtolower( $unit ) == 'mins' ) {
            $meta = 'PT' . $amount . 'M';
        } elseif( strtolower( $unit ) == strtolower( __( 'hour', 'wp-ultimate-recipe' ) )
            || strtolower( $unit ) == strtolower( __( 'hours', 'wp-ultimate-recipe' ) )
            || strtolower( $unit ) == 'hr'
            || strtolower( $unit ) == 'hrs' ) {
            $meta = 'PT' . $amount . 'H';
        }
        
        return $meta;
    }

    // Ingredient Fields

    public function alternate_image()
    {
        return $this->meta( 'recipe_alternate_image' );
    }

    public function alternate_image_url( $type )
    {
        $thumb = wp_get_attachment_image_src( $this->alternate_image(), $type );
        return $thumb ? $thumb['0'] : '';
    }

    public function author()
    {
        $author_id = $this->post->post_author;

        if( $author_id == 0 ) {
            return $this->meta( 'recipe-author' );
        } else {
            $author = get_userdata( $this->post->post_author );

            return $author->data->display_name;
        }
    }

    public function cook_time()
    {
        return $this->meta( 'recipe_cook_time' );
    }

    public function cook_time_meta()
    {
        $meta = false;

        $amount = esc_attr( $this->cook_time() );
        $unit = strtolower( $this->cook_time_text() );

        $meta = $this->get_time_meta_string( $amount, $unit );

        return $meta;
    }

    public function cook_time_text()
    {
        return $this->meta( 'recipe_cook_time_text' );
    }

    public function date()
    {
        return $this->post->post_date;
    }

    public function description()
    {
        return $this->meta( 'recipe_description' );
    }

    public function excerpt()
    {
        return $this->post->post_excerpt;
    }

    public function featured_image()
    {
        return get_post_thumbnail_id( $this->ID() );
    }

    public function featured_image_url( $type )
    {
        $thumb = wp_get_attachment_image_src( $this->featured_image(), $type );
        return $thumb['0'];
    }

    public function ID()
    {
        return $this->post->ID;
    }

    public function image_url( $type )
    {
        $thumb = wp_get_attachment_image_src( $this->image_ID(), $type );
        return $thumb['0'];
    }

    public function image_ID()
    {
        if( WPUltimateRecipe::option( 'recipe_alternate_image', '1' ) == '1' ) {
            $image_id = $this->alternate_image() ? $this->alternate_image() : $this->featured_image();
        } else {
            $image_id = $this->featured_image();
        }
        return $image_id;
    }

    public function ingredients()
    {
        $ingredients = @unserialize( $this->meta( 'recipe_ingredients' ) );

        // Try to fix serialize offset issues
        if( $ingredients === false ) {
            $ingredients = unserialize( preg_replace_callback ( '!s:(\d+):"(.*?)";!', array( $this, 'regex_replace_serialize' ), $this->meta( 'recipe_ingredients' ) ) );
        }

        return apply_filters( 'wpurp_recipe_field_ingredients', $ingredients, $this );
    }

    public function instructions()
    {
        $instructions = @unserialize( $this->meta( 'recipe_instructions' ) );

        // Try to fix serialize offset issues
        if( $instructions === false ) {
            $instructions = unserialize( preg_replace_callback ( '!s:(\d+):"(.*?)";!', array( $this, 'regex_replace_serialize' ), $this->meta( 'recipe_instructions' ) ) );
        }

        return apply_filters( 'wpurp_recipe_field_instructions', $instructions, $this );
    }

    public function regex_replace_serialize( $match )
    {
        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    }

    public function link()
    {
        return get_permalink( $this->ID() );
    }

    public function link_print()
    {
        $keyword = urlencode( WPUltimateRecipe::option( 'print_template_keyword', 'print' ) );
        if( strlen( $keyword ) <= 0 ) {
            $keyword = 'print';
        }

        $link = $this->link();

        if( get_option('permalink_structure') ) {
            if( substr( $link, -1) != '/' ) {
                $link .= '/';
            }
            $link .= $keyword;

            // Make sure slug is present
            if( WPUltimateRecipe::option( 'remove_recipe_slug', '0' ) == '1' ) {

                $recipe_slug = $this->post->post_name;
                $post_type_slug = WPUltimateRecipe::option( 'recipe_slug', 'recipe' );
                $link = str_replace( '/' . $recipe_slug . '/', '/' . $post_type_slug . '/' . $recipe_slug . '/', $link );
            }
        } else {
            $link .= '&' . $keyword;
        }

        return $link;
    }

    public function notes()
    {
        return $this->meta( 'recipe_notes' );
    }

    public function nutritional( $field = false )
    {
        $nutritional = apply_filters( 'wpurp_recipe_field_nutritional', unserialize( $this->meta( 'recipe_nutritional' ) ), $this );

        if( $field ) {
            $nutritional = isset( $nutritional[$field] ) ? $nutritional[$field] : '';
        }

        return $nutritional;
    }

    public function passive_time()
    {
        return $this->meta( 'recipe_passive_time' );
    }

    public function passive_time_text()
    {
        return $this->meta( 'recipe_passive_time_text' );
    }

    public function post_content()
    {
        return $this->post->post_content;
    }

    public function post_status()
    {
        return $this->post->post_status;
    }

    public function prep_time()
    {
        return $this->meta( 'recipe_prep_time' );
    }

    public function prep_time_meta()
    {
        $meta = false;

        $amount = esc_attr( $this->prep_time() );
        $unit = strtolower( $this->prep_time_text() );

        $meta = $this->get_time_meta_string( $amount, $unit );

        return $meta;
    }

    public function prep_time_text()
    {
        return $this->meta( 'recipe_prep_time_text' );
    }

    public function rating()
    {
        if( WPUltimateRecipe::is_addon_active( 'user-ratings' ) && WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' ) != 'disabled' ) {
            $user_rating = WPURP_User_Ratings::get_recipe_rating( $this->ID() );
            return $user_rating['rating'];
        } else {
            return $this->rating_author();
        }
    }

    public function rating_author()
    {
        return $this->meta( 'recipe_rating' );
    }

    public function servings()
    {
        return $this->meta( 'recipe_servings' );
    }

    public function servings_normalized()
    {
        return $this->meta( 'recipe_servings_normalized' );
    }

    public function servings_type()
    {
        return $this->meta( 'recipe_servings_type' );
    }

    public function template()
    {
        $template = $this->meta( 'recipe_custom_template' );
        return is_null( $template ) ? 'default' : $template;
    }

    public function terms()
    {
        return unserialize( $this->meta( 'recipe_terms' ) );
    }

    public function terms_with_parents()
    {
        return unserialize( $this->meta( 'recipe_terms_with_parents' ) );
    }

    public function title()
    {
        if ( $this->meta( 'recipe_title' ) ) {
            return $this->meta( 'recipe_title' );
        } else {
            return $this->post->post_title;
        }
    }
    
    public function has_video()
    {
        return $this->video_id() || $this->video_embed() ? true : false;
    }

    public function video_id()
    {
        $video_id = $this->meta( 'recipe_video_id' );
        return $video_id ? $video_id : '';
    }

    public function video_embed()
    {
        $video_embed = $this->meta( 'recipe_video_embed' );
        return $video_embed ? $video_embed : '';
    }

    public function video_thumb( $show_image = false )
    {
        $video_thumb = $this->meta( 'recipe_video_thumb' );
        
        if ( $show_image ) {
            return $video_thumb ? '<img src="' . $video_thumb . '">' : '';
        } else {
            return $video_thumb ? $video_thumb : '';
        }
    }

    public function video_data() {
		return wp_get_attachment_metadata( $this->video_id() );
    }
    
    public function video_url() {
		return wp_get_attachment_url( $this->video_id() );
    }
    
    public function video_thumb_url( $size = 'thumbnail' ) {
		$image_id = get_post_thumbnail_id( $this->video_id() );
		$thumb = wp_get_attachment_image_src( $image_id, $size );
        $image_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';

		return $image_url;
	}

    public function video()
    {
        if ( ! $this->has_video() ) {
            return '';
        }

        if ( $this->video_id() ) {
            $video_data = $this->video_data();
            $output = '[video';
            $output .= ' width="' . $video_data['width'] . '"';
            $output .= ' height="' . $video_data['height'] . '"';

            $format = isset( $video_data['fileformat'] ) && $video_data['fileformat'] ? $video_data['fileformat'] : 'mp4';
            $output .= ' ' . $format . '="' . $this->video_url() . '"';

            $thumb_size = array( $video_data['width'], $video_data['height'] );
            $thumb_url = $this->video_thumb_url( $thumb_size );

            if ( $thumb_url ) {
                $output .= ' poster="' . $thumb_url . '"';
            }
            $output .= '][/video]';
        } else {
            $embed_code = $this->video_embed();

			// Check if it's a regular URL.
			$url = filter_var( $embed_code, FILTER_SANITIZE_URL );

			if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
				global $wp_embed;

				if ( isset( $wp_embed ) ) {
					$output = $wp_embed->run_shortcode( '[embed]' . $url . '[/embed]' );
				}
			} else {
				$output = $embed_code;
			}
        }

        return do_shortcode( $output );
    }

    public function video_metadata() {
        $metadata = maybe_unserialize( $this->meta( 'recipe_video_metadata', '' ) );
		$metadata_updated = intval( $this->meta( 'recipe_video_metadata_updated', '' ) );

		if ( ! $metadata || ( time() - $metadata_updated > 60 * 60 * 24 * 7 ) ) {
			$metadata = WPURP_Metadata_Video::get_video_metadata_for_recipe( $this );

			// Cache for reuse. Automatically cleared when resaving a recipe.
			if ( $metadata ) {
				update_post_meta( $this->id(), 'recipe_video_metadata', $metadata );
				update_post_meta( $this->id(), 'recipe_video_metadata_updated', time() );
			}
        }

		return $metadata;
	}

    // Custom fields
    public function custom_field( $field )
    {
        return $this->meta( $field );
    }

}