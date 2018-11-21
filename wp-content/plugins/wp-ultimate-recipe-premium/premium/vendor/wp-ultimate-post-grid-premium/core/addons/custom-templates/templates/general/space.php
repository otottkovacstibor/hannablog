<?php

class WPUPG_Template_Space extends WPUPG_Template_Block {

    public $non_breaking;

    public $editorField = 'space';

    public function __construct( $type = 'space' )
    {
        parent::__construct( $type );
    }

    public function non_breaking( $non_breaking )
    {
        $this->non_breaking = $non_breaking;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $output = $this->before_output();

        $output .= $this->non_breaking ? '&nbsp;' : ' ';

        return $this->after_output( $output, $post );
    }
}