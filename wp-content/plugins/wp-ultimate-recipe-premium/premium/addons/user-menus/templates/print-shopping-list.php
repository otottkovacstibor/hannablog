<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WP Ultimate Recipe Plugin</title>
    <script>
        document.title = window.opener.wpurp_print.title;

        document.write('<link rel="stylesheet" type="text/css" href="'+window.opener.wpurp_user_menus.addonUrl + '/css/list-print.css">');
        document.write('<style>' + window.opener.wpurp_user_menus.custom_print_shoppinglist_css + '</style>');
    </script>
</head>
<body onload="setTimeout(function(){window.print()}, 500);">
<script>
    document.write(window.opener.wpurp_user_menus.shoppingListTitle);
    document.write(window.opener.wpurp_user_menus.recipeList);
    document.write(window.opener.wpurp_user_menus.shoppingList);
</script>
</body>
</html>