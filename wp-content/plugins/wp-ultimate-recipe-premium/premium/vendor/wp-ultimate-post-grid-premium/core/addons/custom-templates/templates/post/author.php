<?php

class WPUPG_Template_Post_Author extends WPUPG_Template_Block {

    public $editorField = 'postAuthor';

    public function __construct( $type = 'post-author' )
    {
        parent::__construct( $type );
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $author = get_userdata( $post->post_author );

        $output = $this->before_output();
        $output .= '<span' . $this->style() . '>' . $author->data->display_name . '</span>';

        return $this->after_output( $output, $post );
    }
}