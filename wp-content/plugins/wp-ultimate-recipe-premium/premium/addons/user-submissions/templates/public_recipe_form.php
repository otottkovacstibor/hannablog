<?php $required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() ); ?>
<div id="wpurp_user_submission_form" class="postbox">
    <form id="new_recipe" name="new_recipe" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe->ID(); ?>" />
        <div class="recipe-title-container">
            <p>
                <label for="recipe_title"><?php _e( 'Recipe title', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_title_check', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br />
                <input type="text" id="recipe_title" value="<?php echo isset( $_POST['recipe_title'] ) ? $_POST['recipe_title'] : $recipe->title();  ?>" size="20" name="recipe_title" />
            </p>
        </div>

<?php if( !is_user_logged_in() ) { ?>
        <div class="recipe-author-container">
            <p>
                <label for="recipe-author"><?php _e( 'Your name', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe-author', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br />
                <input type="text" id="recipe-author" value="<?php echo isset( $_POST['recipe-author'] ) ? $_POST['recipe-author'] : $recipe->author();  ?>" size="50" name="recipe-author" />
            </p>
        </div>
<?php } ?>
        <div class="recipe-image-container">
<?php $has_image = $recipe->image_ID() > 0 ? true : false; ?>
<?php if ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) { ?>
            <p>
                <label for="recipe_thumbnail"><?php _e( 'Featured image', 'wp-ultimate-recipe' ); ?><?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label><br />
                <?php if( $has_image ) { ?>
                <img src="<?php echo $recipe->image_url( 'thumbnail' ); ?>" class="recipe_thumbnail" /><br/>
                <?php } ?>
                <input class="recipe_thumbnail_image button" type="file" id="recipe_thumbnail" value="" size="50" name="recipe_thumbnail" />
            </p>
<?php } else { ?>
            <p>
                <input name="recipe_thumbnail" class="recipe_thumbnail_image" type="hidden" value="<?php echo $recipe->image_ID(); ?>" />
                <input class="recipe_thumbnail_add_image button button<?php if($has_image) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Featured Image', 'wp-ultimate-recipe' ); ?>" />
                <input class="recipe_thumbnail_remove_image button<?php if(!$has_image) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e('Remove Featured Image', 'wp-ultimate-recipe' ); ?>" />
                <?php if( in_array( 'recipe_thumbnail', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?>
                <br /><img src="<?php echo $recipe->image_url( 'thumbnail' ); ?>" class="recipe_thumbnail" />
            </p>
<?php } ?>
        </div>
        <div class="recipe-tags-container">
            <p class="taxonomy-select-boxes">
<?php
        $select_fields = array();
        $multiselect = WPUltimateRecipe::option( 'recipe_tags_user_submissions_multiselect', '1' ) == '1' ? true : false;

        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );

        $args = array(
            'echo' => 0,
            'orderby' => 'NAME',
            'hide_empty' => 0,
            'hierarchical' => 1,
        );

        $hide_tags = WPUltimateRecipe::option( 'user_submission_hide_tags', array() );

        foreach( $taxonomies as $taxonomy => $options ) {
            if( !in_array( $taxonomy, $hide_tags ) ) {
                $args['show_option_none'] = $multiselect ? '' : $options['labels']['singular_name'];
                $args['taxonomy'] = $taxonomy;
                $args['name'] = 'recipe-' . $taxonomy;

                $select_fields[$taxonomy] = array(
                    'label' => $options['labels']['singular_name'],
                    'dropdown' => wp_dropdown_categories( $args ),
                );
            }
        }

        if( WPUltimateRecipe::option( 'recipe_tags_user_submissions_categories', '0' ) == '1' ) {
            $args['show_option_none'] = $multiselect ? '' : __( 'Category', 'wp-ultimate-recipe' );
            $args['taxonomy'] = 'category';
            $args['name'] = 'recipe-category';

            $exclude = WPUltimateRecipe::option( 'user_submission_hide_category_terms', array() );
            $args['exclude'] = implode( ',', $exclude );

            $select_fields['category'] = array(
                'label' => __( 'Category', 'wp-ultimate-recipe' ),
                'dropdown' => wp_dropdown_categories( $args ),
            );
        }

        if( WPUltimateRecipe::option( 'recipe_tags_user_submissions_tags', '0' ) == '1' ) {
            $args['show_option_none'] = $multiselect ? '' : __( 'Tag', 'wp-ultimate-recipe' );
            $args['taxonomy'] = 'post_tag';
            $args['name'] = 'recipe-post_tag';

            $exclude = WPUltimateRecipe::option( 'user_submission_hide_tag_terms', array() );
            $args['exclude'] = implode( ',', $exclude );

            $select_fields['post_tag'] = array(
                'label' => __( 'Tag', 'wp-ultimate-recipe' ),
                'dropdown' => wp_dropdown_categories( $args ),
            );
        }

        foreach( $select_fields as $taxonomy => $select_field ) {

            // Multiselect
            if( $multiselect ) {
                preg_match( "/<select[^>]+>/i", $select_field['dropdown'], $select_field_match );
                if( isset( $select_field_match[0] ) ) {
                    $select_multiple = preg_replace( "/name='([^']+)/i", "$0[]' data-placeholder='".$select_field['label']."' multiple='multiple", $select_field_match[0] );
                    $select_field['dropdown'] = str_ireplace( $select_field_match[0], $select_multiple, $select_field['dropdown'] );
                }
            }

            // Selected terms
            $terms = wp_get_post_terms( $recipe->ID(), $taxonomy, array( 'fields' => 'ids' ) );
            foreach( $terms as $term_id ) {
                $select_field['dropdown'] = str_replace( ' value="'. $term_id .'"', ' value="'. $term_id .'" selected="selected"', $select_field['dropdown'] );
            }

            echo $select_field['dropdown'];
        }
?>
            </p>
        </div>
<?php
        $wpurp_user_submission = true;
        include( WPUltimateRecipe::get()->coreDir . '/helpers/recipe_form.php' );
?>
<?php if( WPUltimateRecipe::option( 'user_submissions_use_security_question', '' ) == '1' ) { ?>
    <div class="security-question-container">
        <h4><?php _e( 'Security Question', 'wp-ultimate-recipe' ); ?><span class="wpurp-required">*</span></h4>
        <p>
            <label for="security-answer"><?php echo WPUltimateRecipe::option( 'user_submissions_security_question', '4 + 7 =' ); ?></label> <input type="text" id="security-answer" value="<?php echo isset( $_POST['security-answer'] ) ? $_POST['security-answer'] : '';  ?>" size="25" name="security-answer" />
        </p>
    </div>
<?php } ?>
        <p align="right">
            <?php if( WPUltimateRecipe::option( 'user_submission_preview_button', '1') == '1' ) { ?>
            <input type="submit" value="<?php _e( 'Preview', 'wp-ultimate-recipe' ); ?>" id="preview" name="preview" />
            <?php } ?>
            <input type="submit" value="<?php _e( 'Submit', 'wp-ultimate-recipe' ); ?>" id="submit" name="submit" />
        </p>
        <input type="hidden" name="action" value="post" />
        <?php echo wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
    </form>
</div>