<!doctype html>
<html lang="en" ng-app="templateEditor">
<head>
    <meta charset="utf-8">

    <title>WP Ultimate Recipe - Recipe Template Editor</title>
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
                <span class="navbar-brand">WP Ultimate Recipe Template Editor</span>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="http://www.wpultimaterecipe.com/docs/wp-ultimate-recipe-plugin/template-editor-lessons/" target="_blank">Video Lessons</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Templates <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" ng-click="openLoadTemplateModal()">Load Template</a></li>
                        <li><a href="#" ng-click="openSaveTemplateModal()">Save Template</a></li>
                        <li class="divider"></li>
                        <li><a href="#" ng-click="openImportTemplateModal()">Import Template</a></li>
                        <li><a href="#" ng-click="openExportTemplatePage()">Export Template</a></li>
                        <li class="divider"></li>
                        <li><a href="http://www.wpultimaterecipe.com/templates/" target="_blank">Get more templates</a></li>
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
                <div class="recipe-container-page">
                    <div block id="0" class="recipe-container" ng-class="{editing:editingBlock==0}" data-parent="0" data-row="0" data-col="0" droppable>
                </div>
                </div>
                <div id="placeholder"></div>
                <div class="preview-button-container">
<!--                    Preview as <select id="preview-type" ng-model="template.previewType" ng-options="o.id as o.name for o in previewTypeOptions">-->
<!--                    </select></br>-->
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
                        <accordion-group heading="Recipe Tag Fields" class="panel-warning" properties="recipeTags">
                            <div class="lib-block" block-type="recipeTagName" insertable>Tag Name</div>
                            <div class="lib-block" block-type="recipeTagTerms" insertable>Tag Terms</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Ingredient Group Fields" class="panel-warning" properties="recipeIngredients">
                            <div class="lib-block" block-type="recipeIngredientGroup" insertable>Ingredient Group Name</div>
                            <div class="lib-block" block-type="recipeIngredientContainer" insertable>Ingredient Container</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Ingredient Fields" class="panel-warning" properties="recipeIngredientContainer">
                            <div class="lib-block" block-type="recipeIngredientQuantity" insertable>Ingredient Quantity</div>
                            <div class="lib-block" block-type="recipeIngredientUnit" insertable>Ingredient Unit</div>
                            <div class="lib-block" block-type="recipeIngredientName" insertable>Ingredient Name</div>
                            <div class="lib-block" block-type="recipeIngredientNotes" insertable>Ingredient Notes</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Instruction Group Fields" class="panel-warning" properties="recipeInstructions">
                            <div class="lib-block" block-type="recipeInstructionGroup" insertable>Instruction Group Name</div>
                            <div class="lib-block" block-type="recipeInstructionContainer" insertable>Instruction Container</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Instruction Fields" class="panel-warning" properties="recipeInstructionContainer">
                            <div class="lib-block" block-type="recipeInstructionText" insertable>Instruction Text</div>
                            <div class="lib-block" block-type="recipeInstructionImage" insertable>Instruction Image</div>
                        </accordion-group>
                        <accordion-group heading="Layout" class="panel-info">
                            <div class="lib-block" block-type="rows" insertable>Rows</div>
                            <div class="lib-block" block-type="columns" insertable>Columns</div>
                            <div class="lib-block" block-type="table" insertable>Table</div>
                            <div class="lib-block" block-type="box" insertable>Box</div>
                        </accordion-group>
                        <accordion-group heading="General" class="panel-info">
                            <div class="lib-block" block-type="date" insertable>Date</div>
                            <div class="lib-block" block-type="icon" insertable>Icon</div>
                            <div class="lib-block" block-type="image" insertable>Image</div>
                            <div class="lib-block" block-type="title" insertable>Title & Text</div>
                            <div class="lib-block" block-type="space" insertable>Space</div>
                            <div class="lib-block" block-type="paragraph" insertable>Paragraph</div>
                            <div class="lib-block" block-type="link" insertable>Link</div>
                            <div class="lib-block" block-type="code" insertable>HTML & Shortcodes</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Fields" class="panel-info">
                            <div class="lib-block" block-type="recipeAuthor" insertable>Author</div>
                            <div class="lib-block" block-type="recipePostContent" insertable>Post Content</div>
                            <div class="lib-block" block-type="recipeDate" insertable>Date</div>
                            <div class="lib-block" block-type="recipeImage" insertable>Image</div>
                            <div class="lib-block" block-type="recipeTitle" insertable>Title</div>
                            <div class="lib-block" block-type="recipeDescription" insertable>Description</div>
                            <div class="lib-block" block-type="recipeStars" insertable>Star Rating</div>
                            <div class="lib-block" block-type="recipeTags" insertable>Tags</div>
                            <div class="lib-block" block-type="recipeServings" insertable>Servings</div>
                            <div class="lib-block" block-type="recipeServingsType" insertable>Servings Type</div>
                            <div class="lib-block" block-type="recipeCookTime" insertable>Cook Time</div>
                            <div class="lib-block" block-type="recipeCookTimeUnit" insertable>Cook Time Unit</div>
                            <div class="lib-block" block-type="recipePrepTime" insertable>Prep Time</div>
                            <div class="lib-block" block-type="recipePrepTimeUnit" insertable>Prep Time Unit</div>
                            <div class="lib-block" block-type="recipePassiveTime" insertable>Passive Time</div>
                            <div class="lib-block" block-type="recipePassiveTimeUnit" insertable>Passive Time Unit</div>
                            <div class="lib-block" block-type="recipeIngredients" insertable>Ingredients</div>
                            <div class="lib-block" block-type="recipeInstructions" insertable>Instructions</div>
                            <div class="lib-block" block-type="recipeNotes" insertable>Notes</div>
                            <div class="lib-block" block-type="recipeLink" insertable>Link</div>
                            <div class="lib-block" block-type="recipeCustomField" insertable>Custom Field</div>
                        </accordion-group>
                        <accordion-group heading="Recipe Functionality" class="panel-info">
                            <div class="lib-block" block-type="printButton" insertable>Print Button</div>
                            <div class="lib-block" block-type="addToMealPlan" insertable>Add to Meal Plan</div>
                            <div class="lib-block" block-type="addToShoppingList" insertable>Add to Shopping List</div>
                            <div class="lib-block" block-type="favoriteRecipe" insertable>Favorite Recipe</div>
                            <div class="lib-block" block-type="servingsChanger" insertable>Servings Changer</div>
                            <div class="lib-block" block-type="unitChanger" insertable>Unit Changer</div>
                            <div class="lib-block" block-type="recipeSharing" insertable>Recipe Sharing</div>
                        </accordion-group>
                        <accordion-group heading="Nutritional Information" class="panel-info">
                            <div class="lib-block" block-type="nutritionLabel" insertable>Nutrition Label</div>
                            <div class="lib-block" block-type="calories" insertable>Calories</div>
                            <div class="lib-block" block-type="totalFat" insertable>Total Fat</div>
                            <div class="lib-block" block-type="saturatedFat" insertable>Saturated Fat</div>
                            <div class="lib-block" block-type="transFat" insertable>Trans Fat</div>
                            <div class="lib-block" block-type="polyunsaturatedFat" insertable>Polyunsaturated Fat</div>
                            <div class="lib-block" block-type="monounsaturatedFat" insertable>Monounsaturated Fat</div>
                            <div class="lib-block" block-type="cholesterol" insertable>Cholesterol</div>
                            <div class="lib-block" block-type="sodium" insertable>Sodium</div>
                            <div class="lib-block" block-type="potassium" insertable>Potassium</div>
                            <div class="lib-block" block-type="totalCarbohydrates" insertable>Total Carbohydrates</div>
                            <div class="lib-block" block-type="dietaryFiber" insertable>Dietary Fiber</div>
                            <div class="lib-block" block-type="sugars" insertable>Sugars</div>
                            <div class="lib-block" block-type="protein" insertable>Protein</div>
                            <div class="lib-block" block-type="vitaminA" insertable>Vitamin A</div>
                            <div class="lib-block" block-type="vitaminC" insertable>Vitamin C</div>
                            <div class="lib-block" block-type="calcium" insertable>Calcium</div>
                            <div class="lib-block" block-type="iron" insertable>Iron</div>
                        </accordion-group>
                        <accordion-group heading="Partners" class="panel-info">
                            <div class="lib-block" block-type="bigOven" insertable>BigOven</div>
                            <div class="lib-block" block-type="chicory" insertable>Chicory</div>
                            <div class="lib-block" block-type="foodFanatic" insertable>Food Fanatic</div>
                            <div class="lib-block" block-type="yummly" insertable>Yummly</div>
                        </accordion-group>
                        <accordion-group heading="Social Sharing" class="panel-info">
                            <div class="lib-block" block-type="twitter" insertable>Twitter</div>
                            <div class="lib-block" block-type="facebook" insertable>Facebook</div>
                            <div class="lib-block" block-type="google" insertable>Google+</div>
                            <div class="lib-block" block-type="pinterest" insertable>Pinterest</div>
                            <div class="lib-block" block-type="stumbleupon" insertable>StumbleUpon</div>
                            <div class="lib-block" block-type="linkedin" insertable>Linkedin</div>
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
                    <span ng-show="editingBlock==0">Recipe Container</span>
                    <span ng-hide="editingBlock==null||editingBlock==0">{{ template.blocks[editingBlock]['type'] | camelCaseToHuman }}</span>
                </div>
                <div class="panel-body">
                    <p ng-hide="editingBlock!=null">
                        Select a block to edit its properties
                    </p>

                    <accordion close-others="true" ng-show="editingBlock!=null">
                        <accordion-group heading="Shorten Text" class="panel-info" properties="recipePostContent,recipeTitle,recipeDescription,recipeNotes,recipeCustomField">
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

                        <accordion-group heading="Date" class="panel-info" properties="date,recipeDate">
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

                        <accordion-group heading="Icon" class="panel-info" properties="icon">
                            <div class="form-group">
                                <label>Icon</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control" ng-model="template.blocks[editingBlock]['iconName']" ng-options="o.id as o.name for o in iconNameOptions">
                                    </select>
                                </div>

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

                        <accordion-group heading="Group Options" class="panel-info" properties="recipeIngredients,recipeInstructions">
                            <div class="form-group">
                                <label for="showGroups">Groups</label>
                                <div class="input-group input-group-sm">
                                    <select id="showGroups" class="form-control" ng-model="template.blocks[editingBlock]['showGroups']" ng-options="o.id as o.name for o in showGroupsOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" ng-hide="template.blocks[editingBlock]['showGroups']=='all'">
                                <label for="showGroupsList">Groups (Separate with ;)</label>
                                <input type="text" class="form-control" id="showGroupsList" ng-model="template.blocks[editingBlock]['showGroupsList']">
                            </div>
                        </accordion-group>

                        <accordion-group heading="List Options" class="panel-info" properties="recipeTags,recipeIngredientContainer,recipeInstructionContainer">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['actAsList']"> Include list tags
                                </label>
                            </div>
                            <div class="form-group" ng-show="template.blocks[editingBlock]['actAsList']">
                                <label for="listStyle">List Style</label>
                                <div class="input-group input-group-sm">
                                    <select id="listStyle" class="form-control" ng-model="template.blocks[editingBlock]['listStyle']" ng-options="o.id as o.name for o in listStyleOptions">
                                    </select>
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Recipe Instructions" class="panel-info" properties="recipeInstructions">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['recipeInstructionsImages']"> Show instruction images (legacy setting)
                                </label>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Custom Field" class="panel-info" properties="recipeCustomField">
                            <div class="form-group">
                                <label for="recipeCustomFieldKey">Custom Field Key</label>
                                <input type="text" class="form-control" id="recipeCustomFieldKey" ng-model="template.blocks[editingBlock]['recipeCustomFieldKey']">
                            </div>
                        </accordion-group>

                        <accordion-group heading="Icon" class="panel-info" properties="recipeStars">
                            <div class="form-group">
                                <label for="recipeStarsIconFull">Font Awesome Icon (full)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">fa-</span>
                                    <input type="text" class="form-control" id="recipeStarsIconFull" ng-model="template.blocks[editingBlock]['recipeStarsIconFull']">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="recipeStarsIconHalf">Font Awesome Icon (half)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">fa-</span>
                                    <input type="text" class="form-control" id="recipeStarsIconHalf" ng-model="template.blocks[editingBlock]['recipeStarsIconHalf']">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="recipeStarsIconEmpty">Font Awesome Icon (empty)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">fa-</span>
                                    <input type="text" class="form-control" id="recipeStarsIconEmpty" ng-model="template.blocks[editingBlock]['recipeStarsIconEmpty']">
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Icon" class="panel-info" properties="favoriteRecipe,addToMealPlan,addToShoppingList,printButton">
                            <div class="form-group">
                                <label for="icon">Font Awesome Icon</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">fa-</span>
                                    <input type="text" class="form-control" id="icon" ng-model="template.blocks[editingBlock]['icon']">
                                </div>
                            </div>
                            <div class="form-group" properties="favoriteRecipe">
                                <label for="iconAlt">Font Awesome Icon (favorite)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-addon">fa-</span>
                                    <input type="text" class="form-control" id="iconAlt" ng-model="template.blocks[editingBlock]['iconAlt']">
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Nutritional Information" class="panel-info" properties="calories,totalFat,saturatedFat,transFat,polyunsaturatedFat,monounsaturatedFat,cholesterol,sodium,potassium,totalCarbohydrates,dietaryFiber,sugars,protein,vitaminA,vitaminC,calcium,iron">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['showUnit']"> Show unit
                                </label>
                            </div>
                            <div class="checkbox" properties="totalCarbohydrates,protein,totalFat,saturatedFat,cholesterol,sodium,potassium,dietaryFiber">
                                <label>
                                    <input type="checkbox" ng-model="template.blocks[editingBlock]['nutritionPercentage']"> Use percentage value
                                </label>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Social Sharing" class="panel-info" properties="twitter,facebook,google,pinterest,stumbleupon,linkedin">
                            <div class="form-group" properties="facebook">
                                <label for="facebookLayout">Layout</label>
                                <div class="input-group input-group-sm">
                                    <select id="facebookLayout" class="form-control" ng-model="template.blocks[editingBlock]['socialLayout']" ng-options="o.id as o.name for o in facebookLayoutOptions">
                                    </select>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" ng-model="template.blocks[editingBlock]['facebookShare']"> Include Share button
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" properties="google">
                                <label for="googleLayout">Layout</label>
                                <div class="input-group input-group-sm">
                                    <select id="googleLayout" class="form-control" ng-model="template.blocks[editingBlock]['socialLayout']" ng-options="o.id as o.name for o in googleLayoutOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" properties="pinterest">
                                <label for="pinterestLayout">Layout</label>
                                <div class="input-group input-group-sm">
                                    <select id="pinterestLayout" class="form-control" ng-model="template.blocks[editingBlock]['socialLayout']" ng-options="o.id as o.name for o in pinterestLayoutOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" properties="stumbleupon">
                                <label for="stumbleuponLayout">Layout</label>
                                <div class="input-group input-group-sm">
                                    <select id="stumbleuponLayout" class="form-control" ng-model="template.blocks[editingBlock]['socialLayout']" ng-options="o.id as o.name for o in stumbleuponLayoutOptions">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" properties="linkedin">
                                <label for="linkedinLayout">Layout</label>
                                <div class="input-group input-group-sm">
                                    <select id="linkedinLayout" class="form-control" ng-model="template.blocks[editingBlock]['socialLayout']" ng-options="o.id as o.name for o in linkedinLayoutOptions">
                                    </select>
                                </div>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Block Positioning" class="panel-success" properties="!space">
                            <div class="form-group">
                                <label for="float">Float</label>
                                <div class="input-group input-group-sm">
                                    <select id="float" class="form-control" ng-model="template.blocks[editingBlock]['float']" ng-options="o.id as o.name for o in floatOptions">
                                    </select>
                                </div>
                            </div>

                            <div class="checkbox">
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
                                <div class="checkbox" properties="recipeImage,recipeInstructionImage">
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

                            <div class="form-group">
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
                            <div class="form-group">
                                <label for="fontTransform">Transform</label>
                                <div class="input-group input-group-sm">
                                    <select id="fontTransform" class="form-control" ng-model="template.blocks[editingBlock]['fontTransform']" ng-options="o.id as o.name for o in fontTransformOptions">
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
                            <div class="form-group" properties="recipeInstructionContainer,recipeIngredientContainer">
                                <label for="customStyleItem">Inline CSS (Item)</label>
                                <textarea class="form-control" id="customStyleItem" ng-model="template.blocks[editingBlock]['customStyleItem']"></textarea>
                            </div>
                            <div class="form-group" properties="recipeInstructionContainer,recipeIngredientContainer">
                                <label for="customStyleFirst">Inline CSS (First item)</label>
                                <textarea class="form-control" id="customStyleFirst" ng-model="template.blocks[editingBlock]['customStyleFirst']"></textarea>
                            </div>
                            <div class="form-group" properties="recipeInstructionContainer,recipeIngredientContainer">
                                <label for="customStyleLast">Inline CSS (Last item)</label>
                                <textarea class="form-control" id="customStyleLast" ng-model="template.blocks[editingBlock]['customStyleLast']"></textarea>
                            </div>
                            <div class="form-group" properties="recipeInstructionContainer,recipeIngredientContainer">
                                <label for="customStyleOdd">Inline CSS (Odd items)</label>
                                <textarea class="form-control" id="customStyleOdd" ng-model="template.blocks[editingBlock]['customStyleOdd']"></textarea>
                            </div>
                            <div class="form-group" properties="recipeInstructionContainer,recipeIngredientContainer">
                                <label for="customStyleEven">Inline CSS (Even items)</label>
                                <textarea class="form-control" id="customStyleEven" ng-model="template.blocks[editingBlock]['customStyleEven']"></textarea>
                            </div>
                        </accordion-group>

                        <accordion-group heading="Grid Hover ({{template.blocks[editingBlock]['hover']}})" class="panel-warning" properties="!container">
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
                                        <div class="form-group" ng-show="condition.condition_type=='tag'">
                                            <input type="text" class="form-control" ng-model="condition.term" placeholder="term slug">
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
                                        <div class="form-group" ng-show="condition.condition_type=='tag'">
                                            <select class="form-control" ng-model="condition.when" ng-options="o.id as o.name for o in whenTagOptions">
                                            </select>
                                        </div>
                                        <div class="form-group" ng-show="condition.condition_type=='tag'">
                                            <input type="text" class="form-control" ng-model="condition.taxonomy" placeholder="taxonomy (category, post_tag, ...)">
                                        </div>
                                        <a href="#" class="link-danger" ng-click="removeCondition($index)">(Remove)</a>
                                    </div>
                                </div>
                                <button class="btn btn-success" ng-click="addFieldCondition()">Add Field Condition</button>
                                <button class="btn btn-success" ng-click="addSubFieldCondition()">Add Sub Field Condition</button>
                                <button class="btn btn-success" ng-click="addCustomFieldCondition()">Add Custom Field Condition</button><br/><br/>
                                <button class="btn btn-success" ng-click="addTagCondition()">Add Tag Condition</button>
                                <button class="btn btn-success" ng-click="addSettingCondition()">Add Settings Condition</button>
                                <button class="btn btn-success" ng-click="addResponsiveCondition()" ng-disabled="hasResponsiveCondition">Add Responsive Condition</button>
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