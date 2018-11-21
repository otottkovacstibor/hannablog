<?php

class WPUPG_Template_Post_Date extends WPUPG_Template_Block {

    public $format;

    public $editorField = 'postDate';

    public function __construct( $type = 'post-date' )
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

        $output .= '<span' . $this->style() . '>' . get_the_date( $this->format, $post->ID ) . '</span>';

        return $this->after_output( $output, $post );
    }
}