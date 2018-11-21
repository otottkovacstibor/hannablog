export default {
    parseCSS(template) {
        let css = template.style.css;

        // Reconstruct CSS with new value and comments.
        for ( let property of Object.values(template.style.properties) ) {
            let fields = '';
            Object.entries(property).forEach(([field, value]) => {    
                if ( ! ['id', 'name', 'default', 'value', 'options'].includes( field ) ) {
                    fields = ` ${field}=${value}`;
                }
            });

            const replacement = `${property.value}; /*wprm_${property.id}${fields}*/`;
            css = css.replace( new RegExp( `%wprm_${property.id}%\s*;`, 'g' ), replacement );
        }

        return css;
    },
    getShortcodeName(id) {
        let name = id.replace('wprm-', '');
        name = name.replace(/-/g, ' ');
        name = name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
        });

        return name;
    },
    getFullShortcode(shortcode, previewId = false) {
        let fullShortcode = '[' + shortcode.id;

        // Add preview recipe ID.
        if (previewId) {
            fullShortcode += ` id="${previewId}"`;
        }

        // Add shortcode attributes.
        for (let attribute in shortcode.attributes) {
            if ( shortcode.attributes.hasOwnProperty(attribute) ) {
                let value = shortcode.attributes[attribute];
                
                // Replace " with HTML entity to prevent breaking shortcode.
                value = value.replace(/"/gm, '&quot;');
                fullShortcode += ' ' + attribute + '="' + value + '"';
            }
        }

        // Close shortcode.
        fullShortcode += ']';

        return fullShortcode;
    },
    dependencyMet(object, properties) {
        if (properties && object.hasOwnProperty('dependency')) {
            let dependencies = object.dependency;
            
            // Make sure dependencies is an array.
            if ( ! Array.isArray( dependencies ) ) {
                dependencies = [dependencies];
            }

            // Check all dependencies.
            for ( let dependency of dependencies ) {
                if ( properties.hasOwnProperty(dependency.id) ) {
                    const dependency_value = properties[dependency.id].value;

                    if ( dependency.hasOwnProperty('type') && 'inverse' == dependency.type ) {
                        if (dependency_value == dependency.value) {
                            return false;
                        }
                    } else {
                        if (dependency_value != dependency.value) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    },
};
