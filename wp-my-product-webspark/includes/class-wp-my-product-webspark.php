<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_My_Product_Webspark {

    public function __construct() {
        add_filter( 'woocommerce_account_menu_items', [ $this, 'add_account_menu_items' ] );
        add_action( 'woocommerce_account_add-product_endpoint', [ $this, 'render_add_product_page' ] );
        add_action( 'woocommerce_account_my-products_endpoint', [ $this, 'render_my_products_page' ] );
        add_action( 'wp_loaded', [ $this, 'process_product_deletion' ] );
        add_action( 'init', [ $this, 'register_endpoints' ] );
        add_action( 'wp_loaded', [ $this, 'process_add_product_form' ] );
    }

    public function add_account_menu_items( $items ) {
        $items['add-product'] = __( 'Add Product', 'product-manager' );
        $items['my-products'] = __( 'My Products', 'product-manager' );
        return $items;
    }

    public function register_endpoints() {
        add_rewrite_endpoint( 'add-product', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'my-products', EP_ROOT | EP_PAGES );
    }

    public function render_add_product_page() {
        if ( is_user_logged_in() ) {
            include plugin_dir_path(__FILE__) . 'templates/add-product-form.php';
        } else {
            echo __( 'Please log in to add a product.', 'product-manager' );
        }
    }

    public function render_my_products_page() {
        if ( is_user_logged_in() ) {
            include plugin_dir_path(__FILE__) . 'templates/my-products.php';
        } else {
            echo __( 'Please log in to view your products.', 'product-manager' );
        }
    }

    public function process_add_product_form() {
        if ( isset( $_POST['add_product_nonce'] ) && wp_verify_nonce( $_POST['add_product_nonce'], 'add_product' ) ) {
            $user_id = get_current_user_id();
            $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
            $title = sanitize_text_field( $_POST['product_title'] );
            $price = floatval( $_POST['product_price'] );
            $quantity = intval( $_POST['product_quantity'] );
            $description = wp_kses_post( $_POST['product_description'] );
            $product_image_url = esc_url_raw( $_POST['product_image_url'] );

            if ( $product_id ) {
                $product = wc_get_product( $product_id );

                if ( $product && $product->get_post_data()->post_author == $user_id ) {
                    $product->set_name( $title );
                    $product->set_regular_price( $price );
                    $product->set_description( $description );
                    $product->set_stock_quantity( $quantity );
                    $product->set_manage_stock( true );
                    $product->set_status( 'pending' );
                    if ( $product_image_url ) {
                        $image_id = attachment_url_to_postid( $product_image_url );
                        if ( $image_id ) {
                            $product->set_image_id( $image_id );
                        }
                    }
                    $product->save();
                    wc_add_notice( __( 'Product updated successfully!', 'product-manager' ), 'success' );
                } else {
                    wc_add_notice( __( 'You do not have permission to edit this product.', 'product-manager' ), 'error' );
                }
            } else {
                $product = new WC_Product_Simple();
                $product->set_name( $title );
                $product->set_regular_price( $price );
                $product->set_description( $description );
                $product->set_stock_quantity( $quantity );
                $product->set_manage_stock( true );
                $product->set_status( 'pending' );
                $product->set_catalog_visibility( 'visible' );
                if ( $product_image_url ) {
                    $image_id = attachment_url_to_postid( $product_image_url );
                    if ( $image_id ) {
                        $product->set_image_id( $image_id );
                    }
                }
                $product_id = $product->save();

                if ( $product_id ) {
                    wc_add_notice( __( 'Product added successfully!', 'product-manager' ), 'success' );
                } else {
                    wc_add_notice( __( 'Failed to add product.', 'product-manager' ), 'error' );
                }
            }

            wp_redirect( wc_get_account_endpoint_url( 'my-products' ) );
            exit;
        }
    }

    public function process_product_deletion() {
        if ( isset( $_POST['delete_product_nonce'] ) && wp_verify_nonce( $_POST['delete_product_nonce'], 'delete_product' ) ) {
            $product_id = intval( $_POST['product_id'] );

            if ( get_post_field( 'post_author', $product_id ) == get_current_user_id() ) {
                wp_trash_post( $product_id );
                wc_add_notice( __( 'Product deleted successfully.', 'product-manager' ), 'success' );
            } else {
                wc_add_notice( __( 'You do not have permission to delete this product.', 'product-manager' ), 'error' );
            }

            wp_redirect( wc_get_account_endpoint_url( 'my-products' ) );
            exit;
        }
    }
}

new WP_My_Product_Webspark();






