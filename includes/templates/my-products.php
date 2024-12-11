<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$current_user_id = get_current_user_id();

$args = [
    'post_type'      => 'product',
    'posts_per_page' => -1,
    'author'         => $current_user_id,
    'post_status'    => [ 'publish', 'pending' ]
];

$products = new WP_Query( $args );

if ( $products->have_posts() ) : ?>
    <table>
        <thead>
        <tr>
            <th><?php _e( 'Product Name', 'product-manager' ); ?></th>
            <th><?php _e( 'Quantity', 'product-manager' ); ?></th>
            <th><?php _e( 'Price', 'product-manager' ); ?></th>
            <th><?php _e( 'Status', 'product-manager' ); ?></th>
            <th><?php _e( 'Edit', 'product-manager' ); ?></th>
            <th><?php _e( 'Delete', 'product-manager' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php while ( $products->have_posts() ) : $products->the_post();
            $product = wc_get_product( get_the_ID() ); ?>
            <tr>
                <td><?php echo esc_html( $product->get_name() ); ?></td>
                <td><?php echo esc_html( $product->get_stock_quantity() ?: __( 'N/A', 'product-manager' ) ); ?></td>
                <td><?php echo wc_price( $product->get_regular_price() ); ?></td>
                <td><?php echo esc_html( ucfirst( $product->get_status() ) ); ?></td>
                <td>
                    <a href="<?php echo esc_url( add_query_arg( [ 'product_id' => $product->get_id() ], wc_get_account_endpoint_url( 'add-product' ) ) ); ?>">
                        <?php _e( 'Edit', 'product-manager' ); ?>
                    </a>
                </td>
                <td>
                    <form method="post" onsubmit="return confirm('<?php _e( 'Are you sure you want to delete this product?', 'product-manager' ); ?>');">
                        <?php wp_nonce_field( 'delete_product', 'delete_product_nonce' ); ?>
                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
                        <button type="submit" name="delete_product"><?php _e( 'Delete', 'product-manager' ); ?></button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

<?php else : ?>
    <p><?php _e( 'No products found.', 'product-manager' ); ?></p>
<?php endif;

wp_reset_postdata();
?>


