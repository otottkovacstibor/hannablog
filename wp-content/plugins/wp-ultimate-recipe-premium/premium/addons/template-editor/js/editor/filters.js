'use strict';

angular.module('templateEditor.filters', [])
    .filter('camelCaseToHuman', function() {
        return function(input) {
            if(input === undefined) return '';
            return input.charAt(0).toUpperCase() + input.substr(1).replace(/[A-Z]/g, ' $&');
        }
    })
;