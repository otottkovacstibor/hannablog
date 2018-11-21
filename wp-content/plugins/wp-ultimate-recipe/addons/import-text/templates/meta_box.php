<input class="recipe_import_text button" type="button" value="<?php _e( 'Import Recipe from Plain Text', 'wp-ultimate-recipe' ) ?>" />
<div class="recipe_import_text_container">
    <div class="recipe_import_text_plain_container">
        <label for="recipe_import_text_plain"><?php _e( 'Paste your plain text recipe here', 'wp-ultimate-recipe' ); ?>:</label>
        <textarea id="recipe_import_text_plain"></textarea>
    </div>
    <h4><?php _e( 'Define Recipe Regions', 'wp-ultimate-recipe' );?></h4>
    <p><?php _e( 'Highlight a section in your recipe and click the corresponding button to define it.', 'wp-ultimate-recipe' );?></p>
    <table id="recipe_import_text_regions_container" class="recipe_import_text_table">
        <tbody>
        <tr>
            <td>
                <ul id="recipe_region_buttons">
                    <li><input type="button" id="recipe_region_title" class="recipe_region_button button" value="<?php _e( 'Title', 'wp-ultimate-recipe' );?>" /></li>
                    <li><input type="button" id="recipe_region_description" class="recipe_region_button button" value="<?php _e( 'Description', 'wp-ultimate-recipe' );?>" /></li>
                    <li><input type="button" id="recipe_region_ingredients" class="recipe_region_button button" value="<?php _e( 'Ingredients', 'wp-ultimate-recipe' );?>" /></li>
                    <li><input type="button" id="recipe_region_instructions" class="recipe_region_button button" value="<?php _e( 'Instructions', 'wp-ultimate-recipe' );?>" /></li>
                    <li><input type="button" id="recipe_region_notes" class="recipe_region_button button" value="<?php _e( 'Notes', 'wp-ultimate-recipe' );?>" /></li>
                </ul>
            </td>
            <td>
                <div id="recipe_region_text"></div>
            </td>
        </tr>
        </tbody>
    </table>
    <h4><?php _e( 'Ingredients', 'wp-ultimate-recipe' );?></h4>
    <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Lines starting with a ! will be starting a new group.', 'wp-ultimate-recipe' ); ?></p>
    <table id="recipe_import_define_ingredient_lines" class="recipe_import_text_table">
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
    <h4><?php _e( 'Ingredient Details', 'wp-ultimate-recipe' );?></h4>
    <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Only unit aliases defined in the settings will be recognized.', 'wp-ultimate-recipe' ); ?></p>
    <table id="recipe_import_define_ingredient_details" class="recipe_import_text_table">
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

    <h4><?php _e( 'Instructions', 'wp-ultimate-recipe' );?></h4>
    <p><strong><?php _e( 'Tip', 'wp-ultimate-recipe' ); ?>:</strong> <?php _e( 'Lines starting with a ! will be starting a new group.', 'wp-ultimate-recipe' ); ?></p>
    <select id="recipe_instructions_separate_type">
        <option value="every_line"><?php _e( 'Every line is a separate instruction', 'wp-ultimate-recipe' );?></option>
        <option value="empty_line"><?php _e( 'Separate instructions with an empty line', 'wp-ultimate-recipe' );?></option>
    </select>
    <table id="recipe_import_define_instruction_lines" class="recipe_import_text_table">
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
    <input class="recipe_import_text_use button button-primary" type="button" value="<?php _e( 'Use', 'wp-ultimate-recipe' ) ?>" />
    <input class="recipe_import_text_cancel button" type="button" value="<?php _e( 'Cancel', 'wp-ultimate-recipe' ) ?>" />
</div>