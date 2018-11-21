<?php
    $rating = WPURP_User_Ratings::get_recipe_rating( $recipe->ID() );

    $classes = '';
    if( WPURP_User_Ratings::is_current_user_allowed_to_vote() ) {
        $classes .= ' user-can-vote';

        $current_user_rating = WPURP_User_Ratings::get_current_user_rating_for( $recipe->ID() );

        if( !$current_user_rating && WPUltimateRecipe::option( 'user_ratings_vote_attention', '1' ) == '1' ) {
            $classes .= ' vote-attention';
        }
    }
?>
<div data-recipe-id="<?php echo $recipe->ID(); ?>" class="user-star-rating recipe-tooltip<?php echo $classes; ?>" data-icon-full="<?php echo esc_attr( $icon_full ); ?>" data-icon-half="<?php echo esc_attr( $icon_half ); ?>" data-icon-empty="<?php echo esc_attr( $icon_empty ); ?>">
    <?php
    for( $i = 1; $i <= 5; $i++ )
    {
        if( $i <= $rating['stars'] ) {
            $icon = $icon_full;
        } else if( $i-1 == $rating['stars'] && $rating['half_star'] == true ) {
            $icon = $icon_half;
        }  else {
            $icon = $icon_empty;
        }

        echo '<i data-star-value="'.$i.'" class="fa ' . esc_attr( $icon ) . '" data-original-icon="' . esc_attr( $icon ) . '"></i>';
    }
    ?>
</div>
<div class="recipe-tooltip-content">
    <div class="user-rating-stats">
        <?php _e( 'Votes', 'wp-ultimate-recipe' ); ?>: <span class="user-rating-votes"><?php echo $rating['votes']; ?></span><br/>
        <?php _e( 'Rating', 'wp-ultimate-recipe' ); ?>: <span class="user-rating-rating"><?php echo $rating['rating']; ?></span><br/>
        <?php if( isset( $current_user_rating ) ) { _e( 'You', 'wp-ultimate-recipe' ); ?>: <span class="user-rating-current-rating"><?php echo $current_user_rating; ?></span><?php } ?>
    </div>
    <div class="vote-attention-message">
        <?php _e( 'Rate this recipe!', 'wp-ultimate-recipe' ); ?>
    </div>
</div>