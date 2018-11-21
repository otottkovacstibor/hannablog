<!doctype html>
<html lang="en" ng-app="templateEditor">
<head>
    <meta charset="utf-8">

    <title>WP Ultimate Post Grid - Template Editor</title>
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script src="../vendor/jquery.min.js"></script>
    <script src="../vendor/angular.min.js"></script>
    <script src="../vendor/ui-bootstrap-0.11.0.min.js"></script>
    <script src="../vendor/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.js"></script>
    <script src="../vendor/redactor/redactor.min.js"></script>
    <script src="../vendor/angular-redactor.js"></script>

    <script src="../js/editor.js"></script>
    <script src="../js/editor/app.js"></script>
    <script src="../js/editor/controllers.js"></script>
    <script src="../js/editor/directives.js"></script>
    <script src="../js/editor/filters.js"></script>
    <script src="../js/editor/services.js"></script>

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../vendor/bootstrap/stylesheets/bootstrap.css">
    <link rel="stylesheet" href="../vendor/angular-bootstrap-colorpicker/css/colorpicker.css">
    <link rel="stylesheet" href="../vendor/redactor/redactor.css">
    <link rel="stylesheet" href="../css/editor.css">
</head>
<body ng-controller="TemplateEditorCtrl">
<div class="wrapper">
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <span class="navbar-brand">WP Ultimate Post Grid Template Editor</span>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Templates <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" ng-click="openLoadTemplateModal()">Load Template</a></li>
                        <li><a href="#" ng-click="openSaveTemplateModal()">Save Template</a></li>
                        <li class="divider"></li>
                        <li><a href="#" ng-click="openImportTemplateModal()">Import Template</a></li>
                        <li><a href="#" ng-click="openExportTemplatePage()">Export Template</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.container-fluid -->
    </nav>

    <script type="text/ng-template" id="loadTemplateModal.html">
        <div class="modal-header">
            <h3 class="modal-title">Load Template</h3>
        </div>
        <div class="modal-body">
            <div ng-show="templates.length==0">
                We were unable to load the templates. Please try again.
            </div>
            <table class="table table-striped" ng-hide="templates.length==0">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Active?</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="template in templates">
                    <td>{{template.id}} - {{template.name}}</td>
                    <td><span class="label label-default" ng-hide="template.active==''">{{template.active}}</span></td>
                    <td><button class="btn btn-primary btn-xs" ng-click="load(template)">Load</button> <button class="btn btn-danger btn-xs" ng-click="delete(template)" ng-hide="template.active">Delete</button></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" ng-click="cancel()">Cancel</button>
        </div>
    </script>

    <script type="text/ng-template" id="saveTemplateModal.html">
        <div class="modal-header">
            <h3 class="modal-title">Save Template</h3>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <strong ng-hide="template.saveAsNew">{{template.name}}</strong>
                <input type="text" class="form-control" placeholder="Template Name" ng-model="template.newName" ng-show="template.saveAsNew">
            </div>
            <p ng-show="(template.id<=2||template.id==99)&&template.id!=null">The default templates will be reset upon activation, so please save as a new one.</p>
            <p ng-hide="template.saveAsNew">The current template will be overwritten. <a href="#" ng-click="template.saveAsNew=true">Save as a new template instead?</a></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" ng-click="cancel()">Cancel</button>
            <button class="btn btn-primary" ng-click="ok()">Save</button>
        </div>
    </script>

    <script type="text/ng-template" id="importTemplateModal.html">
        <div class="modal-header">
            <h3 class="modal-title">Import Template</h3>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="control-label" for="importTemplateName">Name</label>
                <input type="text" class="form-control" id="importTemplateName" placeholder="My Template" ng-model="template.newName">
            </div>
            <div class="form-group">
                <label class="control-label" for="importTemplateCode">Template Code</label>
                <textarea class="form-control" rows="6" id="importTemplateCode" ng-model="template.code"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-default" ng-click="cancel()">Cancel</button>
            <button class="btn btn-primary" ng-click="load()">Import</button>
        </div>
    </script>

    <form id="exportTemplate" action="export.php" method="post" target="_blank">
        <input type="hidden" name="exportTemplate" id="exportTemplateValue" value=""/>
    </form>

    <div class="middle">

        <div class="container">
            <main class="content">
                <div id="alerts-container" ng-hide="!alerts.length">
                    <alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</alert>
                </div>
                <div class="post-container-page">
                    <div block id="0" class="post-container" ng-class="{editing:editingBlock==0}" data-parent="0" data-row="0" data-col="0" droppable>
                </div>
                </div>
                <div id="placeholder"></div>
                <div class="preview-button-container">
                    <button ng-click="parseTemplate()" class="btn btn-primary">Preview Template</button>
                </div>
                <div id="preview-container">
                    <div id="preview" ng-bind-html="preview">
                </div>
                </div>
            </main><!-- .content -->
        </div><!-- .container-->

        <aside class="left-sidebar">
            <div id="panel-building" class="panel panel-primary">
                <div class="panel-heading">
                    Building Blocks
                </div>
                <div class="panel-body">
                    <accordion close-others="true">
                        <accordion-group heading="Layout" class="panel-info">
                            <div class="lib-block" block-type="rows" insertable>Rows</div>
                            <div class="lib-block" block-type="columns" insertable>Columns</div>
                            <div class="lib-block" block-type="table" insertable>Table</div>
                            <div class="lib-block" block-type="box" insertable>Box</div>
                        </accordion-group>
                        <accordion-group heading="General" class="panel-info">
                            <div class="lib-block" block-type="date" insertable>Date</div>
                            <div class="lib-block" block-type="image" insertable>Image</div>
                            <div class="lib-block" block-type="title" insertable>Title & Text</div>
                            <div class="lib-block" block-type="space" insertable>Space</div>
                            <div class="lib-block" block-type="paragraph" insertable>Paragraph</div>
                            <div class="lib-block" block-type="link" insertable>Link</div>
                            <div class="lib-block" block-type="code" insertable>HTML & Shortcodes</div>
                        </accordion-group>
                        <accordion-group heading="Post Fields" class="panel-info">
                            <div class="lib-block" block-type="postImage" insertable>Image</div>
                            <div class="lib-block" block-type="postTitle" insertable>Title</div>
                            <div class="lib-block" block-type="postContent" insertable>Content</div>
                            <div class="lib-block" block-type="postExcerpt" insertable>Excerpt</div>
                            <div class="lib-block" block-type="postDate" insertable>Date</div>
                            <div class="lib-block" block-type="postAuthor" insertable>Author</div>
                            <div class="lib-block" block-type="postTaxonomyTerms" insertable>Taxonomy Terms</div>
                            <div class="lib-block" block-type="postCustomField" insertable>Custom Field</div>
                        </accordion-group>
                    </accordion>
                </div>
            </div>
        </aside><!-- .left-sidebar -->

        <aside class="right-sidebar">
            <div class="block-actions">
                <button class="btn btn-danger" ng-click="deleteBlock(editingBlock)" ng-disabled="editingBlock==null||editingBlock==0">Delete</button>
                &nbsp;
                <button class="btn btn-info" ng-click="selectParentBlock(editingBlock)" ng-disabled="editingBlock==null||editingBlock==0">Parent</button>
