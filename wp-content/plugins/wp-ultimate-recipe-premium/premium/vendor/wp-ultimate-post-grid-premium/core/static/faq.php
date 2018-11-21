<?php
// Subpage
$sub = isset( $_GET['sub'] ) ? $_GET['sub'] : 'getting_started';

if( !in_array( $sub, array( 'getting_started', 'whats_new', 'support', 'our_plugins' ) ) ) {
    $sub = 'getting_started';
}

// Active version
if( WPUltimatePostGrid::is_premium_active() ) {
    $name = 'WP Ultimate Post Grid Premium';
    $version = WPUPG_PREMIUM_VERSION;
} else {
    $name = 'WP Ultimate Post Grid';
    $version = WPUPG_VERSION;
}
$full_name = $name . ' ' . $version;

// Image directory
$img_dir = WPUltimatePostGrid::get()->coreUrl . '/img/faq/';
?>

<div class="wrap about-wrap">

    <h1><?php echo $name; ?></h1>

    <div class="about-text">Welcome to version <?php echo $version ?> of the very best grid plugin!</div>

    <div class="wpupg-badge">Version <?php echo $version; ?></div>

    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url('edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_faq&sub=getting_started'); ?>" class="nav-tab<?php if( $sub == 'getting_started' ) echo ' nav-tab-active'; ?>">
            Getting Started
        </a><a href="<?php echo admin_url('edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_faq&sub=whats_new'); ?>" class="nav-tab<?php if( $sub == 'whats_new' ) echo ' nav-tab-active'; ?>">
            What&#8217;s New
        </a><a href="<?php echo admin_url('edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_faq&sub=support'); ?>" class="nav-tab<?php if( $sub == 'support' ) echo ' nav-tab-active'; ?>">
            I need help!
        </a><a href="<?php echo admin_url('edit.php?post_type=' . WPUPG_POST_TYPE . '&page=wpupg_faq&sub=our_plugins'); ?>" class="nav-tab<?php if( $sub == 'our_plugins' ) echo ' nav-tab-active'; ?>">
            Our Plugins
        </a>
    </h2>

    <?php include( WPUltimatePostGrid::get()->coreDir . '/static/faq/'.$sub.'.php' ); ?>
</div>