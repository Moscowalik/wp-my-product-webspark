<?php
/**
 * Plugin Name: WP My Product Webspark
 * Description: Custom plugin to extend WooCommerce functionality.
 * Version: 1.0
 * Author: My Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function check_woocommerce_active() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="error"><p><strong>My Product Manager</strong> requires WooCommerce to be installed and activated.</p></div>';
        } );
        return false;
    }
    return true;
}

if ( check_woocommerce_active() ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-my-product-webspark.php';
    require_once plugin_dir_path( __FILE__ ) . 'functions.php';
}