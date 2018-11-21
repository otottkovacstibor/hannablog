<?php

class WPURP_Template_Icon extends WPURP_Template_Block {

    public $name;

    public $editorField = 'icon';

    public function __construct( $type = 'icon' )
    {
        parent::__construct( $type );
    }

    public function name( $name )
    {
        $this->name = $name;
        return $this;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        $icon = file_get_contents( WPUltimateRecipe::get()->coreDir . '/vendor/nucleo/' . $this->name . '.svg' );
        if( !$icon ) return '';

        if( $this->present( $this->settings, 'fontColor' ) ) {
            $icon = str_replace( '#444444', $this->settings->fontColor, $icon );
        }

        $icon = str_replace( 'width="24px"', 'width="100%"', $icon );
        $icon = str_replace( 'height="24px"', 'height="100%"', $icon );

        $output = $this->before_output();
        $output .= '<span' . $this->style() . '>' . $icon . '</span>';

        return $this->after_output( $output, $recipe, $args );
    }
}