<?php

class WPURP_Template_Recipe_Stars extends WPURP_Template_Block {

    public $editorField = 'recipeStars';

    public $icon_full = 'fa-star';
    public $icon_half = 'fa-star-half-o';
    public $icon_empty = 'fa-star-o';

    public function __construct( $type = 'recipe-stars' )
    {
        parent::__construct( $type );
    }

    public function icon_full( $icon_full )
    {
        $this->icon_full = $icon_full;
        return $this;
    }

    public function icon_half( $icon_half )
    {
        $this->icon_half = $icon_half;
        return $this;
    }

    public function icon_empty( $icon_empty )
    {
        $this->icon_empty = $icon_empty;
        return $this;
    }

    // TODO Better integration with user ratings
    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        if( WPUltimateRecipe::is_addon_active( 'user-ratings' ) && WPUltimateRecipe::option( 'user_ratings_enable', 'everyone' ) != 'disabled' ) {
            $stars =  WPUltimateRecipe::addon( 'user-ratings' )->output( $recipe, $this->icon_full, $this->icon_half, $this->icon_empty );
        } else {
            $stars =  $this->stars_author( $recipe );
        }

        $output = $this->before_output();
        $output .= '<span' . $this->style() . '>' . $stars . '</span>';

        return $this->after_output( $output, $recipe, $args );
    }

    private function stars_author( $recipe )
    {
        $star_full = '<i class="fa ' . esc_attr( $this->icon_full ) . '"></i>';
        $star_empty = '<i class="fa ' . esc_attr( $this->icon_empty ) . '"></i>';

        $rating = $recipe->rating_author();
        $stars = '';

        if( $rating != 0 )
        {
            for( $i = 1; $i <= 5; $i++ )
            {
                if( $i <= $rating ) {
                    $stars .= $star_full;
                } else {
                    $stars .= $star_empty;
                }
            }
        }

        return $stars;
    }
}