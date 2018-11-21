<?php

class WPURP_Template_Recipe_Link extends WPURP_Template_Block {

    public $editorField = 'recipeLink';

    public $title = false;
    public $link = false;
    public $target = '';

    public function __construct( $type = 'recipe-link' )
    {
        parent::__construct( $type );
    }

    public function title( $title )
    {
        $this->title = $title;
        return $title;
    }

    public function link( $link )
    {
        $this->link = $link;
        return $this;
    }

    public function target( $target )
    {
        $this->target = $target;
        return $this;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        $output = $this->before_output();

        $text = $this->link && $this->title ? $recipe->title() : $recipe->link();

        if( $this->link ) {
            $output .= '<a href="' . $recipe->link() . '" target="' . $this->target . '"' . $this->style() . '>' . $text . '</a>';
        } else {
            $output .= '<span' . $this->style() . '>' . $text . '</span>';
        }

        return $this->after_output( $output, $recipe, $args );
    }
}