<?php
define( 'WPUPG_VERSION', '1.9' );
define( 'WPUPG_POST_TYPE', 'wpupg_grid' );

class WPUltimatePostGrid {

    private static $instance;
    private static $instantiated_by_premium;
    private static $addons = array();

    /**
     * Return instance of self
     */
    public static function get( $instantiated_by_premium = true )
    {
        // Instantiate self only once
        if( is_null( self::$instance ) ) {
            self::$instantiated_by_premium = $instantiated_by_premium;
            self::$instance = new self;
            self::$instance->init();
        }

        return self::$instance;
    }

    /**
     * Returns true if we are using the Premium version
     */
    public static function is_premium_active()
    {
        return self::$instantiated_by_premium;
    }

    /**
     * Add loaded addon to array of loaded addons
     */
    public static function loaded_addon( $addon, $instance )
    {
        if( !array_key_exists( $addon, self::$addons ) ) {
            self::$addons[$addon] = $instance;
        }
    }

    /**
     * Returns true if the specified addon has been loaded
     */
    public static function is_addon_active( $addon )
    {
        return array_key_exists( $addon, self::$addons );
    }

    public static function addon( $addon )
    {
        if( isset( self::$addons[$addon] ) ) {
            return self::$addons[$addon];
        }

        return false;
    }

    /**
     * Access a VafPress option with optional default value
     */
    public static function option( $name, $default = null )
    {
        $option = vp_option( 'wpupg_option.' . $name );

        return is_null( $option ) ? $default : $option;
    }


    public $pluginName = 'wp-ultimate-post-grid';
    public $coreDir;
    public $corePath;
    public $coreUrl;
    public $pluginFile;

    protected $helper_dirs = array();
    protected $helpers = array();

    /**
     * Initialize
     */
    public function init()
    {
        // Load external libraries
        if( !class_exists( 'VP_AutoLoader' ) ) {
            require_once( 'vendor/vafpress/bootstrap.php' );
        }

        // Update plugin version
        update_option( $this->pluginName . '_version', WPUPG_VERSION );

        // Set core directory, URL and main plugin file
        $this->corePath = str_replace( '/wp-ultimate-post-grid.php', '', plugin_basename( __FILE__ ) );
        $this->coreDir = apply_filters( 'wpupg_core_dir', WP_PLUGIN_DIR . '/' . $this->corePath );
        $this->coreUrl = apply_filters( 'wpupg_core_url', plugins_url() . '/' . $this->corePath );
        $this->pluginFile = apply_filters( 'wpupg_plugin_file', __FILE__ );

        // Load textdomain
        if( !self::is_premium_active() ) {
            $domain = 'wp-ultimate-post-grid';
            $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

            load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
            load_plugin_textdomain( $domain, false, $this->corePath . '/lang/' );
        }

        // Add core helper directory
        $this->add_helper_directory( $this->coreDir . '/helpers' );

        // Migrate first if needed
        $this->helper( 'migration' );

        // Load required helpers
        $this->helper( 'activate' );
        $this->helper( 'ajax' );
        $this->helper( 'content' );
        $this->helper( 'css' );
        $this->helper( 'faq' );
        $this->helper( 'giveaway' );
        $this->helper( 'grid_cache' );
        $this->helper( 'grid_save' );
        $this->helper( 'meta_box' );
        $this->helper( 'meta_box_post' );
        $this->helper( 'notices' );
        $this->helper( 'pagination' );
        $this->helper( 'plugin_action_link' );
        $this->helper( 'post_save' );
        $this->helper( 'post_type' );
        $this->helper( 'support_tab' );
        $this->helper( 'vafpress_menu' );
        $this->helper( 'vafpress_shortcode' );
        $this->helper( 'shortcodes/filter_shortcode' );
        $this->helper( 'shortcodes/grid_shortcode' );

        // Include required helpers but don't instantiate
        $this->include_helper( 'addons/addon' );
        $this->include_helper( 'addons/premium_addon' );
        $this->include_helper( 'models/grid' );

        // Load core addons
        $this->helper( 'addon_loader' )->load_addons( $this->coreDir . '/addons' );

        // Load default assets
        $this->helper( 'assets' );
    }

    /**
     * Access a helper. Will instantiate if helper hasn't been loaded before.
     */
    public function helper( $helper )
    {
        // Lazy instantiate helper
        if( !isset( $this->helpers[$helper] ) ) {
            $this->include_helper( $helper );

            // Get class name from filename
            $class_name = 'WPUPG';

            $dirs = explode( '/', $helper );
            $file = end( $dirs );
            $name_parts = explode( '_', $file );
            foreach( $name_parts as $name_part ) {
                $class_name .= '_' . ucfirst( $name_part );
            }

            // Instantiate class if exists
            if( class_exists( $class_name ) ) {
                $this->helpers[$helper] = new $class_name();
            }
        }

        // Return helper instance
        return $this->helpers[$helper];
    }

    /**
     * Include a helper. Looks through all helper directories that have been added.
     */
    public function include_helper( $helper )
    {
        foreach( $this->helper_dirs as $dir )
        {
            $file = $dir . '/'.$helper.'.php';

            if( file_exists( $file ) ) {
                require_once( $file );
            }
        }
    }

    /**
     * Add a directory to look for helpers.
     */
    public function add_helper_directory( $dir )
    {
        if( is_dir( $dir ) ) {
            $this->helper_dirs[] = $dir;
        }
    }

    /*
     * Quick access functions
     */

    public function template( $template )
    {
        return $this->addon( 'custom-templates' )->get_template( $template );
    }
}

// Premium version is responsible for instantiating if available
if( !class_exists( 'WPUltimatePostGridPremium' ) ) {
    WPUltimatePostGrid::get();
}