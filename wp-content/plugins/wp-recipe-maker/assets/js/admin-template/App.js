import React, { Component } from 'react';

import API from './general/Api';
import Helpers from './general/Helpers';

import Menu from './menu';
import Main from './main';

import '../../css/admin/template/layout.scss';

export default class App extends Component {

    constructor(props) {
        super(props);

        this.state = {
            mode: 'manage',
            editing: false,
            templates: JSON.parse(JSON.stringify(wprm_admin_template.templates)),
            template: false,
            savingTemplate: false,
        }
    }

    componentDidMount() {
        window.addEventListener( 'beforeunload', this.beforeWindowClose.bind(this) );
    }
    
    componentWillUnmount() {
        window.removeEventListener( 'beforeunload', this.beforeWindowClose.bind(this) );
    }

    beforeWindowClose(event) {
        if ( this.changesMade() ) {
            return false;
        }
    }

    changesMade() {
        return this.state.editing &&
                ( this.state.template.html !== this.state.templates[this.state.template.slug].html
                || Helpers.parseCSS( this.state.template ) !== Helpers.parseCSS( this.state.templates[this.state.template.slug] ) );
    }

    onChangeEditing(editing) {
        if ( editing !== this.state.editing ) {
            // Scroll to top.
            window.scrollTo(0,0);

            if ( editing ) {
                this.setState({
                    editing,
                    mode: 'properties',
                });
            } else {
                this.setState({
                    editing,
                    mode: 'manage',
                }, () => {
                    // Reset template values.
                    if ( this.state.template ) {
                        this.onChangeTemplate(this.state.template.slug);   
                    }
                });
            }
        }
    }

    onChangeMode(mode) {
        if ( mode !== this.state.mode ) {
            // Scroll to top when going to or coming from HTML/CSS mode.
            if ( 'html' === mode || 'html' === this.state.mode || 'css' === mode || 'css' === this.state.mode ) {
                window.scrollTo(0,0);
            }

            this.setState({
                mode
            });
        }
    }

    onChangeTemplate(slug) {
        // Don't do anything if we're in the middle of saving.
        if ( ! this.state.savingTemplate ) {
            if (this.state.templates.hasOwnProperty(slug)) {
                this.setState({
                    template: JSON.parse(JSON.stringify(this.state.templates[slug])), // Important: use deep clone.
                });
            } else {
                this.setState({
                    template: false,
                });
            }
        }
    }

    onChangeTemplateProperty(id, value) {
        if ( value !== this.state.template.style.properties[id].value ) {
            let newState = this.state;
            newState.template.style.properties[id].value = value;

            this.setState(newState);
        }
    }

    onChangeHTML(html) {
        if ( html !== this.state.template.html ) {
            let newState = this.state;
            newState.template.html = html;

            this.setState(newState);
        }
    }

    onChangeCSS(css) {
        if ( css !== this.state.template.style.css ) {
            let newState = this.state;
            newState.template.style.css = css;

            this.setState(newState);
        }
    }

    onDeleteTemplate(slug) {
        if ( ! this.state.savingTemplate ) {
            this.setState({
                savingTemplate: true,
            });
    
            API.deleteTemplate(slug).then(deletedSlug => {
                let newState = this.state;

                newState.savingTemplate = false;
                newState.template = false;
                delete newState.templates[deletedSlug];

                this.setState(newState);
            }).catch(err => {
                this.setState({
                    savingTemplate: false,
                });
                alert('The template could not be deleted. Try again later or contact support@bootstrapped.ventures');
            });
        }
    }

    onSaveTemplate(template) {
        if ( ! this.state.savingTemplate ) {
            this.setState({
                savingTemplate: true,
            });

            const parsedTemplate = {
                ...template,
                css: Helpers.parseCSS(template),
            }
    
            API.saveTemplate(parsedTemplate).then(savedTemplate => {
                const slug = savedTemplate.slug;
                let newState = this.state;

                newState.savingTemplate = false;
                if ( slug ) {
                    newState.templates[slug] = savedTemplate;
                }

                this.setState(newState, () => {
                    // Force refresh of active template to make sure things are synced.
                    this.onChangeTemplate(slug);
                });
            }).catch(err => {
                this.setState({
                    savingTemplate: false,
                });
                alert('The template could not be saved. Try again later or contact support@bootstrapped.ventures');
            });
        }
    }

    render() {
        return (
            <div>
                <Menu
                    mode={ this.state.mode }
                    editing={ this.state.editing }
                    changesMade={ this.changesMade() }
                    onChangeEditing={ this.onChangeEditing.bind(this) }
                    savingTemplate={ this.state.savingTemplate }
                    onSaveTemplate={ this.onSaveTemplate.bind(this) }
                    onChangeMode={ this.onChangeMode.bind(this) }
                    templates={ this.state.templates }
                    template={ this.state.template }
                    onChangeTemplate={ this.onChangeTemplate.bind(this) }
                    onChangeTemplateProperty={ this.onChangeTemplateProperty.bind(this) }
                />
                <Main
                    mode={ this.state.mode }
                    onChangeMode={ this.onChangeMode.bind(this) }
                    editing={ this.state.editing }
                    onChangeEditing={ this.onChangeEditing.bind(this) }
                    savingTemplate={ this.state.savingTemplate }
                    onDeleteTemplate={ this.onDeleteTemplate.bind(this) }
                    onSaveTemplate={ this.onSaveTemplate.bind(this) }
                    templates={ this.state.templates }
                    template={ this.state.template }
                    onChangeTemplate={ this.onChangeTemplate.bind(this) }
                    onChangeHTML={ this.onChangeHTML.bind(this) }
                    onChangeCSS={ this.onChangeCSS.bind(this) }
                />
            </div>
        );
    }
}
