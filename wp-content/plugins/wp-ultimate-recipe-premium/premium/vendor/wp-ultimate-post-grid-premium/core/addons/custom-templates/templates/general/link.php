<?php

class WPUPG_Template_Link extends WPUPG_Template_Block {

    public $text;
    public $url = '#';
    public $target = '';

    public $editorField = 'link';

    public function __construct( $type = 'link' )
    {
        parent::__construct( $type );
    }

    public function text( $text )
    {
        $this->text = $text;
        return $this;
    }

    public function url( $url )
    {
        $this->url = $url;
        return $this;
    }

    public function target( $target )
    {
        $this->target = $target;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $output = $this->before_output();
        $output .= '<a href="' . $this->url . '" target="' . $this->target . '"'. $this->style() .'>' . $this->text . '</a>';

        return $this->after_output( $output, $post );
    }
}