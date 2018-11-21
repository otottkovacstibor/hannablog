<?php

class WPUPG_Template {

    public $blocks;
    public $container;
    public $fonts;

    public function __construct( $blocks, $fonts = array() )
    {
        $this->fonts = $fonts;

        // Set object blocks before sorting to maintain index
        $this->blocks = $blocks;

        // Sort according to block order
        usort( $blocks, array( $this, 'sortByOrder' ) );

        // Generate children, children will be sorted correctly due to above sort
        foreach( $blocks as $block )
        {
            if( isset( $block->parent ) && isset( $block->row ) && isset( $block->column ) )
            {
                // Set container block, there should only be one
                if( $block->type == 'container' ) {
                    $this->container = $block;
                }
                // Add child to parent
                else
                {
                    $this->blocks[$block->parent]->add_child( $block );
                }
            }
        }

    }

    public function sortByOrder( $a, $b )
    {
        return $a->order > $b->order ? 1 : -1;
    }

    public function serialize()
    {
        return serialize( $this );
    }

    public function encode()
    {
        return base64_encode( $this->serialize() );
    }

    public function output_string( $post, $classes )
    {
        $args = array(
            'classes' => $classes,
        );

        return $this->container->output( $post, $args );
    }

    public function output( $post, $classes )
    {
        echo $this->output_string( $post, $classes );
    }
}