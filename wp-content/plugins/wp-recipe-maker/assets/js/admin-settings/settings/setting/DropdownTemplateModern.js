import React from 'react';
import PropTypes from 'prop-types';
import Select from 'react-select';


const SettingDropdownTemplateModern = (props) => {
    let selectOptions = [];
    const templates = wprm_admin.recipe_templates.modern;

    for (let template in templates) {
        // Don't show Premium templates in list if we're not Premium.
        if ( ! templates[template].premium || wprm_admin.addons.premium ) {
            selectOptions.push({
                value: template,
                label: templates[template].name,
            });
        }
    }

    return (
        <Select
            className="wprm-setting-input"
            value={selectOptions.filter(({value}) => value === props.value)}
            onChange={(option) => props.onValueChange(option.value)}
            options={selectOptions}
            clearable={false}
        />
    );
}

SettingDropdownTemplateModern.propTypes = {
    setting: PropTypes.object.isRequired,
    value: PropTypes.any.isRequired,
    onValueChange: PropTypes.func.isRequired,
}

export default SettingDropdownTemplateModern;