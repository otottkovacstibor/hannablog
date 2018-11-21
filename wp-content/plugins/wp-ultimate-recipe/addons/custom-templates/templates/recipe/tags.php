<?php

class WPURP_Template_Recipe_Tags extends WPURP_Template_Block {

    public $editorField = 'recipeTags';
    public $is_list;
    public $list_style;

    public function __construct( $type = 'recipe-tags' )
    {
        parent::__construct( $type );
    }

    public function is_list( $is_list )
    {
        $this->is_list = $is_list;
        return $this;
    }

    public function list_style( $list_style )
    {
        $this->list_style = $list_style;
        return $this;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        // Backwards compatibility
        if( empty( $this->children ) ) {
            $output = $this->default_output( $recipe );
        } else {

            if( $this->is_list ) {
                if( in_array( $this->list_style, array( 'none', 'circle', 'disc', 'square' ) ) ) {
                    $tag = 'ul';
                } else {
                    $tag = 'ol';
                }

                $sub_tag = 'li';

                $this->add_style( 'list-style', $this->list_style, 'li' );
            } else {
                $tag = 'div';
                $sub_tag = 'div';
            }

            $args['desktop'] = $args['desktop'] && $this->show_on_desktop;
            $output = $this->before_output();

            ob_start();
?>
<<?php echo $tag . $this->style(); ?>>
    <?php
    foreach( $this->tags_list( $recipe ) as $tag_name => $tag_terms ) {
        $slug = strtolower( str_replace( ' ', '-', $tag_name ) );
        ?>
        <<?php echo $sub_tag . $this->style( 'li' ); ?> class="wpurp-recipe-tags-<?php echo $slug; ?>">
            <?php
                $child_args = array_merge( $args, array(
                    'tag_name' => $tag_name,
                    'tag_terms' => $tag_terms,
                ) );

                $this->output_children( $recipe, 0, 0, $child_args );
            ?>
        </<?php echo $sub_tag; ?>>
    <?php
    }
    ?>
</<?php echo $tag; ?>>
<?php
            $output .= ob_get_contents();
            ob_end_clean();
        }

        return $this->after_output( $output, $recipe, $args );
    }

    /**
     * TODO Refactor this.
     */
    private function tags_list( $recipe )
    {
        $tags = array();

        $tag_links = WPUltimateRecipe::option( 'recipe_tags_links', 'archive_custom' );
        $taxonomies = WPUltimateRecipe::get()->tags();
        unset( $taxonomies['ingredient'] );

        foreach( $taxonomies as $taxonomy => $options ) {
            if( !in_array( $taxonomy, WPUltimateRecipe::option('recipe_tags_hide_in_recipe', array() ) ) ) {
                $terms = get_the_terms( $recipe->ID(), $taxonomy );
                if( $terms && ! is_wp_error( $terms ) ) {
                    $links = array();
                    foreach( $terms as $term ) {
                        $opening_tag = '';
                        $closing_tag = '';
                        if( $tag_links !== 'disabled' ) {

                            if( $tag_links == 'archive_custom' || $tag_links == 'custom' ) {
                                $custom_link = WPURP_Taxonomy_MetaData::get( $taxonomy, $term->slug, 'wpurp_link' );
                            } else {
                                $custom_link = false;
                            }

                            if( $custom_link !== false && $custom_link !== '' ) {
                                $nofollow = WPUltimateRecipe::option( 'recipe_tags_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

                                $opening_tag = '<a href="'.$custom_link.'" class="custom-tag-link" target="'.WPUltimateRecipe::option( 'recipe_tags_custom_links_target', '_blank' ).'"' . $nofollow . '>';
                                $closing_tag = '</a>';
                            } else if( $tag_links != 'custom' ) {
                                $opening_tag = '<a href="'.get_term_link( $term->slug, $taxonomy ).'">';
                                $closing_tag = '</a>';
                            }
                        }

                        $links[] = $opening_tag . $term->name . $closing_tag;
                    }

                    // Get name at this point in time to have correct WPML translation
                    $tax = get_taxonomy( $taxonomy );
                    $label = __( $tax->labels->singular_name, 'wp-ultimate-recipe' );
                    $tags[$label] = join( ', ', $links );
                }
            }
        }

        // Categories as tags
        if( WPUltimateRecipe::is_addon_active('custom-taxonomies') && WPUltimateRecipe::option('recipe_tags_show_in_recipe', '0') == '1' )
        {
            $categories = wp_get_post_categories( $recipe->ID() );
            $category_groups = array();

            foreach( $categories as $category ){
                $cat = get_category( $category );

                if( !is_null( $cat->parent ) && $cat->parent != 0 )
                {
                    $category_groups[$cat->parent][] = $cat;
                }
            }

            foreach( $category_groups as $group => $categories )
            {
                $group_category = get_category( $group );
                $group_name = $group_category->name;

                $cats = array();
                foreach( $categories as $cat )
                {
                    $link = get_category_link( $cat->cat_ID );
                    $cats[] = '<a href="'.$link.'">'.$cat->name.'</a>';
                }

                $tags[$group_name] = implode( ', ', $cats );
            }
        }

        return apply_filters( 'wpurp_output_recipe_block_recipe-tags_terms', $tags, $recipe );
    }

    public function default_output( $recipe )
    {
        $this->add_style( 'list-style', 'none' );
        $this->add_style( 'list-style', 'none', 'li' );
        $this->add_style( 'line-height', '1.5em', 'li' );
        $this->add_style( 'display', 'inline-block', 'name' );
        $this->add_style( 'width', '100px', 'name' );
        $this->add_style( 'font-weight', 'bold', 'name' );

        $output = $this->before_output();

        ob_start();
?>
<ul<?php echo $this->style(); ?>>
    <?php
    foreach( $this->tags_list( $recipe) as $tag => $terms ) {
        $slug = strtolower( str_replace( ' ', '-', $tag ) );
        ?>
        <li class="wpurp-recipe-tags-<?php echo $slug; ?>"<?php echo $this->style('li'); ?>>
            <span class="recipe-tag-name"<?php echo $this->style('name'); ?>><?php echo $tag; ?></span>
            <span class="recipe-tags"<?php echo $this->style('terms'); ?>>
                <?php echo $terms; ?>
            </span>
        </li>
    <?php
    }
    ?>
</ul>
<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $output;
    }
}