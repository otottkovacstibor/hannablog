<table class="wpurp-meal-plan-shopping-list">
    <thead>
    <tr>
        <th><?php _e( 'Ingredient', 'wp-ultimate-recipe' ); ?></th>
        <th><?php _e( 'Quantity', 'wp-ultimate-recipe' ); ?><span class="wpurp-meal-plan-actions"><i class="fa fa-close wpurp-shopping-list-close"></i></span></th>
    </tr>
    </thead>
    <tbody>
    <tr class="wpurp-shopping-list-group-placeholder">
        <td colspan="2"><span class="wpurp-shopping-list-group-name"></span><span class="wpurp-meal-plan-actions"><i class="fa fa-pencil wpurp-group-edit"></i><i class="fa fa-trash wpurp-group-delete"></i></span></td>
    </tr>
    <tr class="wpurp-shopping-list-ingredient-placeholder">
        <td><?php if( WPUltimateRecipe::option( 'meal_plan_shopping_list_checkboxes', '1' ) == '1' ) echo '<input type="checkbox" class="wpurp-shopping-list-ingredient-checkbox"> '; ?><span class="wpurp-shopping-list-ingredient-name"></span></td>
        <td><span class="wpurp-shopping-list-ingredient-quantity"></span><span class="wpurp-meal-plan-actions"><i class="fa fa-pencil wpurp-ingredient-edit"></i><i class="fa fa-trash wpurp-ingredient-delete"></i></span></td>
    </tr>
    </tbody>
</table>
<div class="wpurp-meal-plan-footer-actions">
    <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-add-group"><?php _e( 'Add Group', 'wp-ultimate-recipe' ); ?></button><button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-add-ingredient"><?php _e( 'Add Ingredient', 'wp-ultimate-recipe' ); ?></button>
    <div class="wpurp-meal-plan-footer-actions-right">
        <?php if( WPUltimateRecipe::option( 'meal_plan_shopping_list_save', '1' ) == '1' ) { ?>
        <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-shopping-list-save"><?php _e( 'Save', 'wp-ultimate-recipe' ); ?></button>
        <?php } ?>
        <button type="button" class="wpurp-meal-plan-button wpurp-meal-plan-shopping-list-print"><?php _e( 'Print', 'wp-ultimate-recipe' ); ?></button>
    </div>
</div>