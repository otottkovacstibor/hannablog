import React, { Component } from 'react';
import AsyncSelect from 'react-select/lib/Async';

import Api from '../../general/Api';

export default class PreviewRecipe extends Component {
    getOptions(input) {
        if (!input) {
			return Promise.resolve({ options: [] });
        }

		return Api.searchRecipes(input)
            .then((json) => {
                return json.data.recipes_with_id;
            });
    }

    render() {
        return (
            <AsyncSelect
                className="wprm-main-container-preview-recipe"
                placeholder="Select or search a recipe to preview"
                value={this.props.recipe}
                onChange={this.props.onRecipeChange}
                getOptionValue={({id}) => id}
                getOptionLabel={({text}) => text}
                defaultOptions={wprm_admin.latest_recipes}
                loadOptions={this.getOptions.bind(this)}
                noOptionsMessage={() => "Create a recipe on the Manage page"}
                clearable={false}
            />
        );
    }
}