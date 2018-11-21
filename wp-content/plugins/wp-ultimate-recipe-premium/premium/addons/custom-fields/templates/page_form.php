<?php _e( 'Create custom fields for your recipes.', 'wp-ultimate-recipe' ); ?>
<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="add_custom_field">
    <?php wp_nonce_field( 'add_custom_field', 'add_custom_field_nonce', false ); ?>

    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row"><?php _e( 'Key', 'wp-ultimate-recipe' ); ?></th>
                <td>
                    <input type="text" id="wpurp_custom_field_key" name="wpurp_custom_field_key" />
                    <label for="wpurp_custom_field_key"><?php _e( '(e.g. recipe_chef)', 'wp-ultimate-recipe' ); ?> <?php _e( 'Make sure this is unique!', 'wp-ultimate-recipe' ); ?></label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e( 'Name', 'wp-ultimate-recipe' ); ?></th>
                <td>
                    <input type="text" id="wpurp_custom_field_name" name="wpurp_custom_field_name" />
                    <label for="wpurp_custom_field_name"><?php _e( '(e.g. Recipe Chef)', 'wp-ultimate-recipe' ); ?></label>
                </td>
            </tr>
        </tbody>
    </table>
    <br/>
    <?php submit_button( __( 'Add new field', 'wp-ultimate-recipe' ), 'primary wpurp_adding', 'submit', false ); ?>
</form>