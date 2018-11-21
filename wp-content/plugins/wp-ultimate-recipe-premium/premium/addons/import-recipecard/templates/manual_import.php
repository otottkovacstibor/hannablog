

<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import RecipeCard', 'wp-ultimate-recipe' ); ?></h2>

    <?php
    $recipecard_recipes = $this->get_recipecard_recipes();

    if( count( $recipecard_recipes['import'] ) == 0 ) {
        echo '<p>' . __( 'There are no recipes left to import', 'wp-ultimate-recipe' ) . '</p>';
    } else {
        echo '<p>' . __( 'Number of recipes left to import:', 'wp-ultimate-recipe' ) . ' ' . count( $recipecard_recipes['import'] ) .'</p>';

        $recipecard_id = reset( $recipecard_recipes['import'] );
        $post_id = key( $recipecard_recipes['import'] );

        $recipecard = $this->get_recipecard_recipe( $recipecard_id );

        $ingredients = array();
        foreach( $recipecard->ingredients as $ingredient_group ) {
            foreach( $ingredient_group->lines as $ingredient_line ) {
                $ingredients[] = $ingredient_line;
            }
        }

    // Pass ingredients to javascript
    ?>
    <script type="text/javascript">
        <?php echo 'var wpurp_import_ingredients = '. json_encode( $ingredients ) . ';'; ?>
    </script>

    <h3><?php _e( 'Ingredients', 'wp-ultimate-recipe' );?></h3>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_recipecard_manual' ); ?>">
        <input type="hidden" name="action" value="import_recipecard_manual">
        <input type="hidden" name="import_recipecard_id" value="<?php echo $recipecard_id; ?>">
        <input type="hidden" name="import_post_id" value="<?php echo $post_id; ?>">
        <?php wp_nonce_field( 'import_recipecard_manual', 'import_recipecard_manual', false ); ?>

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