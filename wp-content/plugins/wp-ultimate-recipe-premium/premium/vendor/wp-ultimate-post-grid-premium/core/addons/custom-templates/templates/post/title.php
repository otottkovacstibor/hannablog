<?php

class WPUPG_Template_Post_Title extends WPUPG_Template_Block {

    public $tag;

    public $editorField = 'postTitle';

    public function __construct( $type = 'post-title' )
    {
        parent::__construct( $type );
    }

    public function tag( $tag )
    {
        $this->tag = $tag;
        return $this;
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $args['desktop'] = $args['desktop'] && $this->show_on_desktop;

        $output = $this->before_output();

        $tag = isset( $this->tag ) ? $this->tag : 'span';
        $output .= '<' . $tag . $this->style() . '>' . $this->cut_off( $post->post_title ) . '</' . $tag . '>';

        return $this->after_output( $output, $post );
    }
}