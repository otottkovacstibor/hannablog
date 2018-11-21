

<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import Cooked', 'wp-ultimate-recipe' ); ?></h2>

    <?php
    $cooked_recipes = $this->get_cooked_recipes();

    if( count( $cooked_recipes['import'] ) == 0 ) {
        echo '<p>' . __( 'There are no recipes left to import', 'wp-ultimate-recipe' ) . '</p>';
    } else {
        echo '<p>' . __( 'Number of recipes left to import:', 'wp-ultimate-recipe' ) . ' ' . count( $cooked_recipes['import'] ) .'</p>';

        $post_id = reset( $cooked_recipes['import'] );

        $cooked_ingredients = get_post_meta( $post_id, '_cp_recipe_ingredients', true );
        $cooked_ingredient_lines = explode("\n", $cooked_ingredients);

        foreach( $cooked_ingredient_lines as $cooked_ingredient_line ) {
            if( strpos( $cooked_ingredient_line, '--' ) !== 0 ) {
                $ingredients[] = $cooked_ingredient_line;
            }
        }

    // Pass ingredients to javascript
    ?>
    <script type="text/javascript">
        <?php echo 'var wpurp_import_ingredients = '. json_encode( $ingredients ) . ';'; ?>
    </script>

    <?php echo __( 'Currently importing', 'wp-ultimate-recipe' ) . ' <a href="' . get_permalink( $post_id ) . '" target="_blank">' . get_the_title( $post_id ) . '</a>'; ?>

    <h3><?php _e( 'Ingredients', 'wp-ultimate-recipe' ); ?></h3>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_cooked_manual' ); ?>">
        <input type="hidden" name="action" value="import_cooked_manual">
        <input type="hidden" name="import_post_id" value="<?php echo $post_id; ?>">
        <?php wp_nonce_field( 'import_cooked_manual', 'import_cooked_manual', false ); ?>

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