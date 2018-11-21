<?php
$data = file_get_contents( 'php://input' );
$objData = json_decode( $data );

if( stripos( __FILE__, 'vendor/wp-ultimate-post-grid-premium' ) ) {
    require_once( '../../../../../../../../../../wp-load.php' );
} else {
    require_once( '../../../../../../../wp-load.php' );
}


if( !current_user_can( 'manage_options' ) ) die( "You shouldn't be here" );

class WPUPG_Parser {

    protected $blocks = array();
    protected $fonts = array();

    protected $template_blocks = array();
    public $template;

    public function __construct( $blocks )
    {
        // Get blocks that are part of the template
        foreach( $blocks as $block ) {
            if( ( !isset( $block->deleted ) || $block->deleted == false ) && isset( $block->index ) )
            {
                $this->blocks[$block->index] = $block;
            }
        }

        // Parse all blocks
        foreach( $this->blocks as $block ) {

            // Only parse if all parents exist (could have been deleted)
            $parent = $block->parent;

            while( $parent != -1 && isset( $this->blocks[$parent] ) ) {
                $parent = $this->blocks[$parent]->parent;
            }

            if( $parent == -1 ) $this->parseBlock($block);
        }

        // Get the generated template
        $this->template = new WPUPG_Template( $this->template_blocks, $this->fonts );
    }

    protected function parseBlock( $block )
    {
        $template_block = call_user_func( array( $this, 'parseBlock' . ucfirst( $block->type ) ), $block );
        $template_block->add_settings( $block );

        // Block positioning
        if( $block->type != 'container' ) {
            $template_block->parent( $block->parent );
            $template_block->row( $block->row );
            $template_block->column( $block->column );
            $template_block->order( $block->order );
        }

        // Google Web Fonts
        if( isset( $block->fontFamilyType ) && $block->fontFamilyType == 'gwf' ) {
            $fonts = $this->fonts;
            $fonts[] = $block->fontFamilyGWF;
            $this->fonts = $fonts;
        }

        $this->template_blocks[$block->index] = $template_block;
    }

    protected function parseBlockContainer( $block )
    {
        return new WPUPG_Template_Container();
    }

    /**
     * Layout blocks
     */

    protected function parseBlockRows( $block )
    {
        $rows = intval( $block->rows );
        $heights = $this->getRowHeights( $block );

        $template_rows = new WPUPG_Template_Rows();
        return $template_rows->rows($rows)->height($heights);
    }

    protected function getRowHeights( $block )
    {
        $heights = array();

        for($i = 0; $i < $block->rows; $i++)
        {
            $rowtype = 'rowtype' . $i;

            if( $block->{$rowtype} == 'px' ) {
                $rowheight = 'row' . $i;
                $heights[$i] = $block->{$rowheight} . 'px';
            } else {
                $heights[$i] = 'auto';
            }
        }

        return $heights;
    }

    protected function parseBlockColumns( $block )
    {
        $cols = intval( $block->columns );
        $widths = $this->getColWidths( $block );

        $template_columns = new WPUPG_Template_Columns();
        $template_block = $template_columns->columns($cols)->width($widths);

        if( isset( $block->columnsResponsive ) && $block->columnsResponsive ) {
            $template_block->responsive( true );

            if( isset( $block->columnsResponsiveReverse ) && $block->columnsResponsiveReverse ) {
                $template_block->mobile_reverse( true );
            }
        }

        return $template_block;
    }

    protected function getColWidths( $block )
    {
        $widths = array();

        for($i = 0; $i < $block->columns; $i++)
        {
            $coltype = $block->{'columntype' . $i};
            $colwidth = $block->{'column' . $i};

            if( $colwidth == '' || $colwidth == 0 ) {
                $widths[$i] = 'auto';
            } else {
                $widths[$i] = $colwidth . $coltype;
            }
        }

        return $widths;
    }

    protected function parseBlockTable( $block )
    {
        $rows = intval( $block->rows );
        $heights = $this->getRowHeights( $block );
        $cols = intval( $block->columns );
        $widths = $this->getColWidths( $block );

        $template_table = new WPUPG_Template_Table();
        return $template_table->rows($rows)->height($heights)->columns($cols)->width($widths);
    }

    protected function parseBlockBox( $block )
    {
        return new WPUPG_Template_Box();
    }

    /**
     * General Blocks
     */
    protected  function parseBlockDate( $block )
    {
        $template_date = new WPUPG_Template_Date();
        $format = isset( $block->dateFormat ) ? $block->dateFormat : '';
        return $template_date->format($format);
    }

    protected  function parseBlockImage( $block )
    {
        $image = new WPUPG_Template_Image();

        $url = '';

        if( isset( $block->imagePreset ) && $block->imagePreset ) {
            return $image->preset( $block->imagePreset );
        }

        if( isset( $block->imageUrl ) && $block->imageUrl ) {
            $url = $block->imageUrl;
        }

        return $image->url($url);
    }

    protected  function parseBlockTitle( $block )
    {
        $template_title = new WPUPG_Template_Title();
        return $template_title->tag($block->tag)->text($block->text);
    }