<!--                &nbsp;-->
<!--                <button class="btn btn-warning" ng-click="cloneBlock(editingBlock)" ng-disabled="editingBlock==null||editingBlock==0">Clone</button>-->
            </div>
            <div id="panel-properties" class="panel panel-primary">
                <div class="panel-heading">
                    <span ng-show="editingBlock==null">Properties</span>
                    <span ng-show="editingBlock==0">Post Container</span>
                    <span ng-hide="editingBlock==null||editingBlock==0">{{ template.blocks[editingBlock]['type'] | camelCaseToHuman }}</span>
                </div>
                <div class="panel-body">
                    <p ng-hide="editingBlock!=null">
                        Select a block to edit its properties
                    </p>

                    <accordion close-others="true" ng-show="editingBlock!=null">
                        <accordion-group heading="Shorten Text" class="panel-info" properties="postTitle,postContent,postExcerpt,postCustomField">
                            <div class="form-group">
                                <div class="input-group input-group-sm">
                                    <select id="shortenText" class="form-control" ng-model="template.blocks[editingBlock]['shortenText']" ng-options="o.id as o.name for o in shortenTextOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" ng-show="template.blocks[editingBlock]['shortenText']!='none'">
                                <label>Value for X</label>
                                <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['shortenTextNumber']">
                            </div>
                            <div class="form-group" ng-show="template.blocks[editingBlock]['shortenText']!='none'">
                                <label>Text after cut off</label>
                                <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['shortenTextAfter']">
                            </div>
                        </accordion-group>

                        <accordion-group heading="Rows" class="panel-info" properties="rows,table">
                            <div class="form-group">
                                <label for="rows">Number of Rows</label>
                                <div class="input-group input-group-sm">
                                    <select id="rows" class="form-control" ng-model="template.blocks[editingBlock]['rows']" ng-options="o.id as o.name for o in columnsRowsOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="row0">Height</label>
                                <?php
                                for($i=0; $i<10; $i++)
                                {
                                    ?>
                                    <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['rows']<<?php echo $i+1; ?>">
                                        <span class="input-group-addon"><?php echo $i+1; ?></span>
                                        <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['row<?php echo $i; ?>']" ng-hide="template.blocks[editingBlock]['rowtype<?php echo $i; ?>']=='fluid'">
                                        <input type="text" class="form-control" disabled ng-show="template.blocks[editingBlock]['rowtype<?php echo $i; ?>']=='fluid'">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" ng-click="switchRowType('rowtype<?php echo $i; ?>')">{{template.blocks[editingBlock]['rowtype<?php echo $i; ?>']}} <span class="caret"></span></button>
                                        </span>
                                    </div>
                                <?php
                                } ?>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Columns" class="panel-info" properties="columns,table">
                            <div class="form-group">
                                <label for="columns">Number of Columns</label>
                                <div class="input-group input-group-sm">
                                    <select id="columns" class="form-control" ng-model="template.blocks[editingBlock]['columns']" ng-options="o.id as o.name for o in columnsRowsOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" ng-hide="template.blocks[editingBlock]['columns']==1">
                                <label for="column0">Width</label>
                                <?php
                                for($i=0; $i<10; $i++)
                                {
                                ?>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['columns']<<?php echo $i+1; ?>">
                                    <span class="input-group-addon"><?php echo $i+1; ?></span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['column<?php echo $i; ?>']" ng-hide="template.blocks[editingBlock]['columns']==<?php echo $i+1; ?>">
                                    <span class="input-group-btn" ng-hide="template.blocks[editingBlock]['columns']==<?php echo $i+1; ?>">
                                        <button class="btn btn-default" type="button" ng-click="switchColumnType('columntype<?php echo $i; ?>')">{{template.blocks[editingBlock]['columntype<?php echo $i; ?>']}} <span class="caret"></span></button>
                                    </span>
                                    <input type="text" class="form-control"  value="remainder of width" disabled ng-show="template.blocks[editingBlock]['columns']==<?php echo $i+1; ?>">
                                </div>
                                <?php
                                } ?>
                            </div>
                            <div class="checkbox" ng-hide="template.blocks[editingBlock]['columns']==1" properties="columns">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['columnsResponsive']"> Columns become rows on mobile
                                </label>
                            </div>
                            <div class="checkbox" ng-show="template.blocks[editingBlock]['columnsResponsive']&&template.blocks[editingBlock]['columns']!=1" properties="columns">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['columnsResponsiveReverse']"> Reverse order on mobile
                                </label>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Date" class="panel-info" properties="date,postDate">
                            <div class="form-group">
                                <label>Date Format</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['dateFormat']" ng-options="o.id as o.name for o in dateFormatOptions">
                                        <option value="">Select a Preset</option>
                                    </select>
                                </div>
                                <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['dateFormat']">
                                <a href="http://php.net/manual/en/function.date.php" target="_blank">Syntax</a>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Image" class="panel-info" properties="image">
                            <div class="form-group">
                                <label>Image</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['imagePreset']" ng-options="o.id as o.name for o in imagePresetOptions" ng-change="imagePresetDefaults(template.blocks[editingBlock])">
                                        <option value="">Select a Preset</option>
                                    </select>
                                </div>
                                <imagepicker ng-model="template.blocks[editingBlock]['imageUrl']" image-width="template.blocks[editingBlock]['width']" image-height="template.blocks[editingBlock]['height']"></imagepicker>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Title" class="panel-info" properties="title">
                            <div class="form-group">
                                <label for="tag">Tag</label>
                                <div class="input-group input-group-sm">
                                    <select id="tag" class="form-control" ng-model="template.blocks[editingBlock]['tag']" ng-options="o.id as o.name for o in titleTagOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text">Text</label>
                                <textarea class="form-control" id="text" ng-model="template.blocks[editingBlock]['text']"></textarea>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Space" class="panel-info" properties="space">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['nonBreaking']"> Non-Breaking
                                </label>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Paragraph" class="panel-info" properties="paragraph" ng-click="openEditParagraphModal()">
                        </accordion-group>

                        <script type="text/ng-template" id="editParagraphModal.html">
                            <div class="modal-header">
                                <h3 class="modal-title">Edit Paragraph</h3>
                            </div>
                            <div class="modal-body">
                                <textarea redactor ng-model="paragraph"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" ng-click="ok(paragraph)">OK</button>
                            </div>
                        </script>

                        <accordion-group heading="Link" class="panel-info" properties="link">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['linkNewPage']"> Opens in new page
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="linkUrl">Url</label>
                                <input type="text" class="form-control" id="linkUrl" ng-model="template.blocks[editingBlock]['linkUrl']">
                            </div>
                            <div class="form-group">
                                <label for="linkText">Text</label>
                                <input type="text" class="form-control" id="linkText" ng-model="template.blocks[editingBlock]['text']">
                            </div>
                        </accordion-group>

                        <accordion-group heading="Code" class="panel-info" properties="code">
                            <div class="form-group">
                                <label for="code">Code</label>
                                <textarea class="form-control" id="code" ng-model="template.blocks[editingBlock]['text']" rows="4"></textarea>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Taxonomy Terms" class="panel-info" properties="postTaxonomyTerms">
                            <div class="form-group">
                                <label for="termsTaxonomy">Taxonomy</label>
                                <input type="text" class="form-control" id="termsTaxonomy" ng-model="template.blocks[editingBlock]['termsTaxonomy']">
                                For example: category;post_tag
                            </div>
                            <div class="form-group">
                                <label for="termsSeparator">List Separator</label>
                                <input type="text" class="form-control" id="termsSeparator" ng-model="template.blocks[editingBlock]['termsSeparator']">
                                Use &amp;nbsp; for spaces,<br/>
                                &lt;br/&gt; for line breaks
                            </div>
                        </accordion-group>

                        <accordion-group heading="Custom Field" class="panel-info" properties="postCustomField">
                            <div class="form-group">
                                <label for="postCustomFieldKey">Custom Field Key</label>
                                <input type="text" class="form-control" id="postCustomFieldKey" ng-model="template.blocks[editingBlock]['postCustomFieldKey']">
                            </div>
                        </accordion-group>

                        <accordion-group heading="Block Positioning" class="panel-success" properties="!space">
                            <div class="form-group" properties="!container">
                                <label for="float">Float</label>
                                <div class="input-group input-group-sm">
                                    <select id="float" class="form-control" ng-model="template.blocks[editingBlock]['float']" ng-options="o.id as o.name for o in floatOptions">
                                    </select>
                                </div>
                            </div>

                            <div class="checkbox" properties="!container">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['center']"> Center horizontally
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="marginTop">Margin</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Top</span>
                                    <input type="text" class="form-control" id="marginTop" ng-model="template.blocks[editingBlock]['marginTop']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Bottom</span>
                                    <input type="text" class="form-control" id="marginBottom" ng-model="template.blocks[editingBlock]['marginBottom']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['center']">
                                    <span class="input-group-addon">Left</span>
                                    <input type="text" class="form-control" id="marginLeft" ng-model="template.blocks[editingBlock]['marginLeft']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['center']">
                                    <span class="input-group-addon">Right</span>
                                    <input type="text" class="form-control" id="marginRight" ng-model="template.blocks[editingBlock]['marginRight']">
                                    <span class="input-group-addon">px</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="paddingTop">Padding</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Top</span>
                                    <input type="text" class="form-control" id="paddingTop" ng-model="template.blocks[editingBlock]['paddingTop']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Bottom</span>
                                    <input type="text" class="form-control" id="paddingBottom" ng-model="template.blocks[editingBlock]['paddingBottom']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Left</span>
                                    <input type="text" class="form-control" id="paddingLeft" ng-model="template.blocks[editingBlock]['paddingLeft']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Right</span>
                                    <input type="text" class="form-control" id="paddingRight" ng-model="template.blocks[editingBlock]['paddingRight']">
                                    <span class="input-group-addon">px</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="width">Size</label>
                                <div class="checkbox" properties="postImage">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['imageCrop']"> Crop image (use px)
                                    </label>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Width</span>
                                    <input type="text" class="form-control" id="width" ng-model="template.blocks[editingBlock]['width']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('widthType',['px','%'])">{{template.blocks[editingBlock]['widthType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Height</span>
                                    <input type="text" class="form-control" id="height" ng-model="template.blocks[editingBlock]['height']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('heightType',['px','%'])">{{template.blocks[editingBlock]['heightType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Min-Width</span>
                                    <input type="text" class="form-control" id="minWidth" ng-model="template.blocks[editingBlock]['minWidth']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('minWidthType',['px','%'])">{{template.blocks[editingBlock]['minWidthType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Max-Width</span>
                                    <input type="text" class="form-control" id="maxWidth" ng-model="template.blocks[editingBlock]['maxWidth']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('maxWidthType',['px','%'])">{{template.blocks[editingBlock]['maxWidthType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Min-Height</span>
                                    <input type="text" class="form-control" id="minHeight" ng-model="template.blocks[editingBlock]['minHeight']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('minHeightType',['px','%'])">{{template.blocks[editingBlock]['minHeightType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Max-Height</span>
                                    <input type="text" class="form-control" id="maxHeight" ng-model="template.blocks[editingBlock]['maxHeight']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchType('maxHeightType',['px','%'])">{{template.blocks[editingBlock]['maxHeightType']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" properties="!container">
                                <label for="position">Position (Advanced)</label>
                                <div class="input-group input-group-sm">
                                    <select id="position" class="form-control" ng-model="template.blocks[editingBlock]['position']" ng-options="o.id as o.name for o in positionOptions">
                                    </select>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['position']=='static'">
                                    <span class="input-group-addon">Top</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['positionTop']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['position']=='static'">
                                    <span class="input-group-addon">Bottom</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['positionBottom']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['position']=='static'">
                                    <span class="input-group-addon">Left</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['positionLeft']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['position']=='static'">
                                    <span class="input-group-addon">Right</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['positionRight']">
                                    <span class="input-group-addon">px</span>
                                </div>
                            </div>
                        </accordion-group>
                        <accordion-group heading="Block Style" class="panel-success" properties="!space">
                            <div class="form-group">
                                <label for="backgroundImage">Background</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['backgroundPreset']" ng-options="o.id as o.name for o in backgroundPresetOptions">
                                        <option value="">Select a Preset</option>
                                    </select>
                                </div>
                                <imagepicker ng-model="template.blocks[editingBlock]['backgroundImage']"></imagepicker>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Color</span>
                                    <input colorpicker="rgba" type="text" class="form-control" id="backgroundColor" ng-model="template.blocks[editingBlock]['backgroundColor']">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="borderWidth">Border</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Width</span>
                                    <input type="text" class="form-control" id="borderWidth" ng-model="template.blocks[editingBlock]['borderWidth']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['borderStyle']" ng-options="o.id as o.name for o in borderStyleOptions">
                                    </select>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Color</span>
                                    <input colorpicker="rgba" type="text" class="form-control" ng-model="template.blocks[editingBlock]['borderColor']">
                                </div>
                                <div class="checkbox" ng-hide="template.blocks[editingBlock]['borderWidth']==null||template.blocks[editingBlock]['borderWidth']==0">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['borderTop']"> Top
                                    </label>
                                </div>
                                <div class="checkbox" ng-hide="template.blocks[editingBlock]['borderWidth']==null||template.blocks[editingBlock]['borderWidth']==0">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['borderBottom']"> Bottom
                                    </label>
                                </div>
                                <div class="checkbox" ng-hide="template.blocks[editingBlock]['borderWidth']==null||template.blocks[editingBlock]['borderWidth']==0">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['borderLeft']"> Left
                                    </label>
                                </div>
                                <div class="checkbox" ng-hide="template.blocks[editingBlock]['borderWidth']==null||template.blocks[editingBlock]['borderWidth']==0">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['borderRight']"> Right
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shadowHorizontal">Shadow</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Horizontal</span>
                                    <input type="text" class="form-control" id="shadowHorizontal" ng-model="template.blocks[editingBlock]['shadowHorizontal']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Vertical</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['shadowVertical']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Blur</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['shadowBlur']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Spread</span>
                                    <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['shadowSpread']">
                                    <span class="input-group-addon">px</span>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Color</span>
                                    <input colorpicker="rgba" type="text" class="form-control" ng-model="template.blocks[editingBlock]['shadowColor']">
                                </div>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['shadowType']" ng-options="o.id as o.name for o in shadowTypeOptions">
                                    </select>
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Text Style" class="panel-success" properties="!space">
                            <div class="form-group" properties="!container">
                                <label for="textAlign">Align</label>
                                <div class="input-group input-group-sm">
                                    <select id="textAlign" class="form-control" ng-model="template.blocks[editingBlock]['textAlign']" ng-options="o.id as o.name for o in textAlignOptions">
                                    </select>
                                </div>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['verticalAlign']" ng-options="o.id as o.name for o in verticalAlignOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['fontBold']"> Bold Text
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['fontSmallCaps']"> Small Caps
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="fontSize">Font Size</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="fontSize" ng-model="template.blocks[editingBlock]['fontSize']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchMeasureType('fontSizeUnit')">{{template.blocks[editingBlock]['fontSizeUnit']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lineHeight">Line Height</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="lineHeight" ng-model="template.blocks[editingBlock]['lineHeight']">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" ng-click="switchMeasureType('lineHeightUnit')">{{template.blocks[editingBlock]['lineHeightUnit']}} <span class="caret"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="fontColor">Font Color</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Text</span>
                                    <input colorpicker="rgba" type="text" class="form-control" id="fontColor" ng-model="template.blocks[editingBlock]['fontColor']">
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">Links</span>
                                    <input colorpicker="rgba" type="text" class="form-control" ng-model="template.blocks[editingBlock]['linkColor']">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="fontFamilyType">Font Family</label>
                                <div class="input-group input-group-sm">
                                    <select id="fontFamilyType" class="form-control" ng-model="template.blocks[editingBlock]['fontFamilyType']" ng-options="o.id as o.name for o in fontFamilyTypeOptions">
                                    </select>
                                </div>
                                <input type="text" class="form-control" ng-model="template.blocks[editingBlock]['fontFamilyManual']" ng-show="template.blocks[editingBlock]['fontFamilyType']=='manual'">
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['fontFamilyGWF']" ng-options="o.id as o.name for o in fontFamilyGWFOptions" ng-show="template.blocks[editingBlock]['fontFamilyType']=='gwf'">
                                    </select>
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Custom Style" class="panel-success" properties="!space">
                            <div class="form-group">
                                <label for="customClass">Class Name</label>
                                <input type="text" class="form-control" id="customClass" ng-model="template.blocks[editingBlock]['customClass']">
                            </div>
                            <div class="form-group">
                                <label for="customStyle">Inline CSS</label>
                                <textarea class="form-control" id="customStyle" ng-model="template.blocks[editingBlock]['customStyle']"></textarea>
                            </div>
                        </accordion-group>

                        <accordion-group heading="On Hover ({{template.blocks[editingBlock]['hover']}})" class="panel-warning" properties="!container">
                            <div class="form-group">
                                <label for="hover">Action on Hover</label>
                                <div class="input-group input-group-sm">
                                    <select id="hover" class="form-control" ng-model="template.blocks[editingBlock]['hover']" ng-options="o.id as o.name for o in hoverOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" ng-hide="template.blocks[editingBlock]['hover']=='disabled'">
                                <label for="hoverInTransition">In Effect</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['hoverInTransition']" ng-options="o.id as o.name for o in hoverTransitionOptions">
                                    </select>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['hoverInTransition']=='instant'">
                                    <span class="input-group-addon">Time</span>
                                    <input type="number" class="form-control" id="hoverInTime" ng-model="template.blocks[editingBlock]['hoverInTime']">
                                    <span class="input-group-addon">milliseconds</span>
                                </div>
                            </div>
                            <div class="form-group" ng-hide="template.blocks[editingBlock]['hover']=='disabled'">
                                <label for="hoverOutTransition">Out Effect</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['hoverOutTransition']" ng-options="o.id as o.name for o in hoverTransitionOptions">
                                    </select>
                                </div>
                                <div class="input-group input-group-sm" ng-hide="template.blocks[editingBlock]['hoverOutTransition']=='instant'">
                                    <span class="input-group-addon">Time</span>
                                    <input type="number" class="form-control" id="hoverOutTime" ng-model="template.blocks[editingBlock]['hoverOutTime']">
                                    <span class="input-group-addon">milliseconds</span>
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Conditions ({{template.blocks[editingBlock]['conditions'].length}})" class="panel-warning" properties="!container" ng-click="openEditConditionsModal()">
                        </accordion-group>

                        <script type="text/ng-template" id="editConditionsModal.html">
                            <div class="modal-header">
                                <h3 class="modal-title">Edit Conditions</h3>
                            </div>
                            <div class="modal-body">
                                <div class="modal-condition" ng-show="conditions.length==0">
                                    There are no conditions for this block.
                                </div>
                                <div class="modal-conditions" ng-hide="conditions.length==0" ng-repeat="condition in conditions">
                                    <div class="form-inline modal-condition">
                                        Hide
                                        <div class="form-group" ng-show="condition.condition_type!='responsive'&&(type=='rows'||type=='table'||type=='columns')">
                                            <select class="form-control" ng-model="condition.target" ng-options="o.id as o.name for o in targetOptions">
                                            </select>
                                        </div>
                                        <strong ng-hide="condition.condition_type!='responsive'&&(type=='rows'||type=='table'||type=='columns')">this block</strong>
                                        <span ng-hide="condition.condition_type=='responsive'">when</span>
                                        <div class="form-group" ng-show="condition.condition_type=='field'">
                                            <select class="form-control" ng-model="condition.field" ng-options="o.id as o.name for o in fieldOptions">
                                                <option value="">Field:</option>
                                            </select>
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='sub_field'">
                                            <select class="form-control" ng-model="condition.field" ng-options="o.id as o.name for o in subFieldOptions">
                                                <option value="">Field:</option>
                                            </select>
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='custom_field'">
                                            <input type="text" class="form-control" ng-model="condition.field">
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='setting'">
                                            <select class="form-control" ng-model="condition.setting" ng-options="o.id as o.name for o in settingOptions">
                                                <option value="">Setting:</option>
                                            </select>
                                        </div>
                                        <span ng-hide="condition.condition_type=='responsive'">is</span>
                                        <span ng-show="condition.condition_type=='responsive'">on</span>
                                        <div class="form-group" ng-show="condition.condition_type=='field'||condition.condition_type=='custom_field'||condition.condition_type=='sub_field'">
                                            <select class="form-control" ng-model="condition.when" ng-options="o.id as o.name for o in whenFieldOptions">
                                            </select>
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='setting'">
                                            <select class="form-control" ng-model="condition.when" ng-options="o.id as o.name for o in whenSettingOptions">
                                            </select>
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='responsive'">
                                            <select class="form-control" ng-model="condition.when" ng-options="o.id as o.name for o in whenResponsiveOptions">
                                            </select>
                                        </div>
                                        <a href="#" class="link-danger" ng-click="removeCondition($index)">(Remove)</a>
                                    </div>
                                </div>
                                <button class="btn btn-success" ng-click="addFieldCondition()">Add Field Condition</button>
                                <button class="btn btn-success" ng-click="addCustomFieldCondition()">Add Custom Field Condition</button>
<!--                                <button class="btn btn-success" ng-click="addSettingCondition()">Add Settings Condition</button>-->
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" ng-click="ok(conditions)">OK</button>
                            </div>
                        </script>
                    </accordion>
                </div>
            </div>
            <div class="text-center">
                <input type="button" id="stop-editing" class="btn btn-primary" ng-click="editingBlock=null" ng-disabled="editingBlock==null" value="Stop Editing" />
            </div>
        </aside><!-- .right-sidebar -->

    </div><!-- .middle-->

</div>
</body>
</html>