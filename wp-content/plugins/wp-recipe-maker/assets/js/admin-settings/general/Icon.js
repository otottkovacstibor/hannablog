import React, { Component } from 'react';
import SVG from 'react-inlinesvg';

import IconLicense from '../../../icons/settings/license-key.svg';
import IconRecipeTemplate from '../../../icons/settings/recipe-template.svg';
import IconLabels from '../../../icons/settings/labels.svg';
import IconRecipePrint from '../../../icons/settings/recipe-print.svg';
import IconRecipeTimes from '../../../icons/settings/recipe-times.svg';
import IconNutritionLabel from '../../../icons/settings/nutrition-label.svg';
import IconCustomStyle from '../../../icons/settings/custom-style.svg';
import IconRecipeSnippets from '../../../icons/settings/recipe-snippets.svg';
import IconRecipeRatings from '../../../icons/settings/recipe-ratings.svg';
import IconAdjustableServings from '../../../icons/settings/adjustable-servings.svg';
import IconSocialSharing from '../../../icons/settings/social-sharing.svg';
import IconIngredientLinks from '../../../icons/settings/ingredient-links.svg';
import IconUnitConversion from '../../../icons/settings/unit-conversion.svg';
import IconRecipeSubmission from '../../../icons/settings/recipe-submission.svg';
import IconRecipeDefaults from '../../../icons/settings/recipe-defaults.svg';
import IconImport from '../../../icons/settings/import.svg';
import IconMetadata from '../../../icons/settings/metadata.svg';
import IconPermissions from '../../../icons/settings/permissions.svg';
import IconSettingsTools from '../../../icons/settings/settings-tools.svg';

import IconScrollToTop from '../../../icons/settings/scroll-to-top.svg';

const icons = {
    licenseKey: IconLicense,
    recipeTemplate: IconRecipeTemplate,
    labels: IconLabels,
    recipePrint: IconRecipePrint,
    recipeTimes: IconRecipeTimes,
    nutritionLabel: IconNutritionLabel,
    customStyle: IconCustomStyle,
    recipeSnippets: IconRecipeSnippets,
    recipeRatings: IconRecipeRatings,
    adjustableServings: IconAdjustableServings,
    socialSharing: IconSocialSharing,
    ingredientLinks: IconIngredientLinks,
    unitConversion: IconUnitConversion,
    recipeSubmission: IconRecipeSubmission,
    recipeDefaults: IconRecipeDefaults,
    import: IconImport,
    metadata: IconMetadata,
    permissions: IconPermissions,
    scrollToTop: IconScrollToTop,
    settingsTools: IconSettingsTools,
};

const Icon = (props) => {
    let icon = icons.hasOwnProperty(props.type) ? icons[props.type] : false;

    if ( !icon ) {
        return <span className="wprm-settings-noicon">&nbsp;</span>;
    }

    return (
        <SVG
            src={icon}
            className='wprm-settings-icon'
        />
    );
}
export default Icon;