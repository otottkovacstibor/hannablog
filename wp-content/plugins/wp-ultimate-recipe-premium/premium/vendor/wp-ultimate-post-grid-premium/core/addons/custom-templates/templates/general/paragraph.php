<?php

class WPUPG_Template_Paragraph extends WPUPG_Template_Block {

    public $text;

    public $editorField = 'paragraph';

    public function __construct( $type = 'paragraph' )
    {
        parent::__construct( $type );
    }

    public function text( $text )
    {
        $this->text = $text;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $output = $this->before_output();
        $output .= '<div' . $this->style() . '>' . $this->text . '</div>';

        return $this->after_output( $output, $post );
    }
}