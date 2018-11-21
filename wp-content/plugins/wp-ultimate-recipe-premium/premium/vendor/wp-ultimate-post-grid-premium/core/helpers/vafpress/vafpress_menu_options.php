<?php

// Include part of site URL hash in HTML settings to update when site URL changes
$sitehash = substr( md5( WPUltimatePostGrid::get()->coreUrl ), 0, 8 );

$template_editor_button = WPUltimatePostGrid::is_addon_active( 'template-editor' ) ? 'grid_template_open_template_editor_active' . $sitehash : 'grid_template_open_template_editor_disabled';

$admin_menu = array(
    'title' => 'WP Ultimate Post Grid ' . __('Settings', 'wp-ultimate-post-grid'),
    'logo'  => WPUltimatePostGrid::get()->coreUrl . '/img/logo.png',
    'menus' => array(
//=-=-=-=-=-=-= GRID TEMPLATE =-=-=-=-=-=-=
        array(
            'title' => __('Grid Template', 'wp-ultimate-post-grid'),
            'name' => 'grid_template',
            'icon' => 'font-awesome:fa-picture-o',
            'menus' => array(
                array(
                    'title' => __('Template Editor', 'wp-ultimate-post-grid'),
                    'name' => 'grid_template_template_editor_menu',
                    'controls' => array(
                        array(
                            'type' => 'notebox',
                            'name' => 'grid_template_premium_not_installed',
                            'label' => 'WP Ultimate Post Grid Premium',
                            'description' => __('These features are only available in', 'wp-ultimate-post-grid') . ' <a href="http://bootstrapped.ventures/wp-ultimate-post-grid/" target="_blank">WP Ultimate Post Grid Premium</a></strong>.',
                            'status' => 'warning',
                            'dependency' => array(
                                'field' => '',
                                'function' => 'wpupg_admin_premium_not_installed',
                            ),
                        ),
                        array(
                            'type' => 'section',
                            'title' => __('Template Editor', 'wp-ultimate-post-grid'),
                            'name' => 'grid_template_editor',
                            'fields' => array(
                                array(
                                    'type' => 'html',
                                    'name' => $template_editor_button,
                                    'binding' => array(
                                        'field'    => '',
                                        'function' => 'wpupg_admin_template_editor',
                                    ),
                                ),
                                array(
                                    'type' => 'select',
                                    'name' => 'template_editor_preview_grid',
                                    'label' => __('Preview Grid', 'wp-ultimate-post-grid'),
                                    'description' => __( 'This grid will be used for the preview in the editor.', 'wp-ultimate-post-grid' ),
                                    'items' => array(
                                        'data' => array(
                                            array(
                                                'source' => 'function',
                                                'value' => 'wpupg_admin_grids',
                                            ),
                                        ),
                                    ),
                                    'default' => array(
                                        '{{first}}',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'title' => __('Advanced', 'wp-ultimate-post-grid'),
                    'name' => 'grid_template_advanced_menu',
                    'controls' => array(
                        array(
                            'type' => 'section',
                            'title' => 'CSS',
                            'name' => 'grid_template_advanced_styling',
                            'fields' => array(
                                array(
                                    'type' => 'toggle',
                                    'name' => 'grid_template_force_style',
                                    'label' => __('Force CSS style', 'wp-ultimate-post-grid'),
                                    'description' => __( 'This ensures maximum compatibility with most themes. Can be disabled for advanced usage.', 'wp-ultimate-post-grid' ),
                                    'default' => '0',
                                ),
                                array(
                                    'type' => 'toggle',
                                    'name' => 'grid_template_inline_css',
                                    'label' => __('Output Inline CSS', 'wp-ultimate-post-grid'),
                                    'description' => __( 'When disabled the Template Editor will not output any inline CSS.', 'wp-ultimate-post-grid' ),
                                    'default' => '1',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= GRID =-=-=-=-=-=-=
        array(
            'title' => __('Grid', 'wp-ultimate-post-grid'),
            'name' => 'grid',
            'icon' => 'font-awesome:fa-th-large',
            'controls' => array(
                array(
                    'type' => 'section',
                    'title' => __('Animations', 'wp-ultimate-post-grid'),
                    'name' => 'grid_animations',
                    'fields' => array(
                        array(
                            'type' => 'slider',
                            'name' => 'grid_animation_speed',
                            'label' => __( 'Animation Speed', 'wp-ultimate-post-grid' ),
                            'description' => __( 'Duration of the animations in seconds.', 'wp-ultimate-post-grid' ),
                            'min' => '0',
                            'max' => '5',
                            'step' => '0.05',
                            'default' => '0.8',
                        ),
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= GRID =-=-=-=-=-=-=
        array(
            'title' => __('Filters', 'wp-ultimate-post-grid'),
            'name' => 'filters',
            'icon' => 'font-awesome:fa-filter',
            'controls' => array(
                array(
                    'type' => 'notebox',
                    'name' => 'filters_premium_not_installed',
                    'label' => 'WP Ultimate Post Grid Premium',
                    'description' => __('These features are only available in', 'wp-ultimate-post-grid') . ' <a href="http://bootstrapped.ventures/wp-ultimate-post-grid/" target="_blank">WP Ultimate Post Grid Premium</a></strong>.',
                    'status' => 'warning',
                    'dependency' => array(
                        'field' => '',
                        'function' => 'wpupg_admin_premium_not_installed',
                    ),
                ),
                array(
                    'type' => 'section',
                    'title' => __( 'Dropdown Filters', 'wp-ultimate-post-grid' ),
                    'name' => 'filters_dropdown',
                    'fields' => array(
                        array(
                            'type' => 'color',
                            'name' => 'filters_dropdown_border_color',
                            'label' => __( 'Border Color', 'wp-ultimate-post-grid' ),
                            'default' => '#AAAAAA',
                            'format' => 'hex',
                        ),
                        array(
                            'type' => 'color',
                            'name' => 'filters_dropdown_text_color',
                            'label' => __( 'Default Text Color', 'wp-ultimate-post-grid' ),
                            'default' => '#444444',
                            'format' => 'hex',
                        ),
                        array(
                            'type' => 'color',
                            'name' => 'filters_dropdown_highlight_background_color',
                            'label' => __( 'Highlight Background Color', 'wp-ultimate-post-grid' ),
                            'default' => '#5897FB',
                            'format' => 'hex',
                        ),
                        array(
                            'type' => 'color',
                            'name' => 'filters_dropdown_highlight_text_color',
                            'label' => __( 'Highlight Text Color', 'wp-ultimate-post-grid' ),
                            'default' => '#FFFFFF',
                            'format' => 'hex',
                        ),
                        array(
                            'type' => 'toggle',
                            'name' => 'filters_dropdown_hide_search',
                            'label' => __('Hide Search', 'wp-ultimate-post-grid'),
                            'description' => __( 'Hide the search input for single select dropdowns.', 'wp-ultimate-post-grid' ),
                            'default' => '0',
                        ),
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= ADVANCED =-=-=-=-=-=-=
        array(
            'title' => __('Advanced', 'wp-ultimate-post-grid'),
            'name' => 'advanced',
            'icon' => 'font-awesome:fa-wrench',
            'controls' => array(
                array(
                    'type' => 'section',
                    'title' => __('Shortcode Editor', 'wp-ultimate-post-grid'),
                    'name' => 'advanced_section_shortcode',
                    'fields' => array(
                        array(
                            'type' => 'multiselect',
                            'name' => 'shortcode_editor_post_types',
                            'label' => __('Show shortcode editor for', 'wp-ultimate-post-grid'),
                            'description' => __( 'Where do you want to be able to insert grids with the shortcode editor?', 'wp-ultimate-post-grid' ),
                            'items' => array(
                                'data' => array(
                                    array(
                                        'source' => 'function',
                                        'value' => 'wpupg_admin_post_types',
                                    ),
                                ),
                            ),
                            'default' => array(
                                'post',
                                'page',
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'section',
                    'title' => __('Assets', 'wp-ultimate-post-grid'),
                    'name' => 'advanced_section_assets',
                    'fields' => array(
                        array(
                            'type' => 'toggle',
                            'name' => 'assets_use_cache',
                            'label' => __('Cache Assets', 'wp-ultimate-post-grid'),
                            'description' => __( 'Disable this while developing.', 'wp-ultimate-post-grid' ),
                            'default' => '1',
                        ),
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= CUSTOM CODE =-=-=-=-=-=-=
        array(
            'title' => __('Custom Code', 'wp-ultimate-post-grid'),
            'name' => 'custom_code',
            'icon' => 'font-awesome:fa-code',
            'controls' => array(
                array(
                    'type' => 'codeeditor',
                    'name' => 'custom_code_public_css',
                    'label' => __('Public CSS', 'wp-ultimate-post-grid'),
                    'theme' => 'github',
                    'mode' => 'css',
                ),
            ),
        ),
//=-=-=-=-=-=-= FAQ & SUPPORT =-=-=-=-=-=-=
        array(
            'title' => __('FAQ & Support', 'wp-ultimate-post-grid'),
            'name' => 'faq_support',
            'icon' => 'font-awesome:fa-book',
            'controls' => array(
                array(
                    'type' => 'notebox',
                    'name' => 'faq_support_notebox',
                    'label' => __('Need more help?', 'wp-ultimate-post-grid'),
                    'description' => '<a href="mailto:support@bootstrapped.ventures" target="_blank">WP Ultimate Post Grid ' .__('FAQ & Support', 'wp-ultimate-post-grid') . '</a>',
                    'status' => 'info',
                ),
            ),
        ),
    ),
);