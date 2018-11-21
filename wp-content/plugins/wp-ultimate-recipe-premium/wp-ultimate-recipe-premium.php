<?php
/*
Plugin Name: WP Ultimate Recipe Premium
Plugin URI: http://www.wpultimaterecipe.com
Description: Everything a Food Blog needs. Beautiful SEO friendly recipes, print versions, visitor interaction, ...
Version: 2.8.1
Author: Bootstrapped Ventures
Author URI: http://bootstrapped.ventures
License: GPLv2
*/

define( 'WPURP_PREMIUM_VERSION', '2.8.1' );

class WPUltimateRecipePremium {

    private static $instance;

    /**
     * Return instance of self
     */
    public static function get()
    {
        // Instantiate self only once
        if( is_null( self::$instance ) ) {
            self::$instance = new self;
            self::$instance->init();
        }

        return self::$instance;
    }

    public $premiumName = 'wp-ultimate-recipe-premium';
    public $premiumDir;
    public $premiumPath;
    public $premiumUrl;

    private $wpurp;

    /**
     * Our only task is to correctly set up WP Ultimate Recipe and load the premium helpers and addons
     */
    public function init()
    {
        $this->premiumPath = str_replace( '/wp-ultimate-recipe-premium.php', '', plugin_basename( __FILE__ ) );
        $this->premiumDir = apply_filters( 'wpurp_premium_dir', WP_PLUGIN_DIR . '/' . $this->premiumPath . '/premium' );
        $this->premiumUrl = apply_filters( 'wpurp_premium_url', plugins_url() . '/' . $this->premiumPath . '/premium' );

        add_filter( 'wpurp_core_dir', array( $this, 'filter_wpurp_core_dir' ) );
        add_filter( 'wpurp_core_url', array( $this, 'filter_wpurp_core_url' ) );
        add_filter( 'wpurp_plugin_file', array( $this, 'filter_wpurp_plugin_file' ) );

        // Include and instantiate WP Ultimate Recipe
        require_once( WP_PLUGIN_DIR . '/' . $this->premiumPath . '/core/wp-ultimate-recipe.php' );
        $this->wpurp = WPUltimateRecipe::get( true );

        if( !WPUltimateRecipe::minimal_mode() ) {
            // Load WP Ultimate Post Grid Premium
            require_once( WP_PLUGIN_DIR . '/' . $this->premiumPath . '/premium/vendor/wp-ultimate-post-grid-premium/wp-ultimate-post-grid-premium.php' );

            // Load textdomain
            $domain = 'wp-ultimate-recipe';
            $locale = apply_filters('plugin_locale', get_locale(), $domain);

            load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
            load_plugin_textdomain($domain, false, $this->premiumPath . '/core/lang/');

            // Add Premium helper directory
            $this->wpurp->add_helper_directory($this->premiumDir . '/helpers');

            // Load Premium helpers
            $this->wpurp->helper('ingredient_metadata');
            $this->wpurp->helper('license');
            $this->wpurp->helper('recipe_cloner');
            $this->wpurp->helper('recipe_columns');

            $this->wpurp->helper('shortcodes/extended_index_shortcode');

            $this->wpurp->helper('widgets/recipe_list_widget');

            // Load Premium addons
            $this->wpurp->helper('addon_loader')->load_addons($this->premiumDir . '/addons');

            // Add plugin action links
            add_filter('plugin_action_links_' . $this->premiumPath . '/wp-ultimate-recipe-premium.php', array($this->wpurp->helper('plugin_action_link'), 'action_links'));
        }
    }

    public function filter_wpurp_core_dir()
    {
        return WP_PLUGIN_DIR . '/' . $this->premiumPath . '/core';
    }

    public function filter_wpurp_core_url()
    {
        return plugins_url() . '/' . $this->premiumPath . '/core';
    }

    public function filter_wpurp_plugin_file()
    {
        return __FILE__;
    }
}

// Check if WP Ultimate Recipe isn't activated
if( class_exists( 'WPUltimateRecipe' ) ) {
    wp_die( __( "You need to deactivate the free WP Ultimate Recipe plugin before activating the Premium version. WP Ultimate Recipe Premium is a stand-alone plugin since version 2. You won't lose any settings or recipes when deactivating.", 'wp-ultimate-recipe' ), 'WP Ultimate Recipe Premium', array( 'back_link' => true ) );
} else {
    // Instantiate WP Ultimate Recipe Premium
    WPUltimateRecipePremium::get();
}