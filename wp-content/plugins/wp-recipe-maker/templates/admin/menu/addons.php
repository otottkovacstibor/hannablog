<?php
/**
 * Template for the addons page.
 *
 * @link       http://bootstrapped.ventures
 * @since      1.5.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/admin/menu
 */

?>

<div class="wrap wprm-addons">
	<h1><?php echo esc_html_e( 'Add-Ons', 'wp-recipe-maker' ); ?></h1>
	<div class="wprm-addons-bundle-container">
		<h2>WP Recipe Maker Premium</h2>
		<?php if ( WPRM_Addons::is_active( 'premium' ) ) : ?>
		<p>This add-on is active.</p>
		<?php else : ?>
		<ul>
			<li>Use <strong>ingredient links</strong> for linking to products or other recipes</li>
			<li><strong>Adjustable servings</strong> make it easy for your visitors</li>
			<li>Display all nutrition data in a <strong>nutrition label</strong></li>
			<li><strong>User Ratings</strong> allow visitors to vote without commenting</li>
			<li>Add a mobile-friendly <strong>kitchen timer</strong> to your recipes</li>
			<li>More <strong>Premium templates</strong> for a unique recipe template</li>
			<li>Create custom <strong>recipe taxonomies</strong> like price level, difficulty, ...</li>
			<li>Use <strong>checkboxes</strong> for your ingredients and instructions</li>
		</ul>
		<div class="wprm-addons-button-container">
			<a class="button button-primary" href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/" target="_blank">Learn More</a>
		</div>
		<?php endif; // Premium active. ?>
	</div>

	<div class="wprm-addons-bundle-container">
		<h2>Pro Bundle</h2>
		<?php if ( WPRM_Addons::is_active( 'nutrition' ) ) : ?>
		<p>This add-on is active.</p>
		<?php else : ?>
		<h3>Advanced Nutrition</h3>
		<ul>
			<li>Integration with a <strong>Nutrition API</strong> for automatic nutrition facts</li>
		</ul>
		<div class="wprm-addons-button-container">
			<a class="button button-primary" href="https://help.bootstrapped.ventures/article/21-nutrition-facts-calculation" target="_blank">Learn More</a>
		</div>
		<h3>Unit Conversion</h3>
		<ul>
			<li>Define a second unit system for your ingredients</li>
			<li>Allow visitors to easily switch back and forth</li>
			<li>Automatically calculate quantities and units for the second system</li>
			<li>Manually adjust anything for full control</li>
		</ul>
		<div class="wprm-addons-button-container">
			<a class="button button-primary" href="https://help.bootstrapped.ventures/article/18-unit-conversion" target="_blank">Learn More</a>
		</div>
		<?php endif; // Pro Bundle active. ?>
	</div>

	<div class="wprm-addons-bundle-container">
		<h2>Elite Bundle</h2>
		<?php if ( WPRM_Addons::is_active( 'recipe-submission' ) ) : ?>
		<p>This add-on is active.</p>
		<?php else : ?>
		<h3>Recipe Submission</h3>
		<ul>
			<li>Have your <strong>visitors submit recipes</strong> through your website</li>
		</ul>
		<div class="wprm-addons-button-container">
			<a class="button button-primary" href="https://help.bootstrapped.ventures/article/33-recipe-submisssion" target="_blank">Learn More</a>
		</div>
		<?php endif; // Elite Bundle active. ?>
	</div>
</div>
