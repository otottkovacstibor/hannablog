<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e( 'Add New Recipe from text', 'wp-ultimate-recipe' );?></h2>
    <p><?php _e( 'This is function is still in beta version. Any feedback is welcome!', 'wp-ultimate-recipe' ); ?></p>
    <h3><?php _e( 'Plain Text Recipe Input', 'wp-ultimate-recipe' );?></h3>
    <p><?php _e( 'Paste your recipe in this input field.', 'wp-ultimate-recipe' );?></p>
    <textarea id="input-recipe"></textarea>
    <h3><?php _e( 'Define Recipe Regions', 'wp-ultimate-recipe' );?></h3>
    <p><?php _e( 'Highlight a section in your recipe and click the corresponding button to define it.', 'wp-ultimate-recipe' );?></p>
    <table id="define-regions-container" class="import-table">
        <tbody>
        <tr>
            <td>
                <ul id="recipe_region_buttons">
                    <li><button id="recipe_region_title" class="recipe_region_button"><?php _e( 'Title', 'wp-ultimate-recipe' );?></button></li>
                    <li><button id="recipe_region_description" class="recipe_region_button"><?php _e( 'Description', 'wp-ultimate-recipe' );?></button></li>
                    <li><button id="recipe_region_ingredients" class="recipe_region_button"><?php _e( 'Ingredients', 'wp-ultimate-recipe' );?></button></li>
                    <li><button id="recipe_region_instructions" class="recipe_region_button"><?php _e( 'Instructions', 'wp-ultimate-recipe' );?></button></li>
                    <li><button id="recipe_region_notes" class="recipe_region_button"><?php _e( 'Notes', 'wp-ultimate-recipe' );?></button></li>
                </ul>
            </td>
            <td>
                <div id="recipe_region_text"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <form name="import_recipe" id="import_recipe" method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="post" />
        <?php wp_nonce_field( 'recipe_submit', 'submitrecipe' ); ?>
        <input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce('recipe'); ?>" />

    <h3><?php _e( 'General', 'wp-ultimate-recipe' );?></h3>
    <table class="form-table" class="import-table">
        <tbody>
        <tr>
            <th scope="row"><?php _e( 'Title', 'wp-ultimate-recipe' ); ?></th>
            <td>
                <input type="text" name="recipe_title" id="recipe_title" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Description', 'wp-ultimate-recipe' ); ?></th>
            <td>
                <textarea name="recipe_description" id="recipe_description" rows="4"></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e( 'Notes', 'wp-ultimate-recipe' ); ?></th>
            <td>
                <textarea name="recipe_notes" id="recipe_notes" rows="4"></textarea>
            </td>
        </tr>
        </tbody>
    </table>
    <h3><?php _e( 'Ingredients', 'wp-ultimate-recipe' );?></h3>
        <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Lines starting with a ! will be starting a new group.', 'wp-ultimate-recipe' ); ?></p>
    <table id="define-ingredient-lines" class="import-table">
        <tbody>
        <tr>
            <td>
                <textarea name="raw_recipe_ingredients" id="raw_recipe_ingredients"></textarea>
            </td>
            <td>
                <ul id="raw_recipe_ingredients_lines">
                </ul>
            </td>
        </tr>
        </tbody>
    </table>

    </table>
    <h3><?php _e( 'Ingredient Details', 'wp-ultimate-recipe' );?></h3>
    <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Only unit aliases defined in the settings will be recognized.', 'wp-ultimate-recipe' ); ?></p>
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

    <h3><?php _e( 'Instructions', 'wp-ultimate-recipe' );?></h3>
    <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Lines starting with a ! will be starting a new group.', 'wp-ultimate-recipe' ); ?></p>
    <select id="recipe_instructions_separate_type">
        <option value="empty_line"><?php _e( 'Separate instructions with an empty line', 'wp-ultimate-recipe' );?></option>
        <option value="every_line"><?php _e( 'Every line is a separate instruction', 'wp-ultimate-recipe' );?></option>
    </select>
    <table id="define-instruction-lines" class="import-table">
        <tbody>
        <tr>
            <td>
                <textarea name="raw_recipe_instructions" id="raw_recipe_instructions"></textarea>
            </td>
            <td>
                <ol id="raw_recipe_instructions_lines">
                </ol>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="recipe_instructions_output"></div>
        <?php submit_button( __( 'Add Recipe', 'wp-ultimate-recipe' ) ); ?>
    </form>
</div>