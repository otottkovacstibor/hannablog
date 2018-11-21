<?php

class WPURP_Getting_Started {

    public function __construct()
    {
        add_action( 'current_screen', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'hide' ) );
    }

    public function init() {
        $screen = get_current_screen();
        if( $screen->post_type == 'recipe' && current_user_can( 'manage_options' ) && get_user_meta( get_current_user_id(), '_wpurp_hide_getting_started', true ) == '' ) {
            add_action( 'admin_print_scripts', array( $this, 'js' ) );
            add_action( 'admin_print_styles', array( $this, 'css' ) );
            add_action( 'admin_footer', array( $this, 'lightbox' ) );
        }
    }

    public function js() {
        wp_enqueue_script( 'thickbox' );
    }

    public function css() {
        wp_enqueue_style( 'thickbox' );
    }

    public function lightbox()
    {
        ?>
        <div id="wpurp-getting-started-lightbox" style="display:none;">
            <h1>Welcome!</h1>
            <p>
                If this is your first time using WP Ultimate Recipe we strongly recommend watching this short video introductions covering the <strong>things you need to know</strong> about this plugin:
            </p>
            <p style="text-align: center;">
                <iframe width="420" height="315" src="https://www.youtube.com/embed/ePd2TFCw8aw" frameborder="0" allowfullscreen></iframe>
            </p>
            <form class="wpurp-getting-started-form" action="https://www.getdrip.com/forms/3799019/submissions" method="post" target="_blank" data-drip-embedded-form="3799019">
                <h3 data-drip-attribute="headline">Mastering WP Ultimate Recipe</h3>
                <p data-drip-attribute="description">Get the most out of this recipe plugin by learning the ins and outs of WP Ultimate Recipe in this free email course.</p>
                <div class="input-fields">
                    <label for="fields[email]">Email Address</label><input type="email" name="fields[email]" value="<?php echo wp_get_current_user()->user_email; ?>" />
                </div>
                <div class="input-buttons">
                    <?php $hide_url = esc_url( add_query_arg( array('wpurp_hide_getting_started' => wp_create_nonce( 'wpurp_hide_getting_started' ) ) ) ); ?>
                    <a href="<?php echo $hide_url; ?>" class="button">I'm not interested</a>
                    <input type="submit" class="button button-primary" data-url="<?php echo $hide_url; ?>" name="submit" id="wpurp-getting-started-submit" value="I want to learn more!" data-drip-attribute="sign-up-button" />
                </div>
            </form>
        </div>
        <?php
    }

    public function hide()
    {
        if ( isset( $_GET['wpurp_hide_getting_started'] ) ) {
            check_admin_referer( 'wpurp_hide_getting_started', 'wpurp_hide_getting_started' );
            update_user_meta( get_current_user_id(), '_wpurp_hide_getting_started', get_option( WPUltimateRecipe::get()->pluginName . '_version' ) );
        }
    }
}