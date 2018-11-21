<div class="wrap wpurp-import">

    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Import XML', 'wp-ultimate-recipe' ); ?></h2>
    <h3><?php _e( 'Before importing', 'wp-ultimate-recipe' ); ?></h3>
    <ol>
        <li><?php _e( "It's a good idea to backup your WP database before using the import feature.", 'wp-ultimate-recipe' ); ?></li>
        <li>Select the XML file containing recipes in the WP Ultimate Recipe format:</li>
    </ol>
    <form method="POST" action="<?php echo admin_url( 'edit.php?post_type=recipe&page=wpurp_import_xml_manual' ); ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="import_xml_manual">
        <?php wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
        <input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce('recipe'); ?>" />
        <input type="file" name="xml">
        <?php submit_button( __( 'Import XML', 'wp-ultimate-recipe' ) ); ?>
    </form>
</div>