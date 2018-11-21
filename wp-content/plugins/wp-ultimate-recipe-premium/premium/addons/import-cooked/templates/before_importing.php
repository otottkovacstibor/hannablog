<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import Cooked', 'wp-ultimate-recipe' ); ?></h2>
    <h3><?php _e( 'Before importing', 'wp-ultimate-recipe' ); ?></h3>
    <ol>
        <li><?php _e( "It's a good idea to backup your WP database before using the import feature.", 'wp-ultimate-recipe' ); ?></li>
        <li>Make sure the <strong>permalink structure</strong> for your recipes is the same as the current one. By default WP Ultimate Recipe uses the /recipe/ slug, but if this doesn't correspond with your current setup your old links won't work anymore. Potential solutions:
            <ul>
                <li>Change the /recipe/ slug on the <strong>Recipes > Settings > Recipe Archive</strong> page to /your-current-slug/</li>
                <li>Remove the /recipe/ slug on the <strong>Recipes > Settings > Advanced</strong> page</li>
                <li>Use the <a href="https://wordpress.org/plugins/custom-post-type-permalinks/" target="_blank">Custom Post Type Permalinks</a> plugin for more complex permalinks like /year/month/</li>
            </ul>
        </li>
        <li>
            WP Ultimate Recipe stores 4 different parts for each ingredient: quantity, unit, name and notes. This allows us to do things like the Unit Conversion feature and many others. Unfortunately Cooked stores ingredients as one piece of text if you've been using the Simple Entry method so we can't simply migrate this. We have an automated process that tries it's best but there will be mistakes. To ensure a good migration:
            <ul>
                <li>Make sure commonly used units are in the <strong>Recipes > Settings > Unit Conversion > Unit Aliases</strong> list</li>
                <li>Any other units you might use can be added on the <strong>Recipes > Settings > Import Recipes</strong> page</li>
            </ul>
        </li>
        <li>
            Custom Fields will be created for fields that are not part of our recipes by default. You can manage these on the <strong>Recipes > Custom Fields</strong> page
        </li>
        <li>
            There are a few things that cannot be imported due to plugin differences:
            <ul>
                <li>Cooking times: hours will be added as minutes (e.g. 1 hour, 15 minutes will become 75 minutes)</li>
                <li>User Reviews</li>
                <li>Timers in the recipe instructions</li>
            </ul>
        </li>
        <?php
        $cooked_taxonomies = cooked_plugin::cp_recipe_tax_settings();
        $taxonomies = WPUltimateRecipe::get()->tags();
        if( WPUltimateRecipe::option( 'recipe_tags_use_wp_categories', '1' ) == '1' ) {
            $taxonomies['category'] = array( 'labels' => array( 'name' => 'WordPress Category') );
            $taxonomies['post_tag'] = array( 'labels' => array( 'name' => 'WordPress Tag') );
        }
        unset( $taxonomies['ingredient'] );
        ?>
        <?php if( !empty( $cooked_taxonomies ) ) { ?>
        <li>
            You need to match these Cooked tags with one of our <a href="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_taxonomies' ); ?>" target="_blank">custom tags if you want to import them</a>:
        </li>
        <?php } ?>
    </ol>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_cooked_manual' ); ?>">
        <input type="hidden" name="action" value="import_cooked_manual">

        <?php if( in_array( 'category', $cooked_taxonomies ) ) { ?>
            <br/>
            Category: <select id="cooked_tag_category" name="cooked_tag_category">
                <option value=""><?php _e( 'Select a recipe tag', 'wp-ultimate-recipe' ); ?></option>
                <?php
                foreach( $taxonomies as $taxonomy => $tag_options )
                {
                    echo '<option value="' . $taxonomy . '"';
                    if( $taxonomy == 'category' ) {
                        echo ' selected';
                    }
                    echo '>';
                    echo $tag_options['labels']['name'];
                    echo '</option>';
                }
                ?>
            </select>
        <?php } ?>
        <?php if( in_array( 'tags', $cooked_taxonomies ) ) { ?>
            <br/>
            Tag: <select id="cooked_tag_tags" name="cooked_tag_tags">
                <option value=""><?php _e( 'Select a recipe tag', 'wp-ultimate-recipe' ); ?></option>
                <?php
                foreach( $taxonomies as $taxonomy => $tag_options )
                {
                    echo '<option value="' . $taxonomy . '"';
                    if( $taxonomy == 'post_tag' ) {
                        echo ' selected';
                    }
                    echo '>';
                    echo $tag_options['labels']['name'];
                    echo '</option>';
                }
                ?>
            </select>
        <?php } ?>
        <?php if( in_array( 'cuisine', $cooked_taxonomies ) ) { ?>
            <br/>
            Cuisine: <select id="cooked_tag_cuisine" name="cooked_tag_cuisine">
                <option value=""><?php _e( 'Select a recipe tag', 'wp-ultimate-recipe' ); ?></option>
                <?php
                foreach( $taxonomies as $taxonomy => $tag_options )
                {
                    echo '<option value="' . $taxonomy . '"';
                    if( $taxonomy == 'cuisine' ) {
                        echo ' selected';
                    }
                    echo '>';
                    echo $tag_options['labels']['name'];
                    echo '</option>';
                }
                ?>
            </select>
        <?php } ?>
        <?php if( in_array( 'method', $cooked_taxonomies ) ) { ?>
            <br/>
            Method: <select id="cooked_tag_method" name="cooked_tag_method">
                <option value=""><?php _e( 'Select a recipe tag', 'wp-ultimate-recipe' ); ?></option>
                <?php
                foreach( $taxonomies as $taxonomy => $tag_options )
                {
                    echo '<option value="' . $taxonomy . '"';
                    if( $taxonomy == 'method' ) {
                        echo ' selected';
                    }
                    echo '>';
                    echo $tag_options['labels']['name'];
                    echo '</option>';
                }
                ?>
            </select>
        <?php } ?>

        <?php wp_nonce_field( 'import_cooked_manual', 'import_cooked_manual', false ); ?>
        <?php submit_button( __( 'Import Cooked', 'wp-ultimate-recipe' ) ); ?>
    </form>
</div>