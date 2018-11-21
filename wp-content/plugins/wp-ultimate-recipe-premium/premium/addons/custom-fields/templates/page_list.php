<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>" onsubmit="return confirm('<?php _e('Do you really want to delete this custom field?', 'wp-ultimate-recipe'); ?>');">
    <input type="hidden" name="action" value="delete_custom_field">
    <?php wp_nonce_field( 'delete_custom_field', 'delete_custom_field_nonce', false ); ?>

    <table id="wpurp-fields-table" class="wp-list-table widefat" cellspacing="0">
        <thead>
        <tr>
            <th scope="col" id="field" class="manage-column">
                <?php _e( 'Key', 'wp-ultimate-recipe' ); ?>
            </th>
            <th scope="col" id="name" class="manage-column">
                <?php _e( 'Name', 'wp-ultimate-recipe' ); ?>
            </th>
            <th scope="col" id="action" class="manage-column">
                <?php _e( 'Actions', 'wp-ultimate-recipe' ); ?>
            </th>
        </tr>
        </thead>

        <tbody id="the-list">
<?php
$custom_fields = $this->get_custom_fields();

if ( $custom_fields ) {
    foreach ( $custom_fields as $key => $custom_field ) {

?>
            <tr>
                <td><strong><?php echo $key; ?></strong></td>
                <td class="singular-name"><?php echo $custom_field['name']; ?></td>
                <td>
                    <span class="wpurp_adding">
                        <?php submit_button( __( 'Delete', 'wp-ultimate-recipe' ), 'delete', 'submit-delete-' . $key, false ); ?>
                    </span>
                </td>
            </tr>
<?php
    }
}
?>
        </tbody>
    </table>
</form>