<div class="wrap">
    <h2><?php _e( 'Ingredient Groups', 'wp-ultimate-recipe' ); ?></h2>

    <?php
    $args = array(
        'hide_empty' => false
    );
    $ingredients = get_terms( 'ingredient', $args );



    $ungrouped = '';
    $groups = array( $ungrouped );
    $ingredients_without_group = array();
    $ingredients_with_group = array();

    foreach( $ingredients as $ingredient ) {
        $group = WPURP_Taxonomy_MetaData::get( 'ingredient', $ingredient->slug, 'group' );
        $group = $group ? $group : $ungrouped;

        if( !in_array( $group, $groups ) ) {
            $groups[] = $group;
        }

        $ingredient_data = array(
            'slug' => $ingredient->slug,
            'name' => $ingredient->name,
            'group' => $group,
            'group_key' => array_search( $group, $groups ),
        );

        if( $group == $ungrouped ) {
            $ingredients_without_group[] = $ingredient_data;
        } else {
            $ingredients_with_group[] = $ingredient_data;
        }
    }
    ?>

    <div class="ingredient-group-header">
        <div class="ingredient-group-input-container">
            <input type="text" id="newGroup" placeholder="<?php _e( 'Vegetables', 'wp-ultimate-recipe' ); ?>"><br/>
            <button class="button" onclick="IngredientGroups.setGroup(this)"><?php _e( 'Set group for selected ingredients', 'wp-ultimate-recipe' ); ?></button>
        </div>
    </div>

    <?php
    $sorted_groups = array();
    foreach( $groups as $key => $group ) {
        if( $group != $ungrouped ) {
            $sorted_groups[$group] = '<a href="#" onclick="IngredientGroups.selectGroup(' . $key . ')" class="ingredient-group" data-group-key="' . $key .'">' . $group . '</a>';
        }
    }

    echo __( 'Select', 'wp-ultimate-recipe' ) . ': ';
    echo '<span class="select-group-links">';
    echo '<a href="#" onclick="IngredientGroups.selectGroup(-1)">' . __( 'None', 'wp-ultimate-recipe' ) . '</a>, ';
    echo '<a href="#" onclick="IngredientGroups.selectGroup(0)">' . __( 'Ungrouped', 'wp-ultimate-recipe' ) . '</a>';
    if( count( $sorted_groups ) != 0 ) {
        ksort( $sorted_groups );
        echo ' | ';
        echo implode( ', ', $sorted_groups );
    }
    echo '</span>';

    ?>

    <ul class="ingredients">
        <strong>Ungrouped</strong>
        <?php
        foreach( $ingredients_without_group as $ingredient ) {
            echo '<li><input type="checkbox" name="ingredient" value="' . $ingredient['slug'] .'" class="ingredient-group-' . $ingredient['group_key'] . '">';
            echo $ingredient['name'];
            echo ' <span class="group">' . $ingredient['group'] .'</span>';
            echo '</li>';
        }
        ?>
        <strong>Grouped</strong>
        <?php
        foreach( $ingredients_with_group as $ingredient ) {
            echo '<li><input type="checkbox" name="ingredient" value="' . $ingredient['slug'] .'" class="ingredient-group-' . $ingredient['group_key'] . '">';
            echo $ingredient['name'];
            echo ' <span class="group">' . $ingredient['group'] .'</span>';
            echo '</li>';
        }
        ?>
    </ul>
</div>