<!DOCTYPE HTML>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo get_bloginfo('name'); ?></title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans" type="text/css" media="all">

    <link rel="stylesheet" type="text/css" href="<?php echo $this->addonUrl; ?>/css/shopping-list-mobile.css">
    <script src="<?php echo $this->addonUrl; ?>/js/shopping-list-mobile.js"></script>
</head>
<body class="<?php echo is_rtl() ? 'rtl' : ''; ?>">
<?php
$hash = isset( $hash ) ? $hash : false;
$shopping_list = false;

if( $hash && strlen( $hash ) > 8 ) {
    $hash_date = substr( $hash, 0, 8 );
    $hash_id = substr( $hash, 8 );

    $date = DateTime::createFromFormat( 'Ymd', $hash_date, WPUltimateRecipe::get()->timezone() );

    $shopping_lists = get_option( 'wpurp_meal_plan_shopping_lists', array() );
    if( isset( $shopping_lists[$hash_date] ) && isset( $shopping_lists[$hash_date][$hash_id] ) ) {
        $shopping_list = $shopping_lists[$hash_date][$hash_id];
    } else {
        _e( 'No shopping list found.', 'wp-ultimate-recipe' );
    }
} else {
    _e( 'No shopping list found.', 'wp-ultimate-recipe' );
}

if( $shopping_list ) {
?>
<div id="wpurp-meal-plan-shopping-list-mobile-tip">
    <?php
    echo __( 'Tip! Go to this link on mobile to easily use your shopping list:', 'wp-ultimate-recipe' ) . '<br/>';
    $schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
    $link = $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo '<a href="' . $link . '">' . $link . '</a>';
    ?>
</div>
<table id="wpurp-meal-plan-shopping-list">
    <thead>
    <tr>
        <th><?php _e( 'Shopping List', 'wp-ultimate-recipe' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $id = 0;
    foreach( $shopping_list as $group ) {
        $group_name = isset( $group['name'] ) ? $group['name'] : '';
        echo '<tr class="wpurp-shopping-list-group">';
        echo '<td><span class="wpurp-shopping-list-group-name">' . $group_name . '</span></td>';
        echo '</tr>';

        if( isset( $group['ingredients'] ) ) {
            foreach( $group['ingredients'] as $ingredient ) {
                $ingredient_name = isset( $ingredient['name'] ) ? $ingredient['name'] : '';
                $ingredient_quantity = isset( $ingredient['quantity'] ) ? $ingredient['quantity'] : '';

                echo '<tr class="wpurp-shopping-list-ingredient">';
                echo '<td><input type="checkbox" id="ingredient-' . $id . '" class="wpurp-shopping-list-ingredient-checkbox"> <label for="ingredient-' . $id . '">' . $ingredient_quantity . ' ' . $ingredient_name . '</label></td>';
                echo '</tr>';
                $id++;
            }
        }
    }
    ?>
    </tbody>
</table>
<?php } ?>
</body>
</html>