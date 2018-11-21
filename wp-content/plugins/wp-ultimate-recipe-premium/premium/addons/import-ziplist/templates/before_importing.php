<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import Ziplist', 'wp-ultimate-recipe' ); ?></h2>
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
            WP Ultimate Recipe stores 4 different parts for each ingredient: quantity, unit, name and notes. This allows us to do things like the Unit Conversion feature and many others. Unfortunately Ziplist stores ingredients as one piece of text so we can't simply migrate this. We have an automated process that tries it's best but there will be mistakes. To ensure a good migration:
            <ul>
                <li>Make sure commonly used units are in the <strong>Recipes > Settings > Unit Conversion > Unit Aliases</strong> list</li>
                <li>Any other units you might use can be added on the <strong>Recipes > Settings > Import Recipes</strong> page</li>
            </ul>
        </li>
        <li>
            Custom Fields will be created for fields that are not part of our recipes by default. You can manage these on the <strong>Recipes > Custom Fields</strong> page
        </li>
        <li>
            In WP Ultimate Recipe 1 recipe = 1 recipe post, so posts containing multiple recipes cannot be imported automatically. You can create a separate recipe for those and then include them with our [ultimate-recipe id=""] shortcode. There is some more information on the <strong>Recipes > FAQ</strong> page.
        </li>
        <li>
            If you have the Recipe Image field set in your Ziplist plugin it will replace the featured image of the post.
        </li>
        <li>
            There are a few things that cannot be imported due to plugin differences:
            <ul>
                <li>Links in ingredients</li>
                <li>Bold and italic text in ingredients</li>
                <li>Images in ingredients</li>
                <li>Cooking times: hours will be added as minutes (e.g. 1 hour, 15 minutes will become 75 minutes)</li>
            </ul>
        </li>
    </ol>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_ziplist_manual' ); ?>">
        <input type="hidden" name="action" value="import_ziplist_manual">
        <?php wp_nonce_field( 'import_ziplist_manual', 'import_ziplist_manual', false ); ?>
        <?php submit_button( __( 'Import Ziplist', 'wp-ultimate-recipe' ) ); ?>
    </form>

<?php
$ziplist_recipes = $this->get_ziplist_recipes();

if( count( $ziplist_recipes['problem'] ) != 0 ) {
?>
    <h3><?php _e( 'Unable to import', 'wp-ultimate-recipe' ); ?></h3>
    <p><?php _e( 'We are unable to import these posts or pages automatically:', 'wp-ultimate-recipe' ); ?></p>
    <?php
    foreach( $ziplist_recipes['problem'] as $post_id => $ziplist_id ) {
        $post = get_post( $post_id );
        echo ucfirst( $post->post_type ) . ' - <a href="' . get_permalink( $post_id ) . '">' . $post->post_title . '</a><br/>';
    }
    ?>
<?php } ?>
</div>