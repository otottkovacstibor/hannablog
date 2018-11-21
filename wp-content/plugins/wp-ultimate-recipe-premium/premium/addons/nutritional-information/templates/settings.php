<div class="wrap">
    <h2><?php _e( 'Nutritional Information', 'wp-ultimate-recipe' ); ?></h2>

    <?php
    $limit_by_recipe_id = isset( $_GET['limit_by_recipe'] ) ? intval( $_GET['limit_by_recipe'] ) : 0;
    $limit_by_recipe = $limit_by_recipe_id == 0 ? null : new WPURP_Recipe( $limit_by_recipe_id );

    $recipes = WPUltimateRecipe::get()->query()->post_status( 'any' )->order_by( 'title' )->order( 'ASC' )->get();
    ?>

    <strong><?php _e( 'Recipe', 'wp-ultimate-recipe' ); ?>:</strong>
    <?php
    $options_with = '';
    $options_without = '';

    foreach( $recipes as $recipe ) {
        $selected = $recipe->ID() == $limit_by_recipe_id ? ' selected' : '';
        $option = '<option value="' . $recipe->ID() . '"' . $selected . '>' . $recipe->title() . '</option>';

        if( !$recipe->nutritional() || count( array_filter( $recipe->nutritional() ) ) == 0 ) {
            $options_without .= $option;
        } else {
            $options_with .= $option;
        }
    }
    ?>
    <select id="limit-recipe">
        <option value="0"><?php _e( 'All', 'wp-ultimate-recipe' ); ?></option>
        <optgroup label="<?php echo esc_attr( __( 'Without Nutritional Information', 'wp-ultimate-recipe' ) ); ?>">
            <?php echo $options_without; ?>
        </optgroup>
        <optgroup label="<?php echo esc_attr( __( 'With Nutritional Information', 'wp-ultimate-recipe' ) ); ?>">
            <?php echo $options_with; ?>
        </optgroup>
    </select>

    <?php
    if( $limit_by_recipe_id == 0 ) {
        // All recipes
        $args = array(
            'hide_empty' => false
        );
        $ingredients = get_terms( 'ingredient', $args );
    } else {
        $ingredients = wp_get_post_terms( $limit_by_recipe_id, 'ingredient' );
    }

    $nutritional_information = $this->get();

    // Put ingredients in groups
    $groups = array();
    foreach( $ingredients as $ingredient ) {
        if( isset( $nutritional_information[$ingredient->term_id] ) ) {
            if( !isset( $groups['Information Present'] ) ) {
                $groups['Information Present'] = array();
            }
            $groups['Information Present'][] = $ingredient;
        } else {
            if( !isset( $groups['Information Missing'] ) ) {
                $groups['Information Missing'] = array();
            }
            $groups['Information Missing'][] = $ingredient;
        }
    }
    ksort( $groups ); // Sort by key


    // Output different groups
    foreach( $groups as $group => $ingredients ) {
        echo '<h3>' . $group . '</h3>';
    ?>
    <table class="wp-list-table widefat wpurp-ingredients-nutritional" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-ingredient">
                <?php _e( 'Ingredient', 'wp-ultimate-recipe' ); ?>
            </th>
            <th scope="col" class="manage-column column-actions">
                <?php _e( 'Actions', 'wp-ultimate-recipe' ); ?>
            </th>
            <th scope="col" class="manage-column column-nutritional">
                <?php _e( 'Nutritional Information', 'wp-ultimate-recipe' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
    <?php
        foreach( $ingredients as $ingredient ) {
            $nutritional = $this->get( $ingredient->term_id );

            foreach( $this->fields as $field => $unit ) {
                if( !isset( $nutritional[$field] ) ) {
                    $nutritional[$field] = '';
                }
            }
            ?>
            <tr>
                <td><?php edit_term_link( $ingredient->name, '', '', $ingredient ); ?></td>
                <td>
                    <button type="button" class="button button-primary wpurp-get-nutritional-panel" data-ingredient="<?php echo $ingredient->term_id; ?>"><?php _e( 'Get Nutritional Data', 'wp-ultimate-recipe' ); ?></button>
                    <button type="button" class="button wpurp-edit-nutritional-panel" data-ingredient="<?php echo $ingredient->term_id; ?>"><?php _e( 'Edit', 'wp-ultimate-recipe' ); ?></button>
                </td>
                <td><div id="nutritional-summary-<?php echo $ingredient->term_id; ?>"><?php echo $this->get_summary( $ingredient->term_id ); ?></div></td>
            </tr>
            <tr>
                <td colspan="3" class="nutritional-panel-row">
                    <div id="nutritional-panel-<?php echo $ingredient->term_id; ?>" class="nutritional-panel">
                        <div class="get-nutritional">
                            <div class="get-nutritional-search">
                                <input type="text" value="<?php echo $ingredient->name; ?>" id="search-ingredient-<?php echo $ingredient->term_id; ?>"/>
                                <button type="button" onclick="NutritionalInformation.searchIngredient(<?php echo $ingredient->term_id; ?>);" class="button button-primary"><?php _e( 'Find', 'wp-ultimate-recipe' ); ?></button>
                                <button type="button" class="button" onclick="NutritionalInformation.closePanel(<?php echo $ingredient->term_id; ?>);"><?php _e( 'Close', 'wp-ultimate-recipe' ); ?></button>
                            </div>

                            <div id="get-nutritional-results-<?php echo $ingredient->term_id; ?>" class="get-nutritional-results">
                            </div>
                        </div>
                        <div class="edit-nutritional">
                            <table class="nutritional-data">
                                <tbody>
                                <tr>
                                    <td>
                                        <?php _e( 'Reference serving', 'wp-ultimate-recipe' ); ?>
                                    </td>
                                    <td>
                                        <input class="wide" type="text" name="serving" value="<?php echo isset( $nutritional['_meta']['serving'] ) ? $nutritional['_meta']['serving'] : ''; ?>"/>
                                    </td>
                                    <td>
                                        <?php _e( 'Metric quantity', 'wp-ultimate-recipe' ); ?>
                                    </td>
                                    <td>
                                        <input type="text" name="serving_quantity" value="<?php echo isset( $nutritional['_meta']['serving_quantity'] ) ? $nutritional['_meta']['serving_quantity'] : ''; ?>"/>
                                        <select name="serving_unit">
                                            <?php $unit = isset( $nutritional['_meta']['serving_unit'] ) ? $nutritional['_meta']['serving_unit'] : 'g'; ?>
                                            <option value="g"<?php echo $unit == 'g' ? ' selected' : ''; ?>>g</option>
                                            <option value="ml"<?php echo $unit == 'ml' ? ' selected' : ''; ?>>ml</option>
                                            <?php if( $unit != 'g' && $unit != 'ml' ) {
                                                echo '<option value="' . $unit . '" selected>' . $unit . '</option>';
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Calories', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="calories" value="<?php echo $nutritional['calories']; ?>" /> kcal</td>
                                    <td><?php _e( 'Cholesterol', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="cholesterol" value="<?php echo $nutritional['cholesterol']; ?>" /> mg</td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Total Carbohydrates', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="carbohydrate" value="<?php echo $nutritional['carbohydrate']; ?>" /> g</td>
                                    <td><?php _e( 'Sodium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="sodium" value="<?php echo $nutritional['sodium']; ?>" /> mg</td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Protein', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="protein" value="<?php echo $nutritional['protein']; ?>" /> g</td>
                                    <td><?php _e( 'Potassium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="potassium" value="<?php echo $nutritional['potassium']; ?>" /> mg</td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Total Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="fat" value="<?php echo $nutritional['fat']; ?>" /> g</td>
                                    <td><?php _e( 'Dietary Fiber', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="fiber" value="<?php echo $nutritional['fiber']; ?>" /> g</td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Saturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="saturated_fat" value="<?php echo $nutritional['saturated_fat']; ?>" /> g</td>
                                    <td><?php _e( 'Sugar', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="sugar" value="<?php echo $nutritional['sugar']; ?>" /> g</td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Polyunsaturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="polyunsaturated_fat" value="<?php echo $nutritional['polyunsaturated_fat']; ?>" /> g</td>
                                    <td><?php _e( 'Vitamin A', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="vitamin_a" value="<?php echo $nutritional['vitamin_a']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Monounsaturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="monounsaturated_fat" value="<?php echo $nutritional['monounsaturated_fat']; ?>" /> g</td>
                                    <td><?php _e( 'Vitamin C', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="vitamin_c" value="<?php echo $nutritional['vitamin_c']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e( 'Trans Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="trans_fat" value="<?php echo $nutritional['trans_fat']; ?>" /> g</td>
                                    <td><?php _e( 'Calcium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="calcium" value="<?php echo $nutritional['calcium']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <button type="button" class="button button-primary" onclick="NutritionalInformation.saveNutritional(<?php echo $ingredient->term_id; ?>);"><?php _e( 'Save', 'wp-ultimate-recipe' ); ?></button>
                                        <button type="button" class="button" onclick="NutritionalInformation.closePanel(<?php echo $ingredient->term_id; ?>);"><?php _e( 'Close', 'wp-ultimate-recipe' ); ?></button>
                                    </td>
                                    <td>&nbsp;</td>
                                    <td><?php _e( 'Iron', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="iron" value="<?php echo $nutritional['iron']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>

            <?php
        }
    ?>
        </tbody>
    </table>
    <?php } ?>
    <?php if( $limit_by_recipe ) { ?>
    <h3>Recipe: <?php echo $limit_by_recipe->title(); ?></h3>
    <table class="wp-list-table widefat wpurp-recipe-nutritional" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" class="manage-column">
                <?php _e( 'Nutritional Information', 'wp-ultimate-recipe' ); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <button type="button" class="button button-primary" onclick="NutritionalInformation.calculateRecipe(<?php echo $limit_by_recipe_id; ?>);"><?php _e( 'Calculate Recipe Nutritonal Information', 'wp-ultimate-recipe' ); ?></button><br/>
            </td>
        </tr>
        <tr>
            <td class="calculate-recipe-row">
                <div class="calculate-recipe-panel" data-servings="<?php echo $limit_by_recipe->servings_normalized(); ?>">
                    <table>
                        <tbody>
                        <tr>
                            <td colspan="3"><strong><?php echo __( 'Ingredient servings:', 'wp-ultimate-recipe' ) . ' ' . $limit_by_recipe->servings_normalized(); ?></strong></td>
                        </tr>
                        <?php foreach( $limit_by_recipe->ingredients() as $ingredient ) { ?>
                        <tr class="ingredient-nutritional-match" data-ingredient="<?php echo $ingredient['ingredient_id']; ?>" data-amount="<?php echo $ingredient['amount_normalized']; ?>" data-alias="<?php echo $ingredient['unit']; ?>">
                            <td class="wpurp-recipe-ingredient">
                                <?php
                                echo $ingredient['amount'] . ' <span class="wpurp-recipe-ingredient-unit">' . $ingredient['unit'] . '</span> ' . $ingredient['ingredient'];
                                if( $ingredient['notes'] ) {
                                    echo ' (' . $ingredient['notes'] . ')';
                                }
                                ?>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="3">
                                <button type="button" onclick="NutritionalInformation.useCalculatedRecipe(<?php echo $limit_by_recipe_id; ?>);" class="button button-primary"><?php _e( 'Calculate', 'wp-ultimate-recipe' ); ?></button>
                                <button type="button" class="button" onclick="jQuery('.calculate-recipe-panel').slideUp(250);"><?php _e( 'Close', 'wp-ultimate-recipe' ); ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php
                $nutritional = $limit_by_recipe->nutritional();

                foreach( $this->fields as $field => $unit ) {
                    if( !isset( $nutritional[$field] ) ) {
                        $nutritional[$field] = '';
                    }
                }
                ?>
                <strong><?php _e( 'Display Options:', 'wp-ultimate-recipe' ); ?></strong>
                <table class="nutritional-data">
                    <tbody>
                    <tr>
                        <td><?php _e( 'Serving Size', 'wp-ultimate-recipe' ); ?></td><td><input type="text" name="serving_size" value="<?php echo $nutritional['serving_size']; ?>" /> g</td>
                        <td><?php _e( 'Display in Label', 'wp-ultimate-recipe' ); ?></td><td><input type="text" name="serving_size_display" value="<?php echo $nutritional['serving_size_display']; ?>" /> g</td>
                    </tr>
                    <tr>
                        <td colspan="2"><i><?php _e( 'Not required when displaying nutritional information per serving in the label.', 'wp-ultimate-recipe' ); ?></i></td>
                        <td colspan="2"><i><?php _e( 'Leave blank to display per serving in the label.', 'wp-ultimate-recipe' ); ?></i></td>
                    </tr>
                    </tbody>
                </table>
                <br/>
                <strong><?php _e( 'Data for 1 serving:', 'wp-ultimate-recipe' ); ?></strong>
                <table class="nutritional-data">
                    <tbody>
                    <tr>
                        <td><?php _e( 'Calories', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="calories" value="<?php echo $nutritional['calories']; ?>" /> kcal</td>
                        <td><?php _e( 'Cholesterol', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="cholesterol" value="<?php echo $nutritional['cholesterol']; ?>" /> mg</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Total Carbohydrates', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="carbohydrate" value="<?php echo $nutritional['carbohydrate']; ?>" /> g</td>
                        <td><?php _e( 'Sodium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="sodium" value="<?php echo $nutritional['sodium']; ?>" /> mg</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Protein', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="protein" value="<?php echo $nutritional['protein']; ?>" /> g</td>
                        <td><?php _e( 'Potassium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="potassium" value="<?php echo $nutritional['potassium']; ?>" /> mg</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Total Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="fat" value="<?php echo $nutritional['fat']; ?>" /> g</td>
                        <td><?php _e( 'Dietary Fiber', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="fiber" value="<?php echo $nutritional['fiber']; ?>" /> g</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Saturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="saturated_fat" value="<?php echo $nutritional['saturated_fat']; ?>" /> g</td>
                        <td><?php _e( 'Sugar', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="sugar" value="<?php echo $nutritional['sugar']; ?>" /> g</td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Polyunsaturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="polyunsaturated_fat" value="<?php echo $nutritional['polyunsaturated_fat']; ?>" /> g</td>
                        <td><?php _e( 'Vitamin A', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="vitamin_a" value="<?php echo $nutritional['vitamin_a']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Monounsaturated Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="monounsaturated_fat" value="<?php echo $nutritional['monounsaturated_fat']; ?>" /> g</td>
                        <td><?php _e( 'Vitamin C', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="vitamin_c" value="<?php echo $nutritional['vitamin_c']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Trans Fat', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="trans_fat" value="<?php echo $nutritional['trans_fat']; ?>" /> g</td>
                        <td><?php _e( 'Calcium', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="calcium" value="<?php echo $nutritional['calcium']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                    </tr>
                    <tr>
                        <td class="save-recipe-buttons">
                            <button type="button" class="button button-primary" onclick="NutritionalInformation.saveRecipe(<?php echo $limit_by_recipe_id; ?>);"><?php _e( 'Save', 'wp-ultimate-recipe' ); ?></button>
                            <button type="button" class="button" onclick="NutritionalInformation.resetRecipe(<?php echo $limit_by_recipe_id; ?>);"><?php _e( 'Clear', 'wp-ultimate-recipe' ); ?></button>
                        </td>
                        <td>&nbsp;</td>
                        <td><?php _e( 'Iron', 'wp-ultimate-recipe' ); ?></td> <td><input type="text" name="iron" value="<?php echo $nutritional['iron']; ?>" /> % <?php _e( 'Daily Value', 'wp-ultimate-recipe' ); ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <?php } ?>
    <br/>
    <!-- Begin FatSecret Platform API HTML Attribution Snippet -->
    <a href="http://platform.fatsecret.com">Powered by FatSecret</a>
    <!-- End FatSecret Platform API HTML Attribution Snippet -->
</div>