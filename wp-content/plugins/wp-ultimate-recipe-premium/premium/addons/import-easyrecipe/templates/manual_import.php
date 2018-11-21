

<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import EasyRecipe', 'wp-ultimate-recipe' ); ?></h2>
    <?php
    $wpurp_taxonomies = WPUltimateRecipe::get()->tags();
    unset( $wpurp_taxonomies['ingredient'] );

    $new_tags = array(
        'course' => $_POST['wpurp_import_course'],
        'cuisine' => $_POST['wpurp_import_cuisine'],
    );

    foreach( $new_tags as $tag ) {
        if ( !array_key_exists( $tag, $wpurp_taxonomies ) ) {
            die( __( 'You should select a new tag for the imported recipes', 'wp-ultimate-recipe' ) );
        }
    }

    if( $new_tags['course'] == $new_tags['cuisine'] ) {
        die( __( 'You should select two distinct tags', 'wp-ultimate-recipe' ) );
    }
    ?>

    <?php
    $recipes = $this->get_easyrecipe_recipes();

    if( count( $recipes['import'] ) == 0 ) {
        echo '<p>' . __( 'There are no recipes left to import', 'wp-ultimate-recipe' ) . '</p>';
    } else {
        echo '<p>' . __( 'Number of recipes left to import:', 'wp-ultimate-recipe' ) . ' ' . count( $recipes['import'] ) .'</p>';

        $post_id = reset( $recipes['import'] );

        $recipe = $this->get_easyrecipe( $post_id );
    ?>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_easyrecipe_manual' ); ?>">
        <input type="hidden" name="action" value="import_easyrecipe_manual">
        <?php wp_nonce_field( 'import_easyrecipe_manual', 'import_easyrecipe_manual', false ); ?>
        <input type="hidden" name="import_post_id" value="<?php echo $post_id; ?>">
        <input type="hidden" name="wpurp_import_course" value="<?php echo $new_tags['course']; ?>">
        <input type="hidden" name="wpurp_import_cuisine" value="<?php echo $new_tags['cuisine']; ?>">

        <h3><?php _e( 'Featured Image', 'wp-ultimate-recipe' );?></h3>
    <?php
    // Potential Featured Images
    $potential_images = array();

    $featured_image = get_post_thumbnail_id( $post_id, 'medium' );
    if( $featured_image != '' ) {
        $image = wp_get_attachment_image_src( $featured_image, array( 9999, 150 ) );

        $potential_images[] = array(
            'id' => $featured_image,
            'img' => $image[0],
        );
    }

    $potential_images = array_merge( $potential_images, $this->get_easyrecipe_images( $recipe->innertext ) );

    foreach( $potential_images as $index => $image ) {
        echo '<input type="radio" name="featured-image" value="' . $image['id'] .'" class="radioImageSelect" data-image="' . $image['img'] . '"';
        if( $index == 0 ) {
            echo ' checked="checked"';
        }
        echo ' />';
    }

    if( count( $potential_images ) > 0 ) {
        echo '<br/>';
        $checked = '';
    } else {
        $checked = ' checked="checked"';
    }
    ?>
        <input type="radio" id="featured-image-none" name="featured-image" value="0"<?php echo $checked; ?> /><img/>
        <label for="featured-image-none"><?php _e( 'No featured image', 'wp-ultimate-recipe' );?></label>

        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery('input.radioImageSelect').radioImageSelect();

                jQuery('input#featured-image-none').on( 'click', function() {
                    jQuery('input[name="featured-image"]').each(function() {
                        var myImg = jQuery(this).next('img');

                        // Add / Remove Checked class.
                        if ( jQuery(this).prop('checked') ) {
                            myImg.addClass('item-checked');
                        } else {
                            myImg.removeClass('item-checked');
                        }
                    });
                });
            } );
        </script>

    <?php
    // Recipe Ingredients
        $ingredient_list = $recipe->find( 'ul[class=ingredients]' );
        $ingredient_elements = isset( $ingredient_list[0] ) && is_object( $ingredient_list[0] ) ? $ingredient_list[0]->find( 'li[class=ingredient]' ) : array();

        $ingredients = array();
        foreach( $ingredient_elements as $ingredient ) {
            $text = $this->strip_easyrecipe_tags( $ingredient->plaintext );

            if( strlen( $text ) > 0 ) {
                $ingredients[] = $text;
            }
        }

    // Pass ingredients to javascript
    ?>
        <script type="text/javascript">
            <?php echo 'var wpurp_import_ingredients = '. json_encode( $ingredients ) . ';'; ?>
        </script>

        <h3><?php _e( 'Ingredients', 'wp-ultimate-recipe' ); ?></h3>
        <table id="define-ingredient-details" class="import-table">
            <thead>
            <tr>
                <th><?php _e( 'Amount', 'wp-ultimate-recipe' );?></th>
                <th><?php _e( 'Unit', 'wp-ultimate-recipe' );?></th>
                <th><?php _e( 'Ingredient', 'wp-ultimate-recipe' );?></th>
                <th><?php _e( 'Notes', 'wp-ultimate-recipe' );?></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <?php submit_button( __( 'Import this Recipe', 'wp-ultimate-recipe' ) ); ?>
        <em><?php _e( 'Feel free to stop at anytime and come back later for the rest of the recipes.', 'wp-ultimate-recipe' ); ?></em>
    </form>
<?php } ?>
</div>