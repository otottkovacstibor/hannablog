<?php
$data = file_get_contents( 'php://input' );
$objData = json_decode( $data );

if( stripos( __FILE__, 'vendor/wp-ultimate-post-grid-premium' ) ) {
    require_once( '../../../../../../../../../../wp-load.php' );
} else {
    require_once( '../../../../../../../wp-load.php' );
}

if( !current_user_can( 'manage_options' ) ) die( "You shouldn't be here" );

// Delete template
if( isset( $objData->template ) ) {
    WPUltimatePostGrid::addon( 'custom-templates' )->delete_template( $objData->template );
}

// Load templates
$mapping = WPUltimatePostGrid::addon( 'custom-templates' )->get_mapping();

$template_list = array();
foreach( $mapping as $index => $template )
{
    $active = array();

    $template_list[] = array(
        'id' => $index,
        'name' => $template,
        'active' => implode( ', ', $active ),
    );
}

echo json_encode( $template_list );