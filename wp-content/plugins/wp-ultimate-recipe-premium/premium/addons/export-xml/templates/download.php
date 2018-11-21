<?php
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="WPURP_Recipes.xml"');

$exportRecipes = isset( $_POST['exportRecipes'] ) ? base64_decode( $_POST['exportRecipes'] ) : 'Recipe export failed.';
echo $exportRecipes;