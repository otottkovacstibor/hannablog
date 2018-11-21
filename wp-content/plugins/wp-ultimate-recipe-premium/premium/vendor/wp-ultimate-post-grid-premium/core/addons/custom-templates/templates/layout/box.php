<?php

class WPUPG_Template_Box extends WPUPG_Template_Block {

    public $editorField = 'box';

    public function __construct( $type = 'box' )
    {
        parent::__construct( $type );
    }

    public function output( $post, $args = array() )
    {
        if( !$this->output_block( $post, $args ) ) return '';

        $args['max_width'] = $this->max_width && $args['max_width'] > $this->max_width ? $this->max_width : $args['max_width'];
        $args['max_height'] = $this->max_height && $args['max_height'] > $this->max_height ? $this->max_height : $args['max_height'];
        $args['desktop'] = $args['desktop'] && $this->show_on_desktop;
        $output = $this->before_output();

        ob_start();
?>
<span<?php echo $this->style(); ?>>
    <?php $this->output_children( $post, 0, 0, $args ) ?>
</span>
<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $post );
    }
}