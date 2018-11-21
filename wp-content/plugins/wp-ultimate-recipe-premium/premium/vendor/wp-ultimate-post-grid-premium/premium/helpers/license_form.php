<div class="<?php if( $status == 'invalid' ) { echo 'error'; } else { echo 'updated'; } ?>" id="wpupg_license_form">
    <form method="post" action="options.php">
        <?php settings_fields('edd_wpupg_license'); ?>
        <table class="wpupg_license">
            <tbody>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('License Key', 'wp-ultimate-post-grid'); ?>
                </th>
                <td>
                    <input id="edd_wpupg_license_key" name="edd_wpupg_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" placeholder="<?php _e('Enter your license key', 'wp-ultimate-post-grid'); ?>" />
                    <?php if( $status == 'valid' ) { ?>
                    <span style="color:green;"><?php _e('active'); ?></span>
                    <?php } else if( $status == 'invalid' ) { ?>
                    <span style="color:darkred;"><?php _e('invalid'); ?></span>
                    <?php } ?>
                </td>
                <td>
                    <?php submit_button( __('Change License', 'wp-ultimate-post-grid'), 'license_button', null, false ); ?>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>