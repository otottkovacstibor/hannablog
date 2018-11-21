<?php
$data = file_get_contents( 'php://input' );
$objData = json_decode( $data );

$wp_load_dir = isset( $objData->wp_load_dir ) ? $objData->wp_load_dir : false;
if( !$wp_load_dir || !file_exists( $wp_load_dir . 'wp-load.php' ) ) {
    $wp_load_dir = '../../../../../../../';
}
require_once( $wp_load_dir . 'wp-load.php' );

if( !current_user_can( 'manage_options' ) ) die( "You shouldn't be here" );

if( isset( $objData->template_id ) ) {
    $template = WPUltimateRecipe::get()->template( 'recipe', $objData->template_id );
} else {
    $template = get_option( 'wpurp_custom_template_preview' );
}

class WPURP_Importer {

    protected $template;
    protected $blocks = array();

    public function __construct( $template )
    {
        // Get the generated template
        $this->template = $template;

        $this->import( $template->blocks );
        echo json_encode( $this->blocks );
    }

    protected function import( $template_blocks )
    {
        foreach( $template_blocks as $index => $template_block)
        {
            $block = array();
            foreach( $template_block->settings as $setting => $value )
            {
                $block[$setting] = $value;
            }

            $this->blocks[$index] = $block;
        }
    }
}

new WPURP_Importer( $template );