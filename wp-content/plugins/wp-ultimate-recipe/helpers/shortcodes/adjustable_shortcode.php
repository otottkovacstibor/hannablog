<?php

class WPURP_Adjustable_Shortcode {

    public function __construct()
    {
        add_shortcode( 'adjustable', array( $this, 'adjustable_shortcode' ) );
    }

    function adjustable_shortcode( $option, $content )
    {
        return '<span class="wpurp-adjustable-quantity">' . $content . '</span>';
    }
}