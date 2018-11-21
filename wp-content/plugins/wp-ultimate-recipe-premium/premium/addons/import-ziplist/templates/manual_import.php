

<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import Ziplist', 'wp-ultimate-recipe' ); ?></h2>

    <?php
    $ziplist_recipes = $this->get_ziplist_recipes();

    if( count( $ziplist_recipes['import'] ) == 0 ) {
        echo '<p>' . __( 'There are no recipes left to import', 'wp-ultimate-recipe' ) . '</p>';
    } else {
        echo '<p>' . __( 'Number of recipes left to import:', 'wp-ultimate-recipe' ) . ' ' . count( $ziplist_recipes['import'] ) .'</p>';

        $ziplist_id = reset( $ziplist_recipes['import'] );
        $post_id = key( $ziplist_recipes['import'] );

        $ziplist = $this->ziplist_get_recipe( $ziplist_id );
        $ziplist_ingredients = explode( "\n", $ziplist->ingredients );

        $ingredients = array();
        foreach( $ziplist_ingredients as $item ) {
            if( preg_match( "/^%(\S*)/", $item, $matches ) || preg_match( "/^!(.*)/", $item, $matches ) ) {
                // Image or label, don't process
            } else {
                // Remove bold, italic and links
                $ingredients[] = $this->ziplist_derichify( $item );
            }
        }

    // Pass ingredients to javascript
    ?>
    <script type="text/javascript">
        <?php echo 'var wpurp_import_ingredients = '. json_encode( $ingredients ) . ';'; ?>
    </script>

    <?php echo __( 'Currently importing', 'wp-ultimate-recipe' ) . ' <a href="' . get_permalink( $post_id ) . '" target="_blank">' . get_the_title( $post_id ) . '</a>'; ?>

    <h3><?php _e( 'Ingredients', 'wp-ultimate-recipe' ); ?></h3>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_ziplist_manual' ); ?>">
        <input type="hidden" name="action" value="import_ziplist_manual">
        <input type="hidden" name="import_ziplist_id" value="<?php echo $ziplist_id; ?>">
        <input type="hidden" name="import_post_id" value="<?php echo $post_id; ?>">
        <?php wp_nonce_field( 'import_ziplist_manual', 'import_ziplist_manual', false ); ?>

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