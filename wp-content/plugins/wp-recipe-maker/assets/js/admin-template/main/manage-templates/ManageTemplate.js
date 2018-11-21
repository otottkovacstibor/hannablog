import React, { Fragment } from 'react';

import Loader from '../../general/Loader';

const ManageTemplate = (props) => {
    const editable = 'database' === props.template.location;

    return (
        <div className="wprm-main-container">
            <h2 className="wprm-main-container-name">Selected Template</h2>
            <div className="wprm-manage-templates-template-fields">
                <span>Slug: { props.template.slug }</span> | <span>Name: { props.template.name }</span>
            </div>
            <div className="wprm-manage-templates-template-actions">
                {
                    props.template.premium && ! wprm_admin.addons.premium
                    ?
                    <p style={{ color: 'darkred', fontWeight: 'bold' }}>This template is only available in <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/" target="_blank">WP Recipe Maker Premium</a>.</p>
                    :
                    <Fragment>
                    {
                        props.savingTemplate
                        ?
                        <Loader></Loader>
                        :
                        <Fragment>
                            <button
                                className="button button-primary"
                                onClick={ () => props.onChangeEditing(true) }
                                disabled={ ! editable }
                            >Edit Template</button>
                            <button
                                className="button button-primary"
                                onClick={() => {
                                    const name = prompt( 'Choose a name for the cloned template' );
                                    
                                    if (name) {
                                        props.onSaveTemplate({
                                            ...props.template,
                                            oldSlug: props.template.slug,
                                            slug: false, // Cloning, so generate new slug.
                                            name,
                                        });
                                    }
                                } }
                            >Clone Template</button>
                            <button
                                className="button"
                                onClick={() => {
                                    const name = prompt( 'Choose a new name for this template', props.template.name );
                                    
                                    if ( name && name !== props.template.name ) {
                                        props.onSaveTemplate({
                                            ...props.template,
                                            name,
                                        });
                                    }
                                } }
                                disabled={ ! editable }
                            >Rename</button>
                            <button
                                className="button"
                                onClick={() => {
                                    if (confirm( 'Are you sure you want to delete the "' + props.template.name + '" template?' )) {
                                        props.onDeleteTemplate(props.template.slug);
                                    }
                                } }
                                disabled={ ! editable }
                            >Delete</button>
                        </Fragment>        
                    }
                    </Fragment>
                }
            </div>
        </div>
    );
}

export default ManageTemplate;