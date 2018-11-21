<?php
define( 'WPUPG_PREMIUM_VERSION', '1.9' );

class WPUltimatePostGridPremium {

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

    public $premiumName = 'wp-ultimate-post-grid-premium';
    public $premiumDir;
    public $premiumPath;
    public $premiumUrl;

    private $wpupg;

    /**
     * Our only task is to correctly set up WP Ultimate Recipe and load the premium helpers and addons
     */
    public function init()
    {
        $this->premiumPath = str_replace( '/wp-ultimate-post-grid-premium.php', '', plugin_basename( __FILE__ ) );
        $this->premiumDir = apply_filters( 'wpupg_premium_dir', WP_PLUGIN_DIR . '/' . $this->premiumPath . '/premium' );
        $this->premiumUrl = apply_filters( 'wpupg_premium_url', plugins_url() . '/' . $this->premiumPath . '/premium' );

        add_filter( 'wpupg_core_dir', array( $this, 'filter_wpupg_core_dir' ) );
        add_filter( 'wpupg_core_url', array( $this, 'filter_wpupg_core_url' ) );
        add_filter( 'wpupg_plugin_file', array( $this, 'filter_wpupg_plugin_file' ) );

        // Include and instantiate WP Ultimate Post Grid
        require_once( WP_PLUGIN_DIR . '/' . $this->premiumPath . '/core/wp-ultimate-post-grid.php' );
        $this->wpupg = WPUltimatePostGrid::get( true );

        // Load textdomain
        $domain = 'wp-ultimate-post-grid';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, $this->premiumPath . '/core/lang/');

        // Add Premium helper directory
        $this->wpupg->add_helper_directory($this->premiumDir . '/helpers');

        // Load Premium helpers
        $this->wpupg->helper('admin_columns');
        $this->wpupg->helper('cloner');

        // Don't load licensing code when using as a part of WP Ultimate Recipe Premium
        if( !class_exists( 'WPUltimateRecipePremium' ) ) {
            $this->wpupg->helper('license');
        }

        // Load Premium addons
        $this->wpupg->helper('addon_loader')->load_addons($this->premiumDir . '/addons');

        // Add plugin action links
        add_filter( 'plugin_action_links_' . $this->premiumPath . '/wp-ultimate-post-grid-premium.php', array( $this->wpupg->helper( 'plugin_action_link' ), 'action_links' ) );
    }

    public function filter_wpupg_core_dir()
    {
        return WP_PLUGIN_DIR . '/' . $this->premiumPath . '/core';
    }

    public function filter_wpupg_core_url()
    {
        return plugins_url() . '/' . $this->premiumPath . '/core';
    }

    public function filter_wpupg_plugin_file()
    {
        return __FILE__;
    }
}

// Check if WP Ultimate Recipe isn't activated
if( class_exists( 'WPUltimatePostGrid' ) ) {
    wp_die( __( "You need to deactivate the free WP Ultimate Post Grid plugin before activating the Premium version. You won't lose any settings or grids when deactivating.", 'wp-ultimate-post-grid' ), 'WP Ultimate Post Grid Premium', array( 'back_link' => true ) );
} else {
    // Instantiate WP Ultimate Post Grid Premium
    WPUltimatePostGridPremium::get();
}