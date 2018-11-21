'use strict';

angular.module('templateEditor.controllers', [])
/**
 * Main Controller
 */
    .controller('TemplateEditorCtrl', function($scope, $timeout, $http, $sce, $compile, $modal, $filter) {
        /**
         * Alerts
         */
        $scope.alerts = [
            //{ type: 'info', msg: 'Hi there!' },
        ];

        $scope.closeAlert = function(index) {
            $scope.alerts.splice(index, 1);
        };

        /**
         * Options
         */
        $scope.shortenTextOptions = [{
            id: 'none', name: 'Do not shorten text' },{
            id: 'characters', name: 'Limit to X characters' },{
            id: 'words', name: 'Limit to X words' }
        ];

        $scope.floatOptions = [{
            id: 'none', name: 'None' },{
            id: 'left', name: 'Left' },{
            id: 'right', name: 'Right' }
        ];

        $scope.positionOptions = [{
            id: 'static', name: 'Static' },{
            id: 'absolute', name: 'Absolute' },{
            id: 'relative', name: 'Relative' },{
            id: 'Fixed', name: 'Fixed' }
        ];

        $scope.dateFormatOptions = [{
            id: 'm/d/Y', name: '01/28/2014' },{
            id: 'm/d/y', name: '01/28/14' },{
            id: 'd/m/Y', name: '28/01/2014' },{
            id: 'd/m/y', name: '28/01/14' },{
            id: 'Y-m-d', name: '2014-01-28' }
        ];

        $scope.imagePresetOptions = [{
            id: 'missing-image', name: 'Missing Recipe Image' }
        ];

        $scope.imagePresetDefaults = function(block) {
            switch (block['imagePreset']) {
                case 'missing-image':
                    block['width'] = 150;
                    block['height'] = 150;
                    break;
            }
        }

        $scope.backgroundPresetOptions = [{
            id: 'default', name: 'Default' },{
            id: 'default-blue', name: 'Default (Blue)' },{
            id: 'default-brown', name: 'Default (Brown)' },{
            id: 'default-green', name: 'Default (Green)' }
        ];

        $scope.borderStyleOptions = [{
            id: 'solid', name: 'Solid Line' },{
            id: 'dashed', name: 'Dashed Line' },{
            id: 'dotted', name: 'Dotted Line' },{
            id: 'double', name: 'Double Line' }
        ];

        $scope.shadowTypeOptions = [{
            id: '', name: 'Outer Shadow' },{
            id: 'inset',   name: 'Inner Shadow' }
        ];

        $scope.fontFamilyTypeOptions = [{
            id: '', name: 'Inherit' },{
            id: 'gwf', name: 'Google Web Fonts' },{
            id: 'manual',   name: 'Manual' }
        ];

        // Font options
        $.getJSON('../vendor/google_web_fonts.json', function(json) {
            $scope.fontFamilyGWFOptions = $.map(json.items, function(font, index) {
                var id = font.family.replace(' ','+')
                return [{id: id, name: font.family}]
            })
        });

        $scope.columnsRowsOptions = [{
            id: 1, name: '1' },{
            id: 2, name: '2' },{
            id: 3, name: '3' },{
            id: 4, name: '4' },{
            id: 5, name: '5' },{
            id: 6, name: '6' },{
            id: 7, name: '7' },{
            id: 8, name: '8' },{
            id: 9, name: '9' },{
            id: 10, name: '10' }
        ];

        $scope.titleTagOptions = [{
            id: 'span', name: 'Normal text' },{
            id: 'div', name: 'Block text' },{
            id: 'h1', name: 'Heading 1' },{
            id: 'h2', name: 'Heading 2' },{
            id: 'h3', name: 'Heading 3' },{
            id: 'h4', name: 'Heading 4' },{
            id: 'h5', name: 'Heading 5' },{
            id: 'h6', name: 'Heading 6' }
        ];

        $scope.textAlignOptions = [{
            id: 'inherit', name: 'Inherit' },{
            id: 'left', name: 'Left' },{
            id: 'center', name: 'Center' },{
            id: 'right', name: 'Right' }
        ];

        $scope.verticalAlignOptions = [{
            id: 'inherit', name: 'Inherit' },{
            id: 'baseline', name: 'Baseline' },{
            id: 'sub', name: 'Sub' },{
            id: 'super', name: 'Super' },{
            id: 'text-top', name: 'Text Top' },{
            id: 'text-bottom', name: 'Text Bottom' },{
            id: 'middle', name: 'Middle' },{
            id: 'top', name: 'Top' },{
            id: 'bottom', name: 'Bottom' }
        ];

        $scope.showGroupsOptions = [{
            id: 'all', name: 'Show all groups' },{
            id: 'only', name: 'Show only these groups:' },{
            id: 'except', name: 'Show all except these groups:' }
        ];

        $scope.listStyleOptions = [{
            id: 'none', name: 'None' },{
            id: 'circle', name: 'Circle' },{
            id: 'disc', name: 'Disc' },{
            id: 'square', name: 'Square' },{
            id: 'decimal', name: 'Decimal' },{
            id: 'decimal-leading-zero', name: 'Decimal with leading zero' },{
            id: 'lower-roman', name: 'Lower Roman' },{
            id: 'upper-roman', name: 'Upper Roman' },{
            id: 'lower-latin', name: 'Lower Latin' },{
            id: 'upper-latin', name: 'Upper Latin' },{
            id: 'lower-greek', name: 'Lower Greek' },{
            id: 'armenian', name: 'Armenian' },{
            id: 'georgian', name: 'Georgian' }
        ];

        $scope.twitterLayoutOptions = [{
            id: 'none', name: 'No count' },{
            id: 'horizontal', name: 'Horizontal count' },{
            id: 'vertical', name: 'Vertical count' }
        ];

        $scope.facebookLayoutOptions = [{
            id: 'button', name: 'No count' },{
            id: 'button_count', name: 'Horizontal count' },{
            id: 'box_count', name: 'Vertical count' }
        ];

        $scope.googleLayoutOptions = [{
            id: 'medium', name: 'No count' },{
            id: 'medium_bubble', name: 'Horizontal count' },{
            id: 'tall', name: 'Vertical count' }
        ];

        $scope.pinterestLayoutOptions = [{
            id: 'none', name: 'No count' },{
            id: 'above', name: 'Vertical count' }
        ];

        $scope.stumbleuponLayoutOptions = [{
            id: '4', name: 'No count' },{
            id: '1', name: 'Horizontal count' },{
            id: '5', name: 'Vertical count' }
        ];

        $scope.linkedinLayoutOptions = [{
            id: '', name: 'No count' },{
            id: 'right', name: 'Horizontal count' },{
            id: 'top', name: 'Vertical count' }
        ];

        $scope.hoverOptions = [{
            id: 'disabled', name: 'Disabled' },{
            id: 'show', name: 'Show' },{
            id: 'hide', name: 'Hide' }
        ];

        $scope.hoverTransitionOptions = [{
            id: 'instant', name: 'Instant' },{
            id: 'fade', name: 'Fade' },{
            id: 'slide', name: 'Slide' }
        ];

        $scope.templates = []

         var loadTemplate = function(template) {
            var data = {
                template_id: template.id
            }

            $http.post('importer.php', data)
                .success(function(objData, status) {
                    $timeout(function() { // Fixes $rootScope inprog error
                        // Object to array
                        var data = []
                        for(var i in objData) {
                            data[i] = objData[i]
                        }

                        // Set values
                        $scope.template.blocks = data

                        // Clear container
                        jQuery('#0').empty()
                        $scope.template.blocks[0] = $scope.blockDefaults($scope.template.blocks[0])

                        $scope.currentOrder = data.length

                        // TODO Clean this up
                        var addToCanvas = function(inCanvas, data, id) {
                            if(jQuery.inArray(id, inCanvas) == -1) {
                                var parent_id = parseInt(data[id]['parent'])

                                if(parent_id != -1 && jQuery.inArray(parent_id, inCanvas) == -1) {
                                    inCanvas = addToCanvas(inCanvas, data, parent_id)
                                }

                                var blocktype = data[id]['type']

                                if(blocktype != 'container') {
                                    var droppableBlockTypes = ['rows', 'columns', 'table', 'box']
                                    var block = '';
                                    var parent = '';

                                    if(droppableBlockTypes.indexOf(blocktype) > -1) {
                                        block = $compile('<table block id="'+id+'" class="block '+blocktype+'" ng-class="{editing:editingBlock=='+id+'}" ng-hide="template.blocks['+id+'][\'deleted\']" data-order="'+ data[id]['order'] +'" movable><tr><td id="'+id+'-0-0" class="dropzone" data-parent="'+id+'" data-row="0" data-col="0" droppable>')($scope)
                                    } else { // Non droppable
                                        block = $compile('<div block id="'+id+'" class="block '+blocktype+'" ng-class="{editing:editingBlock=='+id+'}" ng-hide="template.blocks['+id+'][\'deleted\']" data-order="'+ data[id]['order'] +'" movable>')($scope)
                                    }

                                    // Parent is droppable
                                    if(droppableBlockTypes.indexOf(data[data[id]['parent']]['type']) > -1) {
                                        parent = '#' + parent_id + '-' + data[id]['row'] + '-' + data[id]['column'];
                                    } else {
                                        parent = '#' + parent_id;
                                    }

                                    // Add block to parent
                                    jQuery(parent).append(block);

                                    // Sort children by order attribute
                                    jQuery(parent).children('.block').sort(function(a,b) {
                                        return +a.getAttribute('data-order') - +b.getAttribute('data-order')
                                    }).appendTo(parent);

                                    // Block Defaults
                                    $scope.template.blocks[id] = $scope.blockDefaults($scope.template.blocks[id])

                                    // Scope order
                                    if($scope.currentOrder <= data[id]['order']) $scope.currentOrder = data[id]['order']+1

                                    $scope.$apply()
                                    inCanvas.push(id)
                                    return inCanvas
                                }
                            }

                            return inCanvas
                        }

                        var inCanvas = []

                        for(var id in data)
                        {
                            inCanvas = addToCanvas(inCanvas, data, parseInt(id))
                        }

                        $scope.template.id = template.id
                        $scope.template.name = template.name

                        $scope.debug['json-status'] = status;
                    }, 0);
                })
                .error(function(data, status) {
                    $scope.debug['json'] = data || "Request failed";
                    $scope.debug['json-status'] = status;
                })
            ;
        }

        $http.post('manager.php', [])
            .success(function(data, status) {
                $scope.templates = data
            })
            .error(function(data, status) {
                $scope.debug['json'] = data || "Request failed";
                $scope.debug['json-status'] = status;
            })
        ;

        $scope.openLoadTemplateModal = function () {
            var modalInstance = $modal.open({
                templateUrl: 'loadTemplateModal.html',
                controller: 'ModalLoadTemplateCtrl',
                backdrop: 'static',
                resolve: {
                    templates: function () {
                        return $scope.templates;
                    }
                }
            });

            modalInstance.result.then(function (data) {
                if(data !== undefined) {
                    if(data.action == 'load') {
                        loadTemplate(data.template)
                    }
                    if(data.action == 'delete') {
                        var data = {
                            template: data.template.id
                        }

                        $http.post('manager.php', data)
                            .success(function(data, status) {
                                $scope.templates = data
                                $scope.alerts.push({ type: 'success', msg: 'The template has been deleted.' })
                            });
                    }
                }
            }, function() {});
        };

        $scope.openSaveTemplateModal = function () {
            var modalInstance = $modal.open({
                templateUrl: 'saveTemplateModal.html',
                controller: 'ModalSaveTemplateCtrl',
                backdrop: 'static',
                resolve: {
                    template: function () {
                        return $scope.template;
                    }
                }
            });

            modalInstance.result.then(function (data) {
                if(data !== undefined) {
                    data.template = $scope.template

                    $http.post('parser.php', data)
                        .success(function(data, status) {
                            if(data !== null && typeof data === 'object') {
                                $scope.templates.push(data)

                                $scope.template.id = data.id
                                $scope.template.name = data.name
                            }
                        })
                }
            }, function() {});
        };

        $scope.openImportTemplateModal = function () {
            var modalInstance = $modal.open({
                templateUrl: 'importTemplateModal.html',
                controller: 'ModalImportTemplateCtrl',
                backdrop: 'static'
            });

            modalInstance.result.then(function (data) {
                if(data !== undefined) {
                    data.template = {
                        id: null,
                        blocks: []
                    }

                    data.saveAsNew = true

                    $http.post('parser.php', data)
                        .success(function(data, status) {
                            if(data !== null && typeof data === 'object' && !data.error) {
                                $scope.templates.push(data)
                                loadTemplate(data)
                            } else {
                                $scope.alerts.push({ type: 'danger', msg: 'The template was not correctly imported.' })
                            }
                        })
                }
            }, function() {});
        };

        $scope.openExportTemplatePage = function () {
            var data = {
                template: $scope.template,
                export: true
            }

            $http.post('parser.php', data)
                .success(function(data, status) {
                    document.getElementById('exportTemplateValue').value = data;
                    document.getElementById('exportTemplate').submit();
                })
            ;
        };

        $scope.openEditParagraphModal = function () {

            var modalInstance = $modal.open({
                templateUrl: 'editParagraphModal.html',
                controller: 'ModalParagraphCtrl',
                backdrop: 'static',
                resolve: {
                    paragraph: function () {
                        return $scope.template.blocks[$scope.editingBlock]['paragraph'];
                    }
                }
            });

            modalInstance.result.then(function (paragraph) {
                $scope.template.blocks[$scope.editingBlock]['paragraph'] = paragraph
            }, function () {});
        };

        $scope.openEditConditionsModal = function () {

            var modalInstance = $modal.open({
                templateUrl: 'editConditionsModal.html',
                controller: 'ModalConditionsCtrl',
                backdrop: 'static',
                resolve: {
                    block: function () {
                        return $scope.template.blocks[$scope.editingBlock];
                    },
                    conditions: function () {
                        return $scope.template.blocks[$scope.editingBlock]['conditions'];
                    }
                }
            });

            modalInstance.result.then(function (conditions) {
                $scope.template.blocks[$scope.editingBlock]['conditions'] = conditions
            }, function () {});
        };

        $scope.switchRowType = function(field) {
            var curType = $scope.getPropVal($scope.editingBlock, field)
            var newType = curType == 'fluid' ? 'px' : 'fluid'

            $scope.template.blocks[$scope.editingBlock][field] = newType
        }

        $scope.switchColumnType = function(field) {
            var curType = $scope.getPropVal($scope.editingBlock, field)
            var newType = curType == '%' ? 'px' : '%'

            $scope.template.blocks[$scope.editingBlock][field] = newType
        }

        $scope.switchMeasureType = function(field) {
            var curType = $scope.getPropVal($scope.editingBlock, field)
            var newType = curType == 'px' ? 'em' : 'px'

            $scope.template.blocks[$scope.editingBlock][field] = newType
        }

        $scope.switchType = function(field, values) {
            var curType = $scope.getPropVal($scope.editingBlock, field)
            var index = (values.indexOf(curType) + 1) % values.length

            $scope.template.blocks[$scope.editingBlock][field] = values[index]
        }

        // Our template consists of blocks
        $scope.template = {
            id: null,
            name: '',
            blocks: []
        }

        $scope.debug = {}

        // Add a block
        $scope.addBlockToParent = function(type, parent) {
            var block = {
                type: type,
                deleted: false,
                order: $scope.getOrder()
            }

            block = $scope.blockDefaults(block)

            // Get block ID, add to blocks and set parent block ID
            var id = $scope.template.blocks.length
            block['index'] = id
            block['parent'] = parent
            $scope.template.blocks.push(block)

            return id
        }

        $scope.blockDefaults = function(block) {
            block['conditions'] = block['conditions'] || []

            block['shortenText'] = block['shortenText'] || 'none'
            block['shortenTextNumber'] = block['shortenTextNumber'] || ''
            block['shortenTextAfter'] = block['shortenTextAfter'] || '...'


            block['float'] = block['float'] || 'none'
            block['center'] = block['center'] || false
            block['marginTop'] = block['marginTop'] ||0
            block['marginBottom'] = block['marginBottom'] || 0
            block['marginLeft'] = block['marginLeft'] || 0
            block['marginRight'] = block['marginRight'] || 0

            block['widthType'] = block['widthType'] || 'px'
            block['heightType'] = block['heightType'] || 'px'
            block['minWidthType'] = block['minWidthType'] || 'px'
            block['minHeightType'] = block['minHeightType'] || 'px'
            block['maxWidthType'] = block['maxWidthType'] || 'px'
            block['maxHeightType'] = block['maxHeightType'] || 'px'

            block['position'] = block['position'] || 'static'

            block['borderColor'] = block['borderColor'] || ''
            block['borderStyle'] = block['borderStyle'] || 'solid'
            block['borderTop'] = block['borderTop'] !== undefined ? block['borderTop'] : true
            block['borderBottom'] = block['borderBottom'] !== undefined ? block['borderBottom'] : true
            block['borderLeft'] = block['borderLeft'] !== undefined ? block['borderLeft'] : true
            block['borderRight'] = block['borderRight'] !== undefined ? block['borderRight'] : true
            block['shadowType'] = block['shadowType'] || ''

            block['textAlign'] = block['textAlign'] || 'inherit'
            block['verticalAlign'] = block['verticalAlign'] || 'inherit'
            block['fontBold'] = block['fontBold'] || false
            block['fontSmallCaps'] = block['fontSmallCaps'] || false
            block['fontSize'] = block['fontSize'] || ''
            block['fontSizeUnit'] = block['fontSizeUnit'] || 'px'
            block['lineHeight'] = block['lineHeight'] || ''
            block['lineHeightUnit'] = block['lineHeightUnit'] || 'px'
            block['fontColor'] = block['fontColor'] || ''
            block['fontFamilyType'] = block['fontFamilyType'] || ''
            block['fontFamilyGWF'] = block['fontFamilyGWF'] || 'Open+Sans'

            block['customClass'] = block['customClass'] || ''
            block['customStyle'] = block['customStyle'] || ''

            block['hover'] = block['hover'] || 'disabled'
            block['hoverInTransition'] = block['hoverInTransition'] || 'instant'
            block['hoverOutTransition'] = block['hoverOutTransition'] || 'instant'
            block['hoverInTime'] = block['hoverInTime'] || 0
            block['hoverOutTime'] = block['hoverOutTime'] || 0

            switch (block['type']) {
                case 'container':
                    break;
                case 'table':
                case 'rows':
                    block['rows'] = block['rows'] || 1

                    block['row0'] = block['row0'] || 30
                    block['row1'] = block['row1'] || 30
                    block['row2'] = block['row2'] || 30
                    block['row3'] = block['row3'] || 30
                    block['row4'] = block['row4'] || 30
                    block['row5'] = block['row5'] || 30
                    block['row6'] = block['row6'] || 30
                    block['row7'] = block['row7'] || 30
                    block['row8'] = block['row8'] || 30
                    block['row9'] = block['row9'] || 30

                    block['rowtype0'] = block['rowtype0'] || 'fluid'
                    block['rowtype1'] = block['rowtype1'] || 'fluid'
                    block['rowtype2'] = block['rowtype2'] || 'fluid'
                    block['rowtype3'] = block['rowtype3'] || 'fluid'
                    block['rowtype4'] = block['rowtype4'] || 'fluid'
                    block['rowtype5'] = block['rowtype5'] || 'fluid'
                    block['rowtype6'] = block['rowtype6'] || 'fluid'
                    block['rowtype7'] = block['rowtype7'] || 'fluid'
                    block['rowtype8'] = block['rowtype8'] || 'fluid'
                    block['rowtype9'] = block['rowtype9'] || 'fluid'

                    if(block['type'] != 'table') break // Fall through if table
                case 'columns':
                    block['columns'] = block['columns'] || 1

                    block['column0'] = block['column0'] || ''
                    block['column1'] = block['column1'] || ''
                    block['column2'] = block['column2'] || ''
                    block['column3'] = block['column3'] || ''
                    block['column4'] = block['column4'] || ''
                    block['column5'] = block['column5'] || ''
                    block['column6'] = block['column6'] || ''
                    block['column7'] = block['column7'] || ''
                    block['column8'] = block['column8'] || ''
                    block['column9'] = block['column9'] || ''

                    block['columntype0'] = block['columntype0'] || '%'
                    block['columntype1'] = block['columntype1'] || '%'
                    block['columntype2'] = block['columntype2'] || '%'
                    block['columntype3'] = block['columntype3'] || '%'
                    block['columntype4'] = block['columntype4'] || '%'
                    block['columntype5'] = block['columntype5'] || '%'
                    block['columntype6'] = block['columntype6'] || '%'
                    block['columntype7'] = block['columntype7'] || '%'
                    block['columntype8'] = block['columntype8'] || '%'
                    block['columntype9'] = block['columntype9'] || '%'
                    break;
                case 'box':
                    block['text'] = block['text'] || '&nbsp;'
                    break;
                case 'title':
                    block['tag'] = block['tag'] || 'span'
                    block['text'] = block['text'] || 'Title Text'
                    break;
                case 'space':
                    block['nonBreaking'] = block['nonBreaking'] || false
                    block['placeholder'] = block['placeholder'] || $filter('camelCaseToHuman')(block['type'])
                    break;
                case 'paragraph':
                    block['paragraph'] = block['paragraph'] || 'Paragraph Text'
                    break;
                case 'link':
                    block['linkNewPage'] = block['linkNewPage'] !== undefined ? block['linkNewPage'] : true
                    block['linkUrl'] = block['linkUrl'] || '#'
                    block['text'] = block['text'] || 'Link Text'
                    break;
                case 'code':
                    block['placeholder'] = block['placeholder'] || 'Code';
                    block['text'] = block['text'] || '';
                    break;
                case 'postImage':
                    block['width'] = block['width'] || 150;
                    block['height'] = block['height'] || 150;
                    block['placeholder'] = block['placeholder'] || 'Post Image';
                    break;
                case 'postTaxonomyTerms':
                    block['termsTaxonomy'] = block['termsTaxonomy'] || 'category';
                    block['termsSeparator'] = block['termsSeparator'] || ',&nbsp;';
                    block['placeholder'] = 'Post Taxonomy Terms (' + block['termsTaxonomy'] + ')';
                    break;

                default:
                    block['placeholder'] = block['placeholder'] || $filter('camelCaseToHuman')(block['type']);
            }

            return block;
        }

        // Add the container as block 0
        $scope.template.blocks.push({
            type: 'container',
            maxWidth: '200',
            marginTop: 0,
            marginBottom: 0,
            marginLeft: 0,
            marginRight: 0,
            position: 'absolute',
            order: -1,
            index: 0,
            parent: -1
        })

        $scope.blockDefaults($scope.template.blocks[0])

        $scope.getPropVal = function(index, property) {
            if($scope.template.blocks[index] === undefined) {
                return ''
            } else {
                return $scope.template.blocks[index][property]
            }
        }

        // Currently not editing a block
        $scope.editingBlock = null

        //TODO Undo delete button
        $scope.deleteBlock = function(block) {
            // Must not delete container
            if(block == 0) return

            $scope.template.blocks[block]['deleted'] = true
            $scope.editingBlock = null
        }

        $scope.selectParentBlock = function(block) {
            $scope.editingBlock = $scope.template.blocks[block]['parent']
        }

        $scope.cloneBlock = function(block) {
            $scope.editingBlock = $scope.template.blocks[block]['parent']
        }

        //TODO Reset timer
        $scope.$watch('errorMessage', function() {
            if($scope.errorMessage !== null && $scope.errorMessage !== undefined) {
                $timeout(function() {
                    $scope.errorMessage = null
                }, 3000);
            }
        });

        $scope.currentOrder = 0
        $scope.getOrder = function() {
            return $scope.currentOrder++
        }

        $scope.parseTemplate = function() {
            var data = {
                template: $scope.template
            }

            $scope.preview = $sce.trustAsHtml('<div id="floatingCirclesG"><div class="f_circleG" id="frotateG_01"></div><div class="f_circleG" id="frotateG_02"></div><div class="f_circleG" id="frotateG_03"></div><div class="f_circleG" id="frotateG_04"></div><div class="f_circleG" id="frotateG_05"></div><div class="f_circleG" id="frotateG_06"></div><div class="f_circleG" id="frotateG_07"></div><div class="f_circleG" id="frotateG_08"></div></div>');

            $http.post('parser.php', data)
                .success(function(data, status) {
                    $scope.preview = $sce.trustAsHtml(data);
                    $scope.debug['json-status'] = status;
                })
                .error(function(data, status) {
                    $scope.debug['json'] = data || "Request failed";
                    $scope.debug['json-status'] = status;
                })
            ;
        }

        $scope.adjustTable = function(id) {
            var table = document.getElementById(id)

            if( table !== null)
            {
                var type = $scope.getPropVal(id, 'type')
                var rows = $scope.getPropVal(id, 'rows')
                var cols = $scope.getPropVal(id, 'columns')

                rows = rows === undefined ? 1 : parseInt(rows)
                cols = cols === undefined ? 1 : parseInt(cols)

                var cur_rows = table.rows.length
                var cur_cols = table.rows[0].cells.length

                var compile_cell = function compileCell(cell, parent, row, col) {
                    cell.className = 'dropzone'
                    cell.id = parent + '-' + row + '-' + col
                    cell.dataset.parent = parent
                    cell.dataset.row = row
                    cell.dataset.col = col
                    cell.setAttribute('droppable', '')

                    $compile(cell)($scope)
                }

                // Create new rows if needed
                if(cur_rows < rows)
                {
                    for(var i = cur_rows; i < rows; i++)
                    {
                        var new_row = table.insertRow(-1) // Insert row at the end of the table

                        for(var j = 0; j < cur_cols; j++)
                        {
                            var new_cell = new_row.insertCell(j)
                            compile_cell(new_cell, id, i, j)
                        }
                    }

                    cur_rows = rows
                }

                // Create new columns if needed
                if(cur_cols < cols)
                {
                    for(var i = 0; i < cur_rows; i++)
                    {
                        var row = table.rows[i]

                        for(var j = cur_cols; j < cols; j++)
                        {
                            var new_cell = row.insertCell(j)
                            compile_cell(new_cell, id, i, j)
                        }
                    }

                    cur_cols = cols
                }

                // Make sure the right rows and columns are visible
                for(var i = 0; i < cur_rows; i++)
                {
                    var row = table.rows[i]

                    for(var j = 0; j < cur_cols; j++)
                    {
                        var cell = row.cells[j]

                        if(i < rows && j < cols) {
                            cell.classList.remove('deleted')
                        } else {
                            cell.classList.add('deleted')
                        }

                        // Set Row height
                        if($scope.getPropVal(id, 'rowtype' + i) == 'px') {
                            cell.style.height = $scope.getPropVal(id, 'row' + i) + 'px'
                        } else {
                            cell.style.height = '20px'
                        }

                        var colWidth = $scope.getPropVal(id, 'column' + j)

                        // Set column width
                        if(j == cols - 1 || colWidth == "" || colWidth == 0) {
                            cell.style.width = 'auto'
                        } else {
                            cell.style.width = $scope.getPropVal(id, 'column' + j) + $scope.getPropVal(id, 'columntype' + j)
                        }
                    }
                }
            }
        }
    })
    .controller('ModalLoadTemplateCtrl', function($scope, $modalInstance, templates) {
        $scope.templates = templates

        $scope.cancel = function() {
            $modalInstance.close()
        };

        $scope.load = function(template) {
            if(confirm('Are you sure you want to load a new template? Any unsaved changes will be lost.')) {
                $modalInstance.close({
                    action: 'load',
                    template: template
                })
            }
        };

        $scope.delete = function(template) {
            if(confirm('Are you sure you want to delete this template?')) {
                $modalInstance.close({
                    action: 'delete',
                    template: template
                })
            }
        };
    })
    .controller('ModalSaveTemplateCtrl', function($scope, $modalInstance, template) {
        $scope.template = template
        $scope.template.newName = ''

        $scope.template.saveAsNew = $scope.template.id == null || $scope.template.id <= 2 || $scope.template.id == 99 ? true : false

        $scope.ok = function() {
            if($scope.template.saveAsNew && $scope.template.newName == '') {
                alert('A template name is required.')
            } else {
                $modalInstance.close({
                    saveAsNew: $scope.template.saveAsNew,
                    newName: $scope.template.newName
                })
            }
        };

        $scope.cancel = function() {
            $modalInstance.close()
        };
    })
    .controller('ModalImportTemplateCtrl', function($scope, $modalInstance) {
        $scope.template = {
            newName: '',
            code: ''
        }

        $scope.cancel = function() {
            $modalInstance.close()
        };

        $scope.load = function() {
            if($scope.template.newName == '' || $scope.template.code == '') {
                alert('You need to fill in both fields.')
            } else if(confirm('Are you sure you want to import a new template? Any unsaved changes will be lost.')) {
                $modalInstance.close($scope.template)
            }
        };
    })
    .controller('ModalParagraphCtrl', function($scope, $modalInstance, paragraph) {
        $scope.paragraph = paragraph

        $scope.ok = function(paragraph) {
            $modalInstance.close(paragraph)
        };
    })
    .controller('ModalConditionsCtrl', function($scope, $modalInstance, block, conditions) {
        $scope.type = block['type']
        $scope.conditions = conditions

        $scope.hasResponsiveCondition = false
        for(var i = 0, len = conditions.length; i < len; i++) {
            if(conditions[i]['condition_type'] == 'responsive') {
                $scope.hasResponsiveCondition = true
            }
        }

        $scope.targetOptions = [{
            id: 'block', name: 'this block' }
        ];

        if(block['type']=='rows' || block['type']=='table') {
            for(var i = 0, rows = block['rows']; i < rows; i++) {
                $scope.targetOptions.push({ id: 'row-'+i, name: 'row '+(i+1) })
            }
        }

        if(block['type']=='columns' || block['type']=='table') {
            for(var i = 0, cols = block['columns']; i < cols; i++) {
                $scope.targetOptions.push({ id: 'col-'+i, name: 'column '+(i+1) })
            }
        }

        $scope.fieldOptions = [{
            id: 'post_image', name: 'post image' },{
            id: 'post_title', name: 'post title' },{
            id: 'post_content', name: 'post content' },{
            id: 'post_excerpt', name: 'post excerpt' }
        ];

        $scope.settingOptions = [{
            id: 'user_menus_add_to_shopping_list', name: 'add to shopping list button' },{
            id: 'partners_integrations_foodfanatic_enable', name: 'Food Fanatic button' }
        ];

        $scope.whenFieldOptions = [{
            id: 'missing', name: 'missing' },{
            id: 'present', name: 'present' }
        ];

        $scope.whenSettingOptions = [{
            id: 'disabled', name: 'disabled' },{
            id: 'enabled', name: 'enabled' }
        ];

        $scope.whenResponsiveOptions = [{
            id: 'mobile', name: 'mobile' },{
            id: 'desktop', name: 'desktop' }
        ];

        $scope.addFieldCondition = function() {
            var condition = {
                condition_type: 'field',
                target: 'block',
                field: '',
                when: 'missing'
            }

            $scope.conditions.push(condition)
        }

        $scope.addSubFieldCondition = function() {
            var condition = {
                condition_type: 'sub_field',
                target: 'block',
                field: '',
                when: 'missing'
            }

            $scope.conditions.push(condition)
        }

        $scope.addCustomFieldCondition = function() {
            var condition = {
                condition_type: 'custom_field',
                target: 'block',
                field: '',
                when: 'missing'
            }

            $scope.conditions.push(condition)
        }

        $scope.addSettingCondition = function() {
            var condition = {
                condition_type: 'setting',
                target: 'block',
                setting: '',
                when: 'disabled'
            }

            $scope.conditions.push(condition)
        }

        $scope.addResponsiveCondition = function() {
            var condition = {
                condition_type: 'responsive',
                target: 'block',
                when: 'mobile'
            }

            if($scope.hasResponsiveCondition == false) {
                $scope.conditions.push(condition)
                $scope.hasResponsiveCondition = true
            }
        }

        $scope.removeCondition = function(index) {
            if($scope.conditions[index]['condition_type'] == 'responsive') {
                $scope.hasResponsiveCondition = false
            }
            $scope.conditions.splice(index, 1);
        };

        $scope.ok = function(conditions) {
            $modalInstance.close(conditions)
        };
    })
;