    protected  function parseBlockSpace( $block )
    {
        $template_space = new WPUPG_Template_Space();
        return $template_space->non_breaking($block->nonBreaking);
    }

    protected  function parseBlockParagraph( $block )
    {
        $template_paragraph = new WPUPG_Template_Paragraph();
        return $template_paragraph->text( $block->paragraph );
    }

    protected  function parseBlockLink( $block )
    {
        $link = new WPUPG_Template_Link();
        $link->text($block->text)->url($block->linkUrl);

        if( $block->linkNewPage ) {
            $link->target('_blank');
        }

        return $link;
    }

    protected  function parseBlockCode( $block )
    {
        $template_code = new WPUPG_Template_Code();
        return $template_code->text($block->text);
    }

    /**
     * Post Field Blocks
     */

    protected function parseBlockPostImage( $block )
    {
        $image = new WPUPG_Template_Post_Image();

        if( !isset( $block->width ) || !isset( $block->widthType ) || !isset( $block->height ) || !isset( $block->heightType )
            || $block->widthType != 'px' || $block->heightType != 'px' ) {
            $thumb = 'full';
        } else {
            $width = intval( $block->width );
            $height = intval( $block->height );

            $thumb = array($width, $height);
        }

        if( isset( $block->imageCrop ) && $block->imageCrop ) {
            $image->crop( true );
        }

        return $image->thumbnail( $thumb );
    }

    protected  function parseBlockPostDate( $block )
    {
        $template_date = new WPUPG_Template_Post_Date();
        $format = isset( $block->dateFormat ) ? $block->dateFormat : '';
        return $template_date->format($format);
    }

    protected function parseBlockPostTitle( $block )    { return new WPUPG_Template_Post_Title(); }
    protected function parseBlockPostContent( $block )  { return new WPUPG_Template_Post_Content(); }
    protected function parseBlockPostExcerpt( $block )  { return new WPUPG_Template_Post_Excerpt(); }
    protected function parseBlockPostAuthor( $block )   { return new WPUPG_Template_Post_Author(); }

    protected function parseBlockPostTaxonomyTerms( $block )   {
        $terms = new WPUPG_Template_Post_Taxonomy_Terms();

        $taxonomy = isset( $block->termsTaxonomy ) && $block->termsTaxonomy ? $block->termsTaxonomy : 'category';
        $terms->taxonomy( $taxonomy );

        if( isset( $block->termsSeparator ) ) $terms->separator( $block->termsSeparator );

        return $terms;
    }

    protected function parseBlockPostCustomField( $block )       {
        $custom_field = new WPUPG_Template_Post_Custom_Field();
        if( isset( $block->postCustomFieldKey ) ) $custom_field->key( $block->postCustomFieldKey );
        return $custom_field;
    }

    /**
     * General
     */

    public function preview()
    {
        $grid_id = WPUltimatePostGrid::option('template_editor_preview_grid');

        if( !$grid_id ) {
            $args = array(
                'post_type' => WPUPG_POST_TYPE,
                'post_status' => array( 'publish', 'private' ),
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'ASC',
                'fields' => 'ids',
            );

            $query = new WP_Query( $args );
            $posts = $query->have_posts() ? $query->posts : array();

            $grid_id = $posts[0];
        }

        if( $grid_id && WPUPG_POST_TYPE == get_post_type( $grid_id ) ) {
            $src = get_permalink( $grid_id );
            if( strpos( $src, '?' ) ) {
                $src = $src . '&wpupg_template_editor_preview=' . $grid_id;
            } else {
                $src = $src . '?wpupg_template_editor_preview=' . $grid_id;
            }

            update_option( 'wpupg_custom_template_preview', $this->template );

            echo '<iframe id="preview-frame" src="'.$src.'" onload="resizeFrame(this)"/>';
        } else {
            echo '<div style="text-align: center;">';
            _e( 'Please set a Preview Grid in the settings first.', 'wp-ultimate-post-grid' );
            echo '</div>';
        }

    }

    public function save( $save_as_new, $id, $new_name )
    {
        if( $save_as_new ) {
            $new_id = WPUltimatePostGrid::addon( 'custom-templates' )->add_template( $new_name, $this->template );
            echo json_encode( array(
                'id' => $new_id,
                'name' => $new_name,
                'active' => '',
            ) );
        } else {
            WPUltimatePostGrid::addon( 'custom-templates' )->update_template( $id, $this->template );
            echo json_encode(false);
        }
    }

    public function export()
    {
        echo $this->template->encode();
    }
}
$parser = new WPUPG_Parser( $objData->template->blocks );

if( isset( $objData->code ) ) {
    $template = unserialize( base64_decode( $objData->code ) );

    if( is_object( $template ) ) {
        $parser->template = $template;
    } else {
        die( json_encode( array(
            'error' => true,
        ) ) );
    }
}

if( isset( $objData->saveAsNew ) ) {
    $parser->save( $objData->saveAsNew, $objData->template->id, $objData->newName );
} else if( isset( $objData->export ) ) {
    $parser->export();
} else {
    $parser->preview();
}