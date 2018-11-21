<?php

class WPUPG_Template_Date extends WPUPG_Template_Block {

    public $format;

    public $editorField = 'date';

    public function __construct( $type = 'date' )
    {
        parent::__construct( $type );
    }

    public function format( $format )
    {
        $this->format = $format;
        return $this;
    }


    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $output = $this->before_output();

        $output .= '<span' . $this->style() . '>' . date($this->format) . '</span>';

        return $this->after_output( $output, $post );
    }
}