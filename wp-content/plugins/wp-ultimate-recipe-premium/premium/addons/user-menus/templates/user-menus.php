<?php
$menu_display_only = isset( $menu_display_only ) ? $menu_display_only : false;
$posts_from_grid = isset( $posts_from_grid ) ? $posts_from_grid : false;

if( !$menu_display_only ) {
    $groupby_options = array();
    if( WPUltimateRecipe::option( 'recipe_tags_filter_categories', '0' ) == '1' ) {
        $groupby_options['category'] =  __( 'Category', 'wp-ultimate-recipe' );
    }

    if( WPUltimateRecipe::option( 'recipe_tags_filter_tags', '0' ) == '1' ) {
        $groupby_options['post_tag'] = __( 'Tag', 'wp-ultimate-recipe' );
    }

    $taxonomies = WPUltimateRecipe::get()->tags();
    unset($taxonomies['ingredient']);

    foreach( $taxonomies as $taxonomy => $options ) {
        if ( count( get_terms( $taxonomy ) ) > 0 ) {
            $groupby_options[$taxonomy] = $options['labels']['singular_name'];
        }
    }
}

$adjust_servings = '';

if( WPUltimateRecipe::option('user_menus_dynamic_unit_system', '1') == '1' ) {
    $adjust_servings .= '<div>' . __( 'Units', 'wp-ultimate-recipe' );
    $adjust_servings .= '<select onchange="RecipeUserMenus.changeUnits(this)">';

    $systems = WPUltimateRecipe::get()->helper( 'ingredient_units' )->get_active_systems();
    $unit_system = isset( $menu ) ? get_post_meta( $menu->ID, 'user-menus-unitSystem', true ) : WPUltimateRecipe::option( 'user_menus_default_unit_system', '0' );

    foreach($systems as $i => $system) {
        if($i == $unit_system) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        $adjust_servings .= '<option value="'.$i.'"'.$selected.'>'.$system['name'].'</option>';
    }

    $adjust_servings .= '</select></div>';
}

?>

<div class="wpurp-user-menus">

    <?php if( !$menu_display_only ) : ?>
    <div class="user-menus-input-container">
        <?php
        if ( is_user_logged_in() ) {
            global $current_user;
            get_currentuserinfo();

            $user_name = $current_user->display_name;
        } else {
            $user_name = __( 'Visitor', 'wp-ultimate-recipe' );
        }

        $default_menu_name = date('Y-m-d') . ' | ' . $user_name;

        //$default_menu_name = _e( 'My New Menu', 'wp-ultimate-recipe' );
        ?>
        <input type="text" class="user-menus-title" name="post_title" value="<?php if( isset( $menu ) ) { echo get_the_title( $menu->ID ); } else { echo $default_menu_name; } ?>"/><br>
        <select class="user-menus-select" data-placeholder="<?php _e( 'Add Recipes', 'wp-ultimate-recipe' ); ?>">
            <?php
            $groups = $this->get_recipes_grouped_by( 'a-z', $posts_from_grid );
            echo $this->get_select_options( $groups );
            ?>
        </select>
    </div>
    <div class="user-menus-servings-container">
        <?php echo $adjust_servings; ?>
        <div>
            <?php
            $general_servings = intval( WPUltimateRecipe::option('user_menus_default_servings', '4') );

            if( $general_servings < 1 ) {
                $general_servings = 4;
            }
            ?>
            <?php _e( 'Servings', 'wp-ultimate-recipe' ); ?>
            <input type="number" class="user-menus-servings-general" value="<?php echo $general_servings; ?>">
        </div>
    </div>
    <div class="user-menus-group-by-container">
        <?php _e( 'Group by', 'wp-ultimate-recipe' ); ?>: <a href="#" class="user-menus-group-by user-menus-group-by-selected" data-groupby="a-z"><?php _e( 'alphabet', 'wp-ultimate-recipe' ); ?></a><?php
        if( is_array( $groupby_options ) ) {
            foreach( $groupby_options as $id => $name ) {
                echo ', <a href="#" class="user-menus-group-by" data-groupby="'.$id.'">'.strtolower($name).'</a>';
            };
        }
        ?>
    </div>
    <?php endif; // !$menu_display_only ?>

    <div class="user-menus-selected-recipes"></div>
    <div class="user-menus-no-recipes"><?php _e( 'No recipes in your menu yet', 'wp-ultimate-recipe' ); ?></div>

    <?php if( $menu_display_only ) : ?>
    <div class="user-menus-servings-container">
        <?php echo $adjust_servings; ?>
    </div>
    <div style="clear: both;"></div>
    <?php endif; // $menu_display_only ?>

    <div class="user-menus-buttons-container">
        <?php
        // Menu author or admin can delete menus if enabled
        if( !$menu_display_only && isset( $menu ) && WPUltimateRecipe::option( 'user_menus_enable_delete', '0' ) == '1' && ( get_current_user_id() != 0 && ( current_user_can( 'manage_options' ) || $menu->post_author == get_current_user_id() ) ) ) {
            ?>
            <button onclick="RecipeUserMenus.deleteMenu()"><?php _e( 'Delete Menu', 'wp-ultimate-recipe' ); ?></button>
        <?php } ?>
        <?php
        // Menu author or admin can save menus if enabled

        $allow_save = false;

        // If not saved menu OR ( logged in user is admin OR menu author )
        if( ( !isset($menu) ) || ( get_current_user_id() != 0 && ( current_user_can( 'manage_options' ) || $menu->post_author == get_current_user_id() ) ) ) {
            switch( WPUltimateRecipe::option( 'user_menus_enable_save', 'guests' ) ) {

                case 'off':
                    $allow_save = current_user_can( 'manage_options' ) ? true : false;
                    break;

                case 'registered':
                    $allow_save = is_user_logged_in() ? true : false;
                    break;

                case 'guests':
                    $allow_save = true;
                    break;
            }
        }

        if( $allow_save && !$menu_display_only ) {
        ?>
        <button onclick="RecipeUserMenus.saveMenu()"><?php _e( 'Save Menu', 'wp-ultimate-recipe' ); ?></button>
        <?php } ?>

        <?php if( WPUltimateRecipe::option( 'user_menus_enable_print_list', '1' ) == '1' ) { ?>
        <button onclick="RecipeUserMenus.printShoppingList()"><?php _e( 'Print Shopping List', 'wp-ultimate-recipe' ); ?></button>
        <?php } ?>

        <?php if( WPUltimateRecipe::option( 'user_menus_enable_print_menu', '0' ) == '1' ) { ?>
        <button onclick="RecipeUserMenus.printUserMenu()"><?php _e( 'Print Menu', 'wp-ultimate-recipe' ); ?></button>
        <?php } ?>
    </div>

    <table class="user-menus-ingredients">
        <thead>
        <tr>
            <?php
            if( WPUltimateRecipe::option('user_menus_dynamic_unit_system', '1') == '1' ) {
                // Dynamic Unit System
                echo '<th>' . __( 'Ingredient', 'wp-ultimate-recipe' ) . '</th>';
                echo '<th>' . __( 'Amount', 'wp-ultimate-recipe' ) . '</th>';
            } else {
                // Static Unit Systems
                $systems = WPUltimateRecipe::get()->helper( 'ingredient_units' )->get_active_systems();
                $systems_to_show = $this->get_static_unit_systems();
                $nbr_of_columns = count( $systems_to_show ) + 1;

                $inline_style = '';
                if( $nbr_of_columns > 2 ) {
                    $width = round( 100.0 / $nbr_of_columns, 2 );
                    $inline_style = ' style="width: ' . $width . '%;"';
                }

                echo '<th' . $inline_style . '>' . __( 'Ingredient', 'wp-ultimate-recipe' ) . '</th>';
                foreach( $systems_to_show as $system ) {
                    echo '<th' . $inline_style . '>' . $systems[$system]['name'] . '</th>';
                }
            }
            ?>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>