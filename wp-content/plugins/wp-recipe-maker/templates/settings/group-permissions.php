<?php
/**
 * Template for the plugin settings structure.
 *
 * @link       http://bootstrapped.ventures
 * @since      3.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/templates/settings
 */

$permissions = array(
	'id' => 'permissions',
	'name' => __( 'Permissions', 'wp-recipe-maker' ),
	'description' => __( 'Set the role (administrator, editor, ...) or capability (manage_options, edit_posts, ...) required to access specific features.', 'wp-recipe-maker' ),
	'documentation' => 'https://codex.wordpress.org/Roles_and_Capabilities',
	'settings' => array(
		array(
			'id' => 'features_manage_access',
			'name' => __( 'Access to Manage Page', 'wp-recipe-maker' ),
			'type' => 'text',
			'default' => 'administrator',
		),
		array(
			'id' => 'features_tools_access',
			'name' => __( 'Access to Tools Page', 'wp-recipe-maker' ),
			'type' => 'text',
			'default' => 'administrator',
		),
		array(
			'id' => 'features_import_access',
			'name' => __( 'Access to Import Page', 'wp-recipe-maker' ),
			'type' => 'text',
			'default' => 'administrator',
		),
	),
);
