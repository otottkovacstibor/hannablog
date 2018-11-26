const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const {
    Button,
    Disabled,
    ServerSideRender,
    PanelBody,
    Toolbar,
    TextControl,
    SelectControl,
} = wp.components;
const { Fragment } = wp.element;
const {
    InspectorControls,
    BlockControls,
} = wp.editor;

import '../../../css/blocks/modal.scss';

registerBlockType( 'wp-recipe-maker/recipe', {
    title: __( 'WPRM Recipe' ),
    description: __( 'Display a recipe box with recipe metadata.' ),
    icon: 'media-document',
    keywords: [ 'wprm', 'wp recipe maker' ],
    category: 'wp-recipe-maker',
    supports: {
		html: false,
    },
    transforms: {
        from: [
            {
                type: 'shortcode',
                tag: 'wprm-recipe',
                attributes: {
                    id: {
                        type: 'number',
                        shortcode: ( { named: { id = '' } } ) => {
                            return parseInt( id.replace( 'id', '' ) );
                        },
                    },
                    template: {
                        type: 'string',
                        shortcode: ( { named: { template = '' } } ) => {
                            return template.replace( 'template', '' );
                        },
                    },
                },
            },
        ]
    },
    edit: (props) => {
        const { attributes, setAttributes, isSelected, className } = props;

        const modalCallback = ( id ) => {
            setAttributes({
                id,
                updated: Date.now(),
            });
        };

        let templateOptions = [
            { label: 'Use default from settings', value: '' },
        ];
        const templates = wprm_admin.recipe_templates.modern;

        for (let template in templates) {
            // Don't show Premium templates in list if we're not Premium.
            if ( ! templates[template].premium || wprm_admin.addons.premium ) {
                templateOptions.push({
                    value: template,
                    label: templates[template].name,
                });
            }
        }

        return (
            <div className={ className }>{
                attributes.id
                ?
                <Fragment>
                    <BlockControls>
                        <Toolbar
                            controls={[
                                {
                                    icon: 'edit',
                                    title: __( 'Edit Recipe' ),
                                    onClick: () => {
                                        WPRecipeMaker.admin.Modal.open(false, {
                                            recipe_id: attributes.id,
                                            gutenberg: true,
                                            gutenbergCallback: modalCallback,
                                        });
                                        WPRecipeMaker.admin.Modal.disable_menu();
                                    }
                                }
                            ]}
                        />
                    </BlockControls>
                    <InspectorControls>
                        <PanelBody title={ __( 'Recipe Details' ) }>
                            <TextControl
                                label={ __( 'Recipe ID' ) }
                                value={ attributes.id }
                                disabled
                            />
                            <SelectControl
                                label={ __( 'Recipe Template' ) }
                                value={ attributes.template }
                                options={ templateOptions }
                                onChange={ (template) => setAttributes({
                                    template,
                                    updated: Date.now(),
                                }) }
                            />
                        </PanelBody>
                    </InspectorControls>
                    <Disabled>    
                        <ServerSideRender
                            block="wp-recipe-maker/recipe"
                            attributes={ attributes }
                        />
                    </Disabled>
                </Fragment>
                :
                <Fragment>
                    <h2>WPRM { __( 'Recipe' ) }</h2>
                    <Button
                        isPrimary
                        isLarge
                        onClick={ () => {
                            WPRecipeMaker.admin.Modal.open(false, {
                                recipe_id: 0,
                                gutenberg: true,
                                gutenbergCallback: modalCallback,
                            });
                            WPRecipeMaker.admin.Modal.disable_menu();
                        }}>
                        { __( 'Create new Recipe' ) }
                    </Button> <Button
                        isLarge
                        onClick={ () => {
                            WPRecipeMaker.admin.Modal.open(false, {
                                recipe_id: 0,
                                menu: 'insert-recipe',
                                gutenberg: true,
                                gutenbergCallback: modalCallback,
                            });
                            WPRecipeMaker.admin.Modal.disable_menu();
                        }}>
                        { __( 'Insert existing Recipe' ) }
                    </Button>
                </Fragment>
            }</div>
        )
    },
    save: (props) => {
        return null;
    },
} );