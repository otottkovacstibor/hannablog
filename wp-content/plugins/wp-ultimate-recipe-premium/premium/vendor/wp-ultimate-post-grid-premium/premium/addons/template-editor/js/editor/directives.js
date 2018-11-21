'use strict';

angular.module('templateEditor.directives', [])
    .directive('block', ['$filter', function($filter) {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                element.bind('click', function(event) {
                    scope.editingBlock = attrs.id
                    scope.$apply()

                    event.stopPropagation()
                })

                scope.$watch('template.blocks['+attrs.id+']', function() {
                    var type = scope.getPropVal(attrs.id, 'type')

                    // Positioning
                    var float           = scope.getPropVal(attrs.id, 'float')
                    var center          = scope.getPropVal(attrs.id, 'center')
                    var marginTop       = scope.getPropVal(attrs.id, 'marginTop')
                    var marginBottom    = scope.getPropVal(attrs.id, 'marginBottom')
                    var marginLeft      = scope.getPropVal(attrs.id, 'marginLeft')
                    var marginRight     = scope.getPropVal(attrs.id, 'marginRight')
                    var paddingTop      = scope.getPropVal(attrs.id, 'paddingTop')
                    var paddingBottom   = scope.getPropVal(attrs.id, 'paddingBottom')
                    var paddingLeft     = scope.getPropVal(attrs.id, 'paddingLeft')
                    var paddingRight    = scope.getPropVal(attrs.id, 'paddingRight')
                    var width           = scope.getPropVal(attrs.id, 'width')
                    var height          = scope.getPropVal(attrs.id, 'height')
                    var maxWidth        = scope.getPropVal(attrs.id, 'maxWidth')
                    var maxHeight       = scope.getPropVal(attrs.id, 'maxHeight')
                    var minWidth        = scope.getPropVal(attrs.id, 'minWidth')
                    var minHeight       = scope.getPropVal(attrs.id, 'minHeight')
                    var widthType           = scope.getPropVal(attrs.id, 'widthType')
                    var heightType          = scope.getPropVal(attrs.id, 'heightType')
                    var maxWidthType        = scope.getPropVal(attrs.id, 'maxWidthType')
                    var maxHeightType       = scope.getPropVal(attrs.id, 'maxHeightType')
                    var minWidthType        = scope.getPropVal(attrs.id, 'minWidthType')
                    var minHeightType       = scope.getPropVal(attrs.id, 'minHeightType')

                    // Date
                    var dateFormat      = scope.getPropVal(attrs.id, 'dateFormat')

                    // Image
                    var imagePreset     = scope.getPropVal(attrs.id, 'imagePreset')
                    var imageUrl        = scope.getPropVal(attrs.id, 'imageUrl')

                    // Content
                    var placeholder     = scope.getPropVal(attrs.id, 'placeholder')
                    var tag             = scope.getPropVal(attrs.id, 'tag')
                    var text            = scope.getPropVal(attrs.id, 'text')
                    var paragraph       = scope.getPropVal(attrs.id, 'paragraph')

                    // Taxonomy Terms
                    var termsTaxonomy   = scope.getPropVal(attrs.id, 'termsTaxonomy')

                    // Block Style
                    var backgroundPreset = scope.getPropVal(attrs.id, 'backgroundPreset')
                    var backgroundImage  = scope.getPropVal(attrs.id, 'backgroundImage')
                    var backgroundColor  = scope.getPropVal(attrs.id, 'backgroundColor')

                    // Text Style
                    var textAlign       = scope.getPropVal(attrs.id, 'textAlign')
                    var fontBold        = scope.getPropVal(attrs.id, 'fontBold')
                    var fontSmallCaps   = scope.getPropVal(attrs.id, 'fontSmallCaps')
                    var fontSize        = scope.getPropVal(attrs.id, 'fontSize')
                    var fontSizeUnit    = scope.getPropVal(attrs.id, 'fontSizeUnit')
                    var lineHeight      = scope.getPropVal(attrs.id, 'lineHeight')
                    var lineHeightUnit  = scope.getPropVal(attrs.id, 'lineHeightUnit')
                    var fontColor       = scope.getPropVal(attrs.id, 'fontColor')

                    /**
                     * Styling
                     */
                    // Clear previous style
                    element.removeAttr( 'style' );

                    // Positioning
                    if(float && float != 'none') element.css('float', float)

                    if(center) {
                        element.css('margin', '0 auto')
                    } else {
                        if(marginLeft)  element.css('margin-left', marginLeft + 'px')
                        if(marginRight) element.css('margin-right', marginRight + 'px')
                    }
                    if(marginTop)       element.css('margin-top', marginTop + 'px')
                    if(marginBottom)    element.css('margin-bottom', marginBottom + 'px')

                    if(paddingLeft)   element.css('padding-left', paddingLeft + 'px')
                    if(paddingRight)  element.css('padding-right', paddingRight + 'px')
                    if(paddingTop)    element.css('padding-top', paddingTop + 'px')
                    if(paddingBottom) element.css('padding-bottom', paddingBottom + 'px')

                    if(type != 'container') {
                        if(width > 85 && widthType == '%')      width = 85;
                        if(height > 85 && heightType == '%')    height = 85;

                        if(width)         element.css('width', width + widthType)
                        if(height)        element.css('height', height + heightType)
                        if(maxWidth)      element.css('max-width', maxWidth + maxWidthType)
                        if(maxHeight)     element.css('max-height', maxHeight + maxHeightType)
                        if(minWidth)      element.css('min-width', minWidth + minWidthType)
                        if(minHeight)     element.css('min-height', minHeight + minHeightType)
                    }

                    // DateFormat
                    if(dateFormat)        placeholder = $filter('camelCaseToHuman')(type) + ' (' + dateFormat + ')'

                    // Image
                    if(imagePreset && !imageUrl) {
                        imageUrl = '../img/' + imagePreset + '.png'
                    }
                    if(imageUrl) {
                        element.css('background-image', 'url('+imageUrl+')')
                        element.css('background-size', '100% 100%')
                    }

                    // Taxonomy Terms
                    if(termsTaxonomy)     placeholder = 'Post Taxonomy Terms (' + termsTaxonomy + ')'

                    // Content
                    if(placeholder) {
                        var placeholder_block = document.createElement('span')
                        placeholder_block.textContent = placeholder
                        element.empty()
                        element.append(placeholder_block)
                    }

                    if(type == 'link') tag = 'a'
                    if(text && tag) {
                        var text_block = document.createElement(tag)
                        text_block.textContent = text
                        element.empty()
                        element.append(text_block)

                        if(tag != 'a' && tag != 'span') element.css('display', 'block')
                    }

                    if(paragraph) {
                        var paragraph_block = document.createElement('p')
                        paragraph_block.innerHTML = paragraph.replace(new RegExp('\r?\n','g'), '<br />')
                        element.empty()
                        element.append(paragraph_block)
                    }

                    // Block Style
                    if(backgroundPreset && !backgroundImage) {
                        backgroundImage = '../img/' + backgroundPreset + '.png'
                    }
                    if(backgroundImage) element.css('background', 'url(' + backgroundImage + ')')
                    if(backgroundColor) element.css('background-color', backgroundColor)

                    // Text Style
                    if(textAlign)       element.css('text-align', textAlign)
                    if(fontBold)        element.css('font-weight', 'bold')
                    if(fontSmallCaps)   element.css('font-variant', 'small-caps')

                    if(fontSize && fontSizeUnit)        element.css('font-size', fontSize + fontSizeUnit)
                    if(lineHeight && lineHeightUnit)    element.css('line-height', lineHeight + lineHeightUnit)

                    if(fontColor)       element.css('color', fontColor)

                    // Specific
                    switch (type) {
                        case 'rows':
                        case 'columns':
                        case 'table':
                            scope.adjustTable(attrs.id)
                            break
                    }
                }, true)
            }
        };
    }])
    .directive('properties', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                var inverse = attrs.properties.substr(0,1) == '!';
                var type_string = inverse ? attrs.properties.substr(1) : attrs.properties;
                var types = type_string.split(',');

                var hide = function() {
                    element[0].style.display = 'none';
                }
                var show = function() {
                    element[0].style.display = 'block';
                }

                scope.$watch('editingBlock', function() {
                    var editingType = scope.getPropVal(scope.editingBlock, 'type');

                    if( types.indexOf(editingType) == -1 ) {
                        if(inverse) { show(); } else { hide(); }
                    } else {
                        if(inverse) { hide(); } else { show(); }
                    }
                })
            }
        };
    })
    .directive('insertable', function() {
        return function(scope, element) {
            // Get native JS object
            var el = element[0]

            el.draggable = true

            el.addEventListener(
                'dragstart',
                function(e) {
                    e.dataTransfer.effectAllowed = 'move'
                    e.dataTransfer.setData('type', 'insert')
                    e.dataTransfer.setData('blockType', this.getAttribute('block-type'))
                    this.classList.add('drag')
                    return false
                },
                false
            );

            el.addEventListener(
                'dragend',
                function(e) {
                    this.classList.remove('drag')
                    return false
                },
                false
            );
        }
    })
    .directive('movable', function() {
        return function(scope, element) {
            // Get native JS object
            var el = element[0]

            el.draggable = true

            el.addEventListener(
                'dragstart',
                function(e) {
                    if (e.stopPropagation) e.stopPropagation()
                    scope.editingBlock = this.id
                    scope.$apply()

                    e.dataTransfer.effectAllowed = 'move'
                    e.dataTransfer.setData('type', 'move')
                    e.dataTransfer.setData('blockId', this.id)

                    this.classList.add('drag')

                    return false
                },
                false
            );

            el.addEventListener(
                'dragend',
                function(e) {
                    this.classList.remove('drag')
                    return false
                },
                false
            );
        }
    })
    .directive('droppable', function($compile) {
        return {
            link: function(scope, element, attrs) {
                // Get native JS object
                var el = element[0]

                el.addEventListener(
                    'dragover',
                    function(e) {
                        e.dataTransfer.dropEffect = 'move'

                        if (e.preventDefault) e.preventDefault()
                        if (e.stopPropagation) e.stopPropagation()
                        this.classList.add('over')
                        return false
                    },
                    false
                );

                el.addEventListener(
                    'dragenter',
                    function(e) {
                        if (e.stopPropagation) e.stopPropagation()

                        this.classList.add('over')
                        return false
                    },
                    false
                );

                el.addEventListener(
                    'dragleave',
                    function(e) {
                        if (e.stopPropagation) e.stopPropagation()

                        this.classList.remove('over')
                        return false
                    },
                    false
                );

                el.addEventListener(
                    'drop',
                    function(e) {
                        if (e.stopPropagation) e.stopPropagation()

                        this.classList.remove('over')

                        var type = e.dataTransfer.getData('type')

                        if(type == 'insert')
                        {
                            var blocktype = e.dataTransfer.getData('blockType')

                            var id = scope.addBlockToParent(blocktype, attrs.parent)
                            scope.template.blocks[id]['row'] = attrs.row
                            scope.template.blocks[id]['column'] = attrs.col

                            var droppableBlockTypes = ['rows', 'columns', 'table', 'box']
                            var block = ''

                            if(droppableBlockTypes.indexOf(blocktype) > -1) {
                                block = $compile('<table block id="'+id+'" class="block '+blocktype+'" ng-class="{editing:editingBlock=='+id+'}" ng-hide="template.blocks['+id+'][\'deleted\']" movable><tr><td class="dropzone" data-parent="'+id+'" data-row="0" data-col="0" droppable>')(scope)
                            } else { // Non droppable
                                block = $compile('<div block id="'+id+'" class="block '+blocktype+'" ng-class="{editing:editingBlock=='+id+'}" ng-hide="template.blocks['+id+'][\'deleted\']" movable>')(scope)
                            }

                            element.append(block)
                        }
                        else if (type == 'move')
                        {
                            var id = e.dataTransfer.getData('blockId')

                            if(attrs.parent !== id) {
                                var block = document.getElementById(id)
                                var placeholder = document.getElementById('placeholder')

                                // Check if we're dragging into a child of ourselves
                                if(block.contains(this)) {
                                    scope.errorMessage = 'You cannot move this into its own child block'
                                    scope.$apply()
                                } else {
                                    placeholder.appendChild(block)
                                    this.appendChild(block)

                                    scope.template.blocks[id]['parent'] = attrs.parent
                                    scope.template.blocks[id]['row'] = attrs.row
                                    scope.template.blocks[id]['column'] = attrs.col
                                    scope.template.blocks[id]['order'] = scope.getOrder()
                                    scope.$apply()
                                }
                            }
                        }
                        return false
                    },
                    false
                );
            }
        }
    })
    .directive('imagepicker', function() {
        return {
            restrict: 'E',
            require: 'ngModel',
            scope: {
                imageUrl: '=ngModel',
                imageWidth: '=?',
                imageHeight: '=?'
            },
            template: '<div class="input-group input-group-sm">'
                        + '<span class="input-group-addon">Url</span>'
                        + '<input type="text" class="form-control" ng-model="imageUrl" />'
                        + '<span class="input-group-btn">'
                            + '<button class="btn btn-default" type="button" ng-click="openImageManager()"><span class="caret"></span></button>'
                        + '</span>'
                    + '</div>',
            link: function(scope, element, attrs, ngModel) {
                scope.openImageManager = function() {
                    window.wpupg_image_manager = function(url, width, height) {
                        scope.$apply(function() {
                            ngModel.$setViewValue(url)
                            scope.imageWidth = width
                            scope.imageHeight = height
                        })
                    }
                    window.open('../../../../../../../wp-admin/edit.php?post_type=wpupg_grid&page=wpupg_image_manager');
                }
            },
            replace: true
        };
    })
;