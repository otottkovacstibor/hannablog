<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Export XML', 'wp-ultimate-recipe' ); ?></h2>
    <h3><?php _e( 'Select recipes', 'wp-ultimate-recipe' ); ?></h3>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_export_xml_manual' ); ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="export_xml_manual">
        <?php wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
        <input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce('recipe'); ?>" />
        <div class="wpurp_export_select wpurp_export_select_date">
            <?php _e( 'From', 'wp-ultimate-recipe' ); ?> <input type="text" name="wpurp_export_date_from" class="wpurp_date wpurp_export_select" id="wpurp_export_date_from" placeholder="01/28/1988"/>
            <?php _e( 'to', 'wp-ultimate-recipe' ); ?> <input type="text" name="wpurp_export_date_to" class="wpurp_date wpurp_export_select" id="wpurp_export_date_to" placeholder="01/28/1988"/>
        </div>
        <div class="wpurp_export_select wpurp_export_select_author">
            <?php _e( 'Author', 'wp-ultimate-recipe' ); ?> <select name="wpurp_export_author" class="wpurp_export_select" id="wpurp_export_author">
                <option value="0"><?php _e( 'All', 'wp-ultimate-recipe' ); ?></option>
                <?php
                $authors = WPUltimateRecipe::get()->helper( 'cache' )->get( 'recipe_authors' );

                foreach( $authors as $author ) {
                    echo '<option value="' . esc_attr( $author['value'] ) . '">' . $author['label'] . '</option>';
                }
                ?>
            </select>
        </div>
        <br/>
        <?php _e( 'Quick select', 'wp-ultimate-recipe' ); ?> <a href="#" onclick="event.preventDefault(); ExportXML.deselectAllRecipes()"><?php _e( 'None', 'wp-ultimate-recipe' ); ?></a>, <a href="#" onclick="event.preventDefault(); ExportXML.selectAllRecipes()"><?php _e( 'All', 'wp-ultimate-recipe' ); ?></a><br/>
        <br/><br/>
        <?php
        $recipes = WPUltimateRecipe::get()->helper( 'cache' )->get( 'recipes_by_date' );

        foreach( $recipes as $recipe ) {
            echo '<input type="checkbox" name="recipes[]" value="' . $recipe['value'] . '" class="xml-recipe"/> ' . $recipe['label'] . '<br/>';
        }
        ?>
        <?php submit_button( __( 'Export XML', 'wp-ultimate-recipe' ) ); ?>
    </form>
</div>