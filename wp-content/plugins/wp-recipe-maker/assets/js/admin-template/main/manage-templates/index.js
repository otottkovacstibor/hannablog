import React, { Fragment } from 'react';

import '../../../../css/admin/template/manage.scss';

import ManageTemplate from './ManageTemplate';

const ManageTemplates = (props) => {
    let templatesGrouped = {
        'Our Recipe Templates': [],
        'Our Recipe Snippet Templates': [],
        'Theme Recipe Templates': [],
        'Theme Recipe Snippet Templates': [],
        'Your Recipe Templates': [],
        'Your Recipe Snippet Templates': [],
    }

    // Put templates in correct categories.
    Object.entries(props.templates).forEach(([slug, template]) => {    
        if ( 'file' === template.location ) {
            if ( template.custom ) {
                if ( 'snippet' === template.type ) {
                    templatesGrouped['Theme Recipe Snippet Templates'].push(template);
                } else {
                    templatesGrouped['Theme Recipe Templates'].push(template);
                }
            } else {
                if ( 'snippet' === template.type ) {
                    templatesGrouped['Our Recipe Snippet Templates'].push(template);
                } else {
                    templatesGrouped['Our Recipe Templates'].push(template);
                }
            }
        } else {
            if ( 'snippet' === template.type ) {
                templatesGrouped['Your Recipe Snippet Templates'].push(template);
            } else {
                templatesGrouped['Your Recipe Templates'].push(template);
            }
        }
    });

    return (
        <Fragment>
            <div className="wprm-main-container">
                <h2 className="wprm-main-container-name">Need help?</h2>
                <p style={{ textAlign: 'center'}}>Have a look at the <a href="https://help.bootstrapped.ventures/article/53-template-editor" target="_blank">documentation for the Template Editor</a>!</p>
            </div>
            <div className="wprm-main-container">
                <h2 className="wprm-main-container-name">Templates</h2>
                {
                    Object.keys(templatesGrouped).map((header, i) => {
                        let templates = templatesGrouped[header];
                        if ( templates.length > 0 ) {
                            return (
                                <Fragment key={i}>
                                    <h3>{ header }</h3>
                                    {
                                        templates.map((template, j) => {
                                            let classes = 'wprm-manage-templates-template';
                                            classes += props.template.slug === template.slug ? ' wprm-manage-templates-template-selected' : '';
                                            classes += template.premium && ! wprm_admin.addons.premium ? ' wprm-manage-templates-template-premium' : '';

                                            return (
                                                <div
                                                    key={j}
                                                    className={ classes }
                                                    onClick={ () => {
                                                        const newTemplate = props.template.slug === template.slug ? false : template.slug;
                                                        return props.onChangeTemplate(newTemplate);
                                                    }}
                                                >{ template.name }</div>
                                            )
                                        })
                                    }
                                </Fragment>
                            )
                        }
                    })
                }
            </div>
            {
                props.template
                &&
                <ManageTemplate
                    onChangeEditing={ props.onChangeEditing }
                    template={ props.template }
                    onDeleteTemplate={ props.onDeleteTemplate }
                    onChangeTemplate={ props.onChangeTemplate }
                    savingTemplate={ props.savingTemplate }
                    onSaveTemplate={ props.onSaveTemplate }
                />
            }
        </Fragment>
    );
}

export default ManageTemplates;