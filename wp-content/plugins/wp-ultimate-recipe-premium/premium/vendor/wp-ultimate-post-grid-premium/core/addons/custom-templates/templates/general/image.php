<?php

class WPUPG_Template_Image extends WPUPG_Template_Block {

    public $url;
    public $preset = false;

    public $editorField = 'image';

    public function __construct( $type = 'image' )
    {
        parent::__construct( $type );
    }

    public function url( $url )
    {
        $this->url = $url;
        return $this;
    }

    public function preset( $preset )
    {
        $this->preset = $preset;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        if( $this->preset ) {
            $this->url = WPUltimatePostGrid::addon( 'template-editor' )->addonUrl . '/img/' . $this->preset . '.png';
        }

        $output = $this->before_output();
        $output .= '<img src="' . $this->url . '"' . $this->style() . '\>';

        return $this->after_output( $output, $post );
    }
}