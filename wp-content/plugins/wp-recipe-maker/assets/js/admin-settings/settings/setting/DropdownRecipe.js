import React, { Component } from 'react';
import AsyncSelect from 'react-select/lib/Async';


export default class SettingDropdownRecipe extends Component {
    getOptions(input) {
        if (!input) {
			return Promise.resolve({ options: [] });
        }

		return fetch(wprm_admin.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: 'action=wprm_search_recipes&security=' + wprm_admin.nonce + '&search=' + encodeURIComponent( input ),
                headers: {
                    'Accept': 'application/json, text/plain, */*',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
                },
            })
            .then((response) => response.json())
            .then((json) => {
                return json.data.recipes_with_id;
            });
    }

    render() {
        return (
            <div className="wprm-main-container-preview-recipe">
                <AsyncSelect
                    placeholder="Select or search a recipe"
                    value={this.props.value}
                    onChange={this.props.onValueChange}
                    getOptionValue={({id}) => id}
                    getOptionLabel={({text}) => text}
                    defaultOptions={wprm_admin.latest_recipes}
                    loadOptions={this.getOptions.bind(this)}
                    noOptionsMessage={() => "Create a recipe on the Manage page"}
                    clearable={false}
                />
            </div>
        );
    }
}
