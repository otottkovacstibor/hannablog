<?php
$data = file_get_contents( 'php://input' );
$objData = json_decode( $data );

if( stripos( __FILE__, 'vendor/wp-ultimate-post-grid-premium' ) ) {
    require_once( '../../../../../../../../../../wp-load.php' );
} else {
    require_once( '../../../../../../../wp-load.php' );
}

if( !current_user_can( 'manage_options' ) ) die( "You shouldn't be here" );

if( isset( $objData->template_id ) ) {
    $template = WPUltimatePostGrid::get()->template( $objData->template_id );
} else {
    $template = get_option( 'wpupg_custom_template_preview' );
}

class WPUPG_Importer {

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

new WPUPG_Importer( $template );