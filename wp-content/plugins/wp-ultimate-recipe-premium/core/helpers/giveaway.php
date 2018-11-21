<?php

class WPURP_Giveaway {

    public function __construct()
    {
        add_action( 'admin_init',       array( $this, 'hide_notice' ) );
        add_action( 'admin_notices',    array( $this, 'giveaway_notice' ) );
    }

    public function giveaway_notice()
    {
        $now = new DateTime();
        $giveaway_end = new DateTime('2016-01-29');

        if( $now < $giveaway_end && current_user_can( 'manage_options' ) && get_user_meta( get_current_user_id(), '_bv_birthday_giveaway_2016', true ) == '' ) {
            ?>
            <div class="updated bv_notice">
                <div class="bv_notice_dismiss" style="float: right;">
                    <a href="<?php echo esc_url( add_query_arg( array('bv_hide_notice' => wp_create_nonce( 'bv_hide_notice' ) ) ) ); ?>"> <?php _e( 'Hide this message', 'wp-ultimate-recipe' ); ?></a>
                </div>
                <h3>Win a Premium license</h3>
                <p>It's (almost) my birthday and we're <a href="http://bootstrapped.ventures/giveaway/" target="_blank"><strong>giving away Premium licenses</strong></a> for WP Ultimate Recipe, WP Ultimate Post Grid and Easy Image Collage. There's a consolation prize for every participant, so don't miss out!</p>
            </div>
            <?php
        }
    }

    function hide_notice()
    {
        if ( isset( $_GET['bv_hide_notice'] ) ) {
            check_admin_referer( 'bv_hide_notice', 'bv_hide_notice' );
            update_user_meta( get_current_user_id(), '_bv_birthday_giveaway_2016', 'hidden' );
        }
    }
}