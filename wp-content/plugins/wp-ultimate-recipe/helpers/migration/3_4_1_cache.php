<?php
/*
 * -> 3.4.1
 *
 * Fix cache in some environments
 */

// Delete old option
delete_option( 'wpurp_custom_templates' );

// Remove possibly problematic caches
delete_option( 'wpurp_cache' );
delete_option( 'wpurp_cache_temp' );

// Trigger cache reset
WPUltimateRecipe::get()->helper( 'cache' )->trigger_reset();
