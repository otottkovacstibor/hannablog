<?php

class WPUPG_Template_Post_Custom_Field extends WPUPG_Template_Block {

    public $editorField = 'postCustomField';
    public $key;

    public function __construct( $type = 'custom-field' )
    {
        parent::__construct( $type );
    }

    public function key( $key )
    {
        $this->key = $key;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $output = $this->before_output();

        if( !$this->key || !get_post_meta( $post->ID, $this->key, true ) ) return '';

        $output .= '<span' . $this->style() . '>' . $this->cut_off( get_post_meta( $post->ID, $this->key, true ) ) . '</span>';

        return $this->after_output( $output, $post );
    }
}