<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function add_email_notification_setting( $settings ) {
    $new_settings = array(
        'title' => __( 'Email Notification', 'my-product-manager' ),
        'type'  => 'title',
        'id'    => 'email_notification_settings',
    );

    $settings[] = $new_settings;

    $settings[] = array(
        'name'     => __( 'Enable Email Notification', 'my-product-manager' ),
        'desc'     => __( 'Enable or disable email notification when a product is created or updated by the user.', 'my-product-manager' ),
        'id'       => 'enable_email_notification',
        'type'     => 'checkbox',
        'default'  => 'yes',
    );

    $settings[] = array( 'type' => 'sectionend', 'id' => 'email_notification_settings' );

    return $settings;
}
add_filter( 'woocommerce_get_settings_general', 'add_email_notification_setting' );

function notify_admin_on_product_save( $product_id, $is_update = false ) {
    $product = wc_get_product( $product_id );

    $enable_email_notification = get_option( 'enable_email_notification', 'yes' );

    if ( $enable_email_notification !== 'yes' || ! $product ) {
        return;
    }

    $admin_email = get_option( 'admin_email' );
    $author_id = get_post_field( 'post_author', $product_id );
    $author = get_user_by( 'id', $author_id );
    $author_name = $author ? $author->display_name : 'Unknown Author';
    $product_name = $product->get_name();
    $product_edit_link = admin_url( 'post.php?post=' . $product_id . '&action=edit' );

    $subject = $is_update ? sprintf( 'Product Updated: %s', $product_name ) : sprintf( 'New Product Created: %s', $product_name );
    $message = sprintf( "Product Name: %s\nAuthor: %s\nEdit Product: %s\n", $product_name, $author_name, $product_edit_link );

    wp_mail( $admin_email, $subject, $message );
}

function after_product_save( $post_id, $post, $update ) {
    if ( 'product' !== $post->post_type || wp_is_post_autosave( $post_id ) ) {
        return;
    }
    notify_admin_on_product_save( $post_id, $update );
}
add_action( 'save_post_product', 'after_product_save', 10, 3 );

function filter_user_media_files( $query ) {
    if ( ! is_user_logged_in() ) {
        return;
    }
    $user_id = get_current_user_id();
    $query['author'] = $user_id;
    return $query;
}
add_filter( 'ajax_query_attachments_args', 'filter_user_media_files' );


add_filter('query_vars', function ($vars) {
    $vars[] = 'my-products';
    return $vars;
});





