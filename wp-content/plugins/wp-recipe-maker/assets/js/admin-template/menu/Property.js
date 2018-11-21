import React from 'react';

import '../../../css/admin/template/property.scss';

import Helpers from '../general/Helpers';

import PropertyColor from './properties/Color';
import PropertyDropdown from './properties/Dropdown';
import PropertyFont from './properties/Font';
import PropertyIcon from './properties/Icon';
import PropertyImageSize from './properties/ImageSize';
import PropertyNumber from './properties/Number';
import PropertySize from './properties/Size';
import PropertyText from './properties/Text';
import PropertyToggle from './properties/Toggle';

const propertyTypes = {
    color: PropertyColor,
    align: PropertyDropdown,
    border: PropertyDropdown,
    dropdown: PropertyDropdown,
    float: PropertyDropdown,
    font: PropertyFont,
    font_size: PropertySize,
    icon: PropertyIcon,
    image_size: PropertyImageSize,
    percentage: PropertyNumber,
    number: PropertyNumber,
    size: PropertySize,
    text: PropertyText,
    toggle: PropertyToggle,
}

const Property = (props) => {
    const PropertyComponent = propertyTypes.hasOwnProperty(props.property.type) ? propertyTypes[props.property.type] : false;

    if ( ! PropertyComponent ) {
        return null;
    }

    if ( ! Helpers.dependencyMet(props.property, props.properties) ) {
        return null;
    }

    return (
        <div className="wprm-template-property">
            <div className="wprm-template-property-label">
                { props.property.name }
            </div>
            <div className={ `wprm-template-property-value wprm-template-property-value-${props.property.type}` }>
                <PropertyComponent
                    property={props.property}
                    value={props.property.value}
                    onValueChange={(value) => { props.onPropertyChange(props.property.id, value); } }
                />
            </div>
        </div>
    );
}

export default Property